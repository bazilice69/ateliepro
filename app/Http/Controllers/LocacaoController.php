<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\Locacao;
use App\Models\Cliente;
use App\Models\Acervo;
use Carbon\Carbon;

class LocacaoController extends Controller
{
    // 1. Abre a tela de nova locação
    public function create()
    {
        // Busca os clientes e as peças ativas no banco para preencher as opções
        $clientes = Cliente::orderBy('nome')->get();
        $acervos = Acervo::where('status', '!=', 'indisponivel')->get();

        return view('locacoes.create', compact('clientes', 'acervos'));
    }

    // 2. Recebe os dados, verifica a regra de ouro e salva
    public function store(Request $request)
    {
        $lojaId = Auth::user()->loja_id;

        $request->validate([
            // exists escopado por loja: impede referenciar cliente/peça de OUTRA loja.
            'cliente_id' => ['required', Rule::exists('clientes', 'id')->where('loja_id', $lojaId)],
            'acervo_id' => ['required', Rule::exists('acervos', 'id')->where('loja_id', $lojaId)],
            'data_retirada' => 'required|date',
            'data_evento' => 'required|date|after_or_equal:data_retirada',
            'data_devolucao' => 'required|date|after_or_equal:data_evento',
            'valor_total' => 'required|numeric',
            'sinal_pago' => 'nullable|numeric'
        ]);

        // A REGRA DE OURO: Calculando o Período de Indisponibilidade real
        $dataRetirada = Carbon::parse($request->data_retirada);
        $dataDevolucao = Carbon::parse($request->data_devolucao);
        
        // Adicionamos automaticamente 3 dias de lavanderia/inspeção após a devolução
        $dataLiberacao = $dataDevolucao->copy()->addDays(3);

        // TRAVA DE SEGURANÇA: Busca se existe locação onde as datas "batem de frente"
        $conflito = Locacao::where('acervo_id', $request->acervo_id)
            ->where('status', '!=', 'cancelada') // Ignora as que foram canceladas
            ->where(function($query) use ($dataRetirada, $dataLiberacao) {
                // A matemática do bloqueio: (Retirada 1 <= Liberação 2) E (Liberação 1 >= Retirada 2)
                $query->where('data_retirada', '<=', $dataLiberacao->format('Y-m-d'))
                      ->where('data_liberacao_prevista', '>=', $dataRetirada->format('Y-m-d'));
            })
            ->exists();

        // Se achou conflito, barra o processo e devolve o erro para a tela
        if ($conflito) {
            return back()->withErrors(['conflito' => '🚨 PEÇA INDISPONÍVEL! Este vestido/traje já possui uma reserva ou período de lavanderia que conflita com estas datas.'])->withInput();
        }

        // Se passou direto, salva a locação no banco!
        Locacao::create([
            'cliente_id' => $request->cliente_id,
            'acervo_id' => $request->acervo_id,
            'data_retirada' => $dataRetirada,
            'data_evento' => $request->data_evento,
            'data_devolucao' => $dataDevolucao,
            'data_liberacao_prevista' => $dataLiberacao,
            'valor_total' => $request->valor_total,
            'sinal_pago' => $request->sinal_pago ?? 0,
            'status' => 'reservada',
            'observacoes' => $request->observacoes
        ]);

        // Atualiza o status do vestido no estoque
        $peca = Acervo::find($request->acervo_id);
        if ($peca->status == 'disponivel') {
            $peca->update(['status' => 'reservada']);
        }

        return redirect()->route('dashboard')->with('success', 'Locação registrada com sucesso! Datas bloqueadas.');
    }
}