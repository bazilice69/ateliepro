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

        // Loja "nova/vazia": usada para mostrar o onboarding de primeiros passos.
        $lojaVazia = $totais['clientes'] === 0 && $totais['pecas'] === 0 && $totais['locacoes_ativas'] === 0;

        // Tem dados de demonstração carregados? (para oferecer o "limpar")
        // Blindagem: só consulta se a migration da coluna já rodou — evita 500
        // caso o deploy aplique as views antes do migrate.
        $temDemo = \Illuminate\Support\Facades\Schema::hasColumn('clientes', 'is_demo')
            && \App\Models\Cliente::where('is_demo', true)->exists();

        // --- Painel operacional: "o que precisa de atenção" (contadores) ------
        $op = [
            // Provas agendadas de hoje em diante
            'provas' => Prova::where('status', Prova::STATUS_AGENDADA)
                ->whereDate('data_prova', '>=', today())->count(),

            // Locações em aberto (não canceladas e ainda não devolvidas)
            'locacoes_abertas' => Locacao::where('status', '!=', 'cancelada')
                ->whereNull('devolucao_em')->count(),

            // Para retirar: pronta/combinada e ainda não retirada
            'para_retirar' => Locacao::where('status', '!=', 'cancelada')
                ->whereNull('retirada_em')
                ->where(function ($q) {
                    $q->where('status', 'pronta_para_retirada')
                      ->orWhereNotNull('data_retirada');
                })->count(),

            // Para devolução: já retirada, ainda não devolvida
            'para_devolucao' => Locacao::whereNotNull('retirada_em')
                ->whereNull('devolucao_em')
                ->where('status', '!=', 'cancelada')->count(),

            // Encomendas (sob medida) em produção
            'em_producao' => Encomenda::whereNotIn('etapa', [Encomenda::ETAPA_ENTREGUE, Encomenda::ETAPA_CANCELADA])->count(),

            // Oficina por tipo (fila de trabalho pendente). Conta as DUAS fontes
            // sem duplicar: uma ordem de serviço ABERTA na oficina OU uma peça
            // marcada diretamente com aquele status no acervo. Assim o que a
            // lojista vê no Acervo bate com o contador do painel.
            'ajustes' => $this->contarOficina(Servico::TIPO_AJUSTE, Acervo::STATUS_EM_AJUSTE),
            'lavanderia' => $this->contarOficina(Servico::TIPO_LAVANDERIA, Acervo::STATUS_LAVANDERIA),
            'manutencao' => $this->contarOficina(Servico::TIPO_MANUTENCAO, Acervo::STATUS_MANUTENCAO),

            // Encomendas/locações atrasadas (alerta vermelho)
            'entregas_atrasadas' => Encomenda::whereNotIn('etapa', [Encomenda::ETAPA_ENTREGUE, Encomenda::ETAPA_CANCELADA])
                ->whereNotNull('data_entrega')
                ->whereDate('data_entrega', '<', today())->count(),
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
            'totais', 'op', 'movimentacoes', 'proximosEventos', 'agendaHoje',
            'provasHoje', 'ajustesPendentes', 'encomendasProducao', 'lojaVazia', 'temDemo'
        ));
    }

    /**
     * Conta, SEM duplicar, quantas peças estão naquela etapa da oficina —
     * considerando as duas fontes do sistema:
     *   (1) uma ordem de serviço ABERTA na oficina (tipo informado);
     *   (2) uma peça do acervo marcada diretamente com aquele status.
     *
     * Assim o painel reflete tanto a fila da oficina quanto o status manual
     * que a lojista define no card da peça (Acervo).
     */
    private function contarOficina(string $tipoServico, string $statusAcervo): int
    {
        // (1) IDs de peças com serviço aberto desse tipo.
        $pecasComServico = Servico::where('tipo', $tipoServico)
            ->whereIn('status', [Servico::STATUS_SOLICITADO, Servico::STATUS_EM_ANDAMENTO])
            ->pluck('acervo_id')
            ->filter()
            ->unique();

        // (2) IDs de peças marcadas com esse status direto no acervo.
        $pecasComStatus = Acervo::where('status', $statusAcervo)->pluck('id');

        // União sem duplicar.
        return $pecasComServico->merge($pecasComStatus)->unique()->count();
    }
}
