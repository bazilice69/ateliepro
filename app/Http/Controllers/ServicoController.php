<?php

namespace App\Http\Controllers;

use App\Models\Acervo;
use App\Models\Cliente;
use App\Models\Locacao;
use App\Models\Servico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Fila da OFICINA: ajustes/costura, lavanderia e manutenção (escopado por loja).
 */
class ServicoController extends Controller
{
    public function index(Request $request)
    {
        $tipo = $request->query('tipo'); // ajuste | lavanderia | manutencao | null(todos)

        $query = Servico::with(['acervo', 'cliente']);
        if ($tipo) {
            $query->where('tipo', $tipo);
        }

        // Pendentes primeiro, depois por prazo.
        $servicos = $query
            ->orderByRaw("CASE WHEN status IN ('solicitado','em_andamento') THEN 0 ELSE 1 END")
            ->orderBy('prazo')
            ->get();

        return view('servicos.index', compact('servicos', 'tipo'));
    }

    public function create(Request $request)
    {
        $clientes = Cliente::orderBy('nome')->get();
        $acervos = Acervo::orderBy('nome')->get();
        $tipo = $request->query('tipo', Servico::TIPO_AJUSTE);
        $locacaoId = $request->query('locacao_id');
        $acervoId = $request->query('acervo_id');
        $clienteId = $request->query('cliente_id');

        return view('servicos.create', compact('clientes', 'acervos', 'tipo', 'locacaoId', 'acervoId', 'clienteId'));
    }

    public function store(Request $request)
    {
        $lojaId = Auth::user()->loja_id;

        $dados = $request->validate([
            'tipo' => ['required', Rule::in([Servico::TIPO_AJUSTE, Servico::TIPO_LAVANDERIA, Servico::TIPO_MANUTENCAO])],
            'descricao' => ['required', 'string', 'max:200'],
            'acervo_id' => ['nullable', Rule::exists('acervos', 'id')->where('loja_id', $lojaId)],
            'cliente_id' => ['nullable', Rule::exists('clientes', 'id')->where('loja_id', $lojaId)],
            'locacao_id' => ['nullable', Rule::exists('locacaos', 'id')->where('loja_id', $lojaId)],
            'responsavel' => ['nullable', 'string', 'max:100'],
            'prazo' => ['nullable', 'date'],
            'custo' => ['nullable', 'numeric', 'min:0'],
            'itens' => ['nullable', 'string'], // texto: um item por linha
            'observacoes' => ['nullable', 'string'],
        ]);

        // itens (textarea) -> array
        $dados['itens'] = collect(explode("\n", (string) $request->input('itens')))
            ->map(fn ($l) => trim($l))->filter()->values()->all();
        $dados['status'] = Servico::STATUS_SOLICITADO;

        $servico = Servico::create($dados);

        // Reflete o status na peça.
        if ($servico->acervo_id) {
            $statusPeca = $servico->tipo === Servico::TIPO_AJUSTE ? Acervo::STATUS_EM_AJUSTE
                : ($servico->tipo === Servico::TIPO_LAVANDERIA ? Acervo::STATUS_LAVANDERIA : Acervo::STATUS_MANUTENCAO);
            Acervo::where('id', $servico->acervo_id)->update(['status' => $statusPeca]);
        }

        return redirect()->to($this->voltarPara($servico))->with('success', 'Serviço aberto na fila da oficina!');
    }

    public function updateStatus(Request $request, Servico $servico)
    {
        $request->validate([
            'status' => ['required', Rule::in([
                Servico::STATUS_SOLICITADO, Servico::STATUS_EM_ANDAMENTO,
                Servico::STATUS_PRONTO, Servico::STATUS_APROVADO, Servico::STATUS_CANCELADO,
            ])],
        ]);

        $servico->update(['status' => $request->status]);

        // Se concluiu e há peça, ela volta a ficar disponível.
        if ($servico->estaConcluido() && $servico->acervo_id) {
            Acervo::where('id', $servico->acervo_id)->update(['status' => Acervo::STATUS_DISPONIVEL]);
        }

        return back()->with('success', 'Status do serviço atualizado.');
    }

    public function destroy(Servico $servico)
    {
        $servico->delete();
        return back()->with('success', 'Serviço removido.');
    }

    private function voltarPara(Servico $servico): string
    {
        return $servico->locacao_id
            ? route('locacao.show', $servico->locacao_id)
            : route('servicos.index');
    }
}
