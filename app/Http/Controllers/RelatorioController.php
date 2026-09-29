<?php

namespace App\Http\Controllers;

use App\Models\Acervo;
use App\Models\Cliente;
use App\Models\LancamentoFinanceiro;
use App\Models\Locacao;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Relatórios da LOJA (escopados por loja via BelongsToLoja).
 * Visão simplificada (resumo) e detalhada (lançamentos), com export CSV (Excel)
 * e versão de impressão (PDF pelo navegador).
 */
class RelatorioController extends Controller
{
    public function index(Request $request)
    {
        $dados = $this->montarDados($request);
        return view('relatorios.index', $dados);
    }

    public function imprimir(Request $request)
    {
        $dados = $this->montarDados($request);
        return view('relatorios.imprimir', $dados);
    }

    public function exportar(Request $request): StreamedResponse
    {
        [$inicio, $fim] = $this->periodo($request);

        $lancamentos = LancamentoFinanceiro::with(['categoria', 'cliente'])
            ->whereBetween('data_vencimento', [$inicio, $fim])
            ->orderBy('data_vencimento')
            ->get();

        $nome = 'relatorio_' . $inicio->format('Y-m-d') . '_a_' . $fim->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($lancamentos) {
            $out = fopen('php://output', 'w');
            // BOM UTF-8 para acentuação correta no Excel.
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Data', 'Tipo', 'Descrição', 'Categoria', 'Cliente', 'Status', 'Valor'], ';');

            foreach ($lancamentos as $l) {
                fputcsv($out, [
                    optional($l->data_vencimento)->format('d/m/Y'),
                    $l->tipo,
                    $l->descricao,
                    $l->categoria->nome ?? '',
                    $l->cliente->nome ?? '',
                    $l->status,
                    number_format((float) $l->valor_final, 2, ',', '.'),
                ], ';');
            }
            fclose($out);
        }, $nome, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * Período a partir dos filtros (padrão: mês atual).
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    private function periodo(Request $request): array
    {
        $inicio = $request->filled('inicio')
            ? Carbon::parse($request->input('inicio'))->startOfDay()
            : Carbon::now()->startOfMonth();

        $fim = $request->filled('fim')
            ? Carbon::parse($request->input('fim'))->endOfDay()
            : Carbon::now()->endOfMonth();

        return [$inicio, $fim];
    }

    /**
     * Monta os dados dos relatórios (resumo + detalhado).
     */
    private function montarDados(Request $request): array
    {
        [$inicio, $fim] = $this->periodo($request);

        $lancamentos = LancamentoFinanceiro::with(['categoria', 'cliente'])
            ->whereBetween('data_vencimento', [$inicio, $fim])
            ->orderBy('data_vencimento')
            ->get();

        // --- FINANCEIRO (resumo) ---
        $receitas = (float) $lancamentos->where('tipo', 'Receita')->sum('valor_final');
        $despesas = (float) $lancamentos->where('tipo', 'Despesa')->sum('valor_final');
        $recebido = (float) $lancamentos->where('tipo', 'Receita')->where('status', 'Pago')->sum('valor_final');

        $hoje = Carbon::now()->startOfDay();
        // A receber = pendentes ainda no prazo. Em atraso = vencidos (status
        // "Vencido" OU pendentes com vencimento já passado).
        $aReceber = (float) $lancamentos->where('tipo', 'Receita')
            ->filter(fn ($l) => $l->status === 'Pendente' && optional($l->data_vencimento)->gte($hoje))
            ->sum('valor_final');
        $emAtraso = (float) $lancamentos->where('tipo', 'Receita')
            ->filter(fn ($l) => $l->status === 'Vencido'
                || ($l->status === 'Pendente' && optional($l->data_vencimento)->lt($hoje)))
            ->sum('valor_final');

        // Receita/Despesa por categoria
        $porCategoria = $lancamentos->groupBy(fn ($l) => $l->categoria->nome ?? 'Sem categoria')
            ->map(fn ($grupo) => [
                'receita' => (float) $grupo->where('tipo', 'Receita')->sum('valor_final'),
                'despesa' => (float) $grupo->where('tipo', 'Despesa')->sum('valor_final'),
            ]);

        // --- OPERACIONAL ---
        $locacoes = Locacao::whereBetween('data_evento', [$inicio, $fim])->get();
        $operacional = [
            'locacoes' => $locacoes->count(),
            'novos_clientes' => Cliente::whereBetween('created_at', [$inicio, $fim])->count(),
        ];

        // --- PEÇAS mais alugadas (no período) ---
        $pecasMaisAlugadas = Locacao::whereBetween('data_evento', [$inicio, $fim])
            ->selectRaw('acervo_id, count(*) as total')
            ->groupBy('acervo_id')
            ->orderByDesc('total')
            ->limit(10)
            ->get()
            ->map(function ($linha) {
                $peca = Acervo::find($linha->acervo_id);
                return [
                    'nome' => $peca->nome ?? '—',
                    'codigo' => $peca->codigo ?? '—',
                    'total' => $linha->total,
                ];
            });

        return [
            'inicio' => $inicio,
            'fim' => $fim,
            'lancamentos' => $lancamentos,
            'financeiro' => compact('receitas', 'despesas', 'recebido', 'aReceber', 'emAtraso') + [
                'saldo' => $receitas - $despesas,
            ],
            'porCategoria' => $porCategoria,
            'operacional' => $operacional,
            'pecasMaisAlugadas' => $pecasMaisAlugadas,
            'loja' => $request->user()?->loja,
        ];
    }
}
