<?php

namespace App\Services;

use App\Models\DespesaRecorrente;
use App\Models\LancamentoFinanceiro;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * Gera lançamentos financeiros a partir das despesas recorrentes ativas.
 * Idempotente: uma despesa só é lançada uma vez por mês (controle via
 * ultimo_lancamento_em).
 */
class DespesaRecorrenteService
{
    /**
     * Gera as despesas do mês para a loja do usuário logado (botão manual).
     */
    public function gerarParaLojaAtual(?Carbon $referencia = null): int
    {
        $referencia ??= Carbon::now();

        $despesas = DespesaRecorrente::where('ativo', true)->get();

        return $this->gerarParaColecao($despesas, $referencia, Auth::user()?->loja_id);
    }

    /**
     * Gera para TODAS as lojas (usado pelo comando agendado, sem usuário logado).
     */
    public function gerarParaTodas(?Carbon $referencia = null): int
    {
        $referencia ??= Carbon::now();

        // Sem contexto de auth: ignora o global scope de loja.
        $despesas = DespesaRecorrente::withoutGlobalScope('loja')
            ->where('ativo', true)
            ->get();

        return $this->gerarParaColecao($despesas, $referencia, null);
    }

    /**
     * @param  \Illuminate\Support\Collection<int, DespesaRecorrente>  $despesas
     */
    private function gerarParaColecao($despesas, Carbon $referencia, ?int $lojaIdContexto): int
    {
        $ano = $referencia->year;
        $mes = $referencia->month;
        $gerados = 0;

        foreach ($despesas as $despesa) {
            if ($despesa->jaLancadaEm($ano, $mes)) {
                continue;
            }

            $diaVenc = min($despesa->dia_vencimento, $referencia->copy()->endOfMonth()->day);
            $vencimento = Carbon::create($ano, $mes, $diaVenc);

            LancamentoFinanceiro::withoutGlobalScope('loja')->create([
                'loja_id' => $despesa->loja_id,
                'tipo' => 'Despesa',
                'categoria_id' => $despesa->categoria_id,
                'descricao' => $despesa->descricao . ' (' . $referencia->format('m/Y') . ')',
                'valor_original' => $despesa->valor,
                'desconto' => 0,
                'acrescimo' => 0,
                'valor_final' => $despesa->valor,
                'data_vencimento' => $vencimento,
                'status' => 'Pendente',
                'observacoes' => 'Gerado automaticamente da despesa recorrente.',
            ]);

            $despesa->forceFill(['ultimo_lancamento_em' => $referencia->copy()->startOfMonth()])->save();
            $gerados++;
        }

        return $gerados;
    }
}
