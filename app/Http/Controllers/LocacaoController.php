<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\Locacao;
use App\Models\Cliente;
use App\Models\Acervo;
use Carbon\Carbon;

class LocacaoController extends Controller
{
    // Lista de locações (escopada por loja pela trait)
    public function index()
    {
        $locacoes = Locacao::with(['cliente', 'acervo'])->latest()->get();
        return view('locacoes.index', compact('locacoes'));
    }

    // Ficha da locação = CENTRAL DE OPERAÇÃO (provas, ajustes, retirada, devolução)
    public function show(Locacao $locacao)
    {
        $locacao->load(['cliente', 'acervo', 'provas.acervo', 'servicos']);
        return view('locacoes.show', compact('locacao'));
    }

    // Abre a tela de nova locação
    public function create()
    {
        $clientes = Cliente::orderBy('nome')->get();
        $acervos = Acervo::where('status', '!=', Acervo::STATUS_INDISPONIVEL)->get();

        return view('locacoes.create', compact('clientes', 'acervos'));
    }

    // Recebe os dados, verifica a regra de ouro e salva
    public function store(Request $request)
    {
        $lojaId = Auth::user()->loja_id;

        $request->validate([
            'cliente_id' => ['required', Rule::exists('clientes', 'id')->where('loja_id', $lojaId)],
            'acervo_id' => ['required', Rule::exists('acervos', 'id')->where('loja_id', $lojaId)],
            'data_retirada' => 'required|date',
            'data_evento' => 'required|date|after_or_equal:data_retirada',
            'data_devolucao' => 'required|date|after_or_equal:data_evento',
            'valor_total' => 'required|numeric',
            'sinal_pago' => 'nullable|numeric',
            'caucao' => 'nullable|numeric',
        ]);

        $dataRetirada = Carbon::parse($request->data_retirada);
        $dataDevolucao = Carbon::parse($request->data_devolucao);
        $dataLiberacao = $dataDevolucao->copy()->addDays(3); // +3 dias de lavanderia

        // Trava de conflito de datas para a mesma peça.
        $conflito = Locacao::where('acervo_id', $request->acervo_id)
            ->where('status', '!=', 'cancelada')
            ->where(function ($query) use ($dataRetirada, $dataLiberacao) {
                $query->where('data_retirada', '<=', $dataLiberacao->format('Y-m-d'))
                      ->where('data_liberacao_prevista', '>=', $dataRetirada->format('Y-m-d'));
            })
            ->exists();

        if ($conflito) {
            return back()->withErrors(['conflito' => '🚨 PEÇA INDISPONÍVEL! Este vestido/traje já possui uma reserva ou período de lavanderia que conflita com estas datas.'])->withInput();
        }

        // Caução: usa a informada, ou o valor sugerido da peça.
        $peca = Acervo::find($request->acervo_id);
        $caucao = $request->filled('caucao') ? (float) $request->caucao : (float) ($peca->caucao ?? 0);

        $locacao = Locacao::create([
            'cliente_id' => $request->cliente_id,
            'acervo_id' => $request->acervo_id,
            'data_retirada' => $dataRetirada,
            'data_evento' => $request->data_evento,
            'data_devolucao' => $dataDevolucao,
            'data_liberacao_prevista' => $dataLiberacao,
            'valor_total' => $request->valor_total,
            'sinal_pago' => $request->sinal_pago ?? 0,
            'caucao' => $caucao,
            'status' => 'reservada',
            'observacoes' => $request->observacoes,
        ]);

        if ($peca && $peca->status === Acervo::STATUS_DISPONIVEL) {
            $peca->update(['status' => Acervo::STATUS_RESERVADA]);
        }

        return redirect()->route('locacao.show', $locacao)->with('success', 'Locação registrada! Agora acompanhe provas, ajustes e retirada por aqui.');
    }

    // Atualiza o status da locação (fluxo)
    public function updateStatus(Request $request, Locacao $locacao)
    {
        $request->validate([
            'status' => ['required', 'string', 'max:40'],
        ]);
        $locacao->update(['status' => $request->status]);

        return back()->with('success', 'Status da locação atualizado.');
    }

    // Registra a RETIRADA (com checklist e fotos)
    public function retirar(Request $request, Locacao $locacao)
    {
        $request->validate([
            'retirada_checklist' => ['nullable', 'string'],
            'fotos.*' => ['nullable', 'image', 'max:5120'],
        ]);

        $fotos = $this->salvarFotos($request, "locacoes/{$locacao->id}/retirada");

        $locacao->update([
            'retirada_em' => now(),
            'retirada_checklist' => $request->retirada_checklist,
            'retirada_fotos' => $fotos ?: $locacao->retirada_fotos,
            'status' => 'retirada',
        ]);

        // Peça passa a "alugada".
        $locacao->acervo?->update(['status' => Acervo::STATUS_ALUGADA]);

        return back()->with('success', 'Retirada registrada com sucesso!');
    }

    // Registra a DEVOLUÇÃO com vistoria (estado, multa, caução, fotos)
    public function devolver(Request $request, Locacao $locacao)
    {
        $request->validate([
            'vistoria_estado' => ['required', 'string', 'max:40'],
            'vistoria_observacoes' => ['nullable', 'string'],
            'multa' => ['nullable', 'numeric', 'min:0'],
            'caucao_status' => ['required', Rule::in([Locacao::CAUCAO_DEVOLVIDA, Locacao::CAUCAO_RETIDA])],
            'fotos.*' => ['nullable', 'image', 'max:5120'],
        ]);

        $fotos = $this->salvarFotos($request, "locacoes/{$locacao->id}/devolucao");

        // Se a peça tem dano/mancha, vai pra lavanderia/manutenção; senão, disponível.
        $estado = $request->vistoria_estado;
        $novoStatusPeca = in_array($estado, ['dano', 'rasgo'], true)
            ? Acervo::STATUS_MANUTENCAO
            : ($estado === 'mancha' ? Acervo::STATUS_LAVANDERIA : Acervo::STATUS_DISPONIVEL);

        $locacao->update([
            'devolucao_em' => now(),
            'vistoria_estado' => $estado,
            'vistoria_observacoes' => $request->vistoria_observacoes,
            'multa' => $request->multa ?? 0,
            'caucao_status' => $request->caucao_status,
            'devolucao_fotos' => $fotos ?: $locacao->devolucao_fotos,
            'status' => 'aguardando_avaliacao',
        ]);

        $locacao->acervo?->update(['status' => $novoStatusPeca]);

        return back()->with('success', 'Devolução e vistoria registradas!');
    }

    /**
     * Salva as fotos enviadas e retorna os caminhos (array) ou null.
     */
    private function salvarFotos(Request $request, string $pasta): ?array
    {
        if (!$request->hasFile('fotos')) {
            return null;
        }
        $caminhos = [];
        foreach ($request->file('fotos') as $foto) {
            $caminhos[] = $foto->store($pasta, 'public');
        }
        return $caminhos;
    }
}
