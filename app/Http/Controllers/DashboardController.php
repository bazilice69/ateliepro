<?php

namespace App\Http\Controllers;

use App\Models\Acervo;
use App\Models\Agendamento;
use App\Models\Cliente;
use App\Models\Evento;
use App\Models\LancamentoFinanceiro;
use App\Models\Encomenda;
use App\Models\Locacao;
use App\Models\Prova;
use App\Models\Servico;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $inicioMes = Carbon::now()->startOfMonth();
        $fimMes = Carbon::now()->endOfMonth();

        // Todos os totais já são escopados pela loja (trait BelongsToLoja).
        $recebidoMes = (float) LancamentoFinanceiro::where('tipo', 'Receita')
            ->where('status', 'Pago')
            ->whereBetween('data_vencimento', [$inicioMes, $fimMes])
            ->sum('valor_final');

        $aReceber = (float) LancamentoFinanceiro::where('tipo', 'Receita')
            ->where('status', 'Pendente')
            ->sum('valor_final');

        $emAtraso = (float) LancamentoFinanceiro::where('tipo', 'Receita')
            ->where('status', 'Vencido')
            ->sum('valor_final');

        $despesasMes = (float) LancamentoFinanceiro::where('tipo', 'Despesa')
            ->where('status', 'Pago')
            ->whereBetween('data_vencimento', [$inicioMes, $fimMes])
            ->sum('valor_final');

        $saldoPrevisto = $recebidoMes + $aReceber - $despesasMes;

        $totais = [
            'clientes' => Cliente::count(),
            'pecas' => Acervo::count(),
            'locacoes_ativas' => Locacao::where('status', '!=', 'cancelada')->count(),
        ];

        // Movimentações recentes reais
        $movimentacoes = LancamentoFinanceiro::with(['categoria', 'cliente'])
            ->orderByDesc('data_vencimento')
            ->limit(8)
            ->get();

        // Próximos eventos (com contagem regressiva)
        $proximosEventos = Evento::where('status', 'Ativo')
            ->whereDate('data_evento', '>=', now())
            ->orderBy('data_evento')
            ->limit(5)
            ->get();

        // Agenda de hoje
        $agendaHoje = Agendamento::with(['cliente'])
            ->whereDate('data_hora', today())
            ->orderBy('data_hora')
            ->get();

        // Provas de hoje (do módulo operacional)
        $provasHoje = Prova::with(['cliente', 'acervo'])
            ->whereDate('data_prova', today())
            ->orderBy('data_prova')
            ->get();

        // Ajustes/serviços pendentes na oficina
        $ajustesPendentes = Servico::with(['acervo'])
            ->whereIn('status', [Servico::STATUS_SOLICITADO, Servico::STATUS_EM_ANDAMENTO])
            ->orderBy('prazo')
            ->limit(5)
            ->get();

        // Encomendas (sob medida) em produção, priorizando entregas mais próximas
        $encomendasProducao = Encomenda::with(['cliente'])
            ->whereNotIn('etapa', [Encomenda::ETAPA_ENTREGUE, Encomenda::ETAPA_CANCELADA])
            ->orderByRaw('data_entrega IS NULL')
            ->orderBy('data_entrega')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'recebidoMes', 'aReceber', 'emAtraso', 'despesasMes', 'saldoPrevisto',
            'totais', 'movimentacoes', 'proximosEventos', 'agendaHoje',
            'provasHoje', 'ajustesPendentes', 'encomendasProducao'
        ));
    }
}
