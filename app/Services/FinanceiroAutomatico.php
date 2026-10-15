<?php

namespace App\Services;

use App\Models\CategoriaFinanceira;
use App\Models\LancamentoFinanceiro;
use App\Models\Locacao;

/**
 * Gera lançamentos financeiros AUTOMATICAMENTE a partir das operações da loja
 * (hoje: locações). A ideia é que a lojista não precise digitar o dinheiro duas
 * vezes — ao registrar uma locação/retirada/devolução, o financeiro já reflete.
 *
 * IDEMPOTÊNCIA: cada lançamento automático é identificado por
 * (origem, origem_id, origem_item) e usa updateOrCreate — assim, reprocessar a
 * mesma locação ATUALIZA o lançamento existente em vez de duplicar.
 *
 * Multi-tenant: todos os writes passam loja_id explícito e ignoram o global
 * scope para funcionar também fora de um request autenticado (ex.: comandos).
 */
class FinanceiroAutomatico
{
    public const ORIGEM_LOCACAO = 'locacao';

    public const ITEM_SINAL = 'sinal';
    public const ITEM_SALDO = 'saldo';
    public const ITEM_MULTA = 'multa';
    public const ITEM_CAUCAO = 'caucao';

    /**
     * Sincroniza os lançamentos de RECEITA de uma locação:
     *   - sinal pago  -> Receita "Paga" na categoria "Locação de Trajes"
     *   - saldo a receber -> Receita "Pendente" (vencimento = data de retirada)
     *
     * Chamado ao criar a locação e sempre que valores mudarem.
     */
    public function sincronizarLocacao(Locacao $locacao): void
    {
        $valorTotal = (float) $locacao->valor_total;
        $sinal = (float) $locacao->sinal_pago;
        $saldo = max(0, $valorTotal - $sinal);

        $categoriaLocacao = $this->categoriaId($locacao->loja_id, 'Locação de Trajes', 'Receita');
        $desc = $this->descricaoBase($locacao);

        // 1) SINAL — só existe lançamento se houver sinal pago.
        if ($sinal > 0) {
            $this->registrar($locacao, self::ITEM_SINAL, [
                'tipo' => 'Receita',
                'categoria_id' => $categoriaLocacao,
                'cliente_id' => $locacao->cliente_id,
                'descricao' => "Sinal — {$desc}",
                'valor_original' => $sinal,
                'valor_final' => $sinal,
                'data_vencimento' => $locacao->data_retirada ?? now(),
                'data_pagamento' => $locacao->created_at ?? now(),
                'status' => 'Pago',
            ]);
        } else {
            $this->remover($locacao, self::ITEM_SINAL);
        }

        // 2) SALDO — a receber na retirada. Se zerado, remove eventual pendência.
        if ($saldo > 0) {
            $this->registrar($locacao, self::ITEM_SALDO, [
                'tipo' => 'Receita',
                'categoria_id' => $categoriaLocacao,
                'cliente_id' => $locacao->cliente_id,
                'descricao' => "Saldo da locação — {$desc}",
                'valor_original' => $saldo,
                'valor_final' => $saldo,
                'data_vencimento' => $locacao->data_retirada ?? now(),
                'status' => 'Pendente',
            ]);
        } else {
            $this->remover($locacao, self::ITEM_SALDO);
        }
    }

    /**
     * Marca o SALDO da locação como recebido (chamado na retirada, quando o
     * cliente normalmente quita o restante e leva a peça). Se não houver
     * lançamento de saldo pendente, não faz nada.
     */
    public function quitarSaldoLocacao(Locacao $locacao): void
    {
        LancamentoFinanceiro::withoutGlobalScope('loja')
            ->where('origem', self::ORIGEM_LOCACAO)
            ->where('origem_id', $locacao->id)
            ->where('origem_item', self::ITEM_SALDO)
            ->where('status', '!=', 'Pago')
            ->update([
                'status' => 'Pago',
                'data_pagamento' => now(),
            ]);
    }

    /**
     * Lança as receitas que só surgem na DEVOLUÇÃO/vistoria:
     *   - multa (dano/atraso) -> categoria "Multas e Juros"
     *   - caução retida       -> categoria "Retenção de Caução"
     */
    public function registrarDevolucao(Locacao $locacao): void
    {
        $desc = $this->descricaoBase($locacao);
        $multa = (float) $locacao->multa;

        if ($multa > 0) {
            $this->registrar($locacao, self::ITEM_MULTA, [
                'tipo' => 'Receita',
                'categoria_id' => $this->categoriaId($locacao->loja_id, 'Multas e Juros', 'Receita'),
                'cliente_id' => $locacao->cliente_id,
                'descricao' => "Multa/avaria — {$desc}",
                'valor_original' => $multa,
                'valor_final' => $multa,
                'data_vencimento' => now(),
                'data_pagamento' => now(),
                'status' => 'Pago',
            ]);
        } else {
            $this->remover($locacao, self::ITEM_MULTA);
        }

        // Caução retida vira receita da loja; devolvida não gera lançamento.
        if ($locacao->caucao_status === Locacao::CAUCAO_RETIDA && (float) $locacao->caucao > 0) {
            $this->registrar($locacao, self::ITEM_CAUCAO, [
                'tipo' => 'Receita',
                'categoria_id' => $this->categoriaId($locacao->loja_id, 'Retenção de Caução', 'Receita'),
                'cliente_id' => $locacao->cliente_id,
                'descricao' => "Caução retida — {$desc}",
                'valor_original' => (float) $locacao->caucao,
                'valor_final' => (float) $locacao->caucao,
                'data_vencimento' => now(),
                'data_pagamento' => now(),
                'status' => 'Pago',
            ]);
        } else {
            $this->remover($locacao, self::ITEM_CAUCAO);
        }
    }

    /**
     * Grava (ou atualiza) um lançamento automático de forma idempotente.
     *
     * @param  array<string, mixed>  $dados
     */
    private function registrar(Locacao $locacao, string $item, array $dados): void
    {
        // Sem categoria compatível na loja não há como lançar — aborta em
        // silêncio para nunca quebrar o fluxo operacional da locação.
        if (empty($dados['categoria_id'])) {
            return;
        }

        LancamentoFinanceiro::withoutGlobalScope('loja')->updateOrCreate(
            [
                'origem' => self::ORIGEM_LOCACAO,
                'origem_id' => $locacao->id,
                'origem_item' => $item,
            ],
            array_merge([
                'loja_id' => $locacao->loja_id,
                'desconto' => 0,
                'acrescimo' => 0,
                'observacoes' => 'Gerado automaticamente a partir da locação #' . $locacao->id . '.',
            ], $dados),
        );
    }

    /**
     * Remove um lançamento automático que não se aplica mais (ex.: sinal zerado).
     */
    private function remover(Locacao $locacao, string $item): void
    {
        LancamentoFinanceiro::withoutGlobalScope('loja')
            ->where('origem', self::ORIGEM_LOCACAO)
            ->where('origem_id', $locacao->id)
            ->where('origem_item', $item)
            ->delete();
    }

    /**
     * Resolve o id da categoria pelo nome (dentro da loja). Se não existir,
     * cria — assim lojas antigas sem a categoria não ficam sem lançamento.
     */
    private function categoriaId(int $lojaId, string $nome, string $tipo): ?int
    {
        return CategoriaFinanceira::withoutGlobalScope('loja')
            ->firstOrCreate(
                ['loja_id' => $lojaId, 'nome' => $nome, 'tipo' => $tipo],
            )->id;
    }

    private function descricaoBase(Locacao $locacao): string
    {
        $peca = $locacao->acervo?->nome ?? 'Peça';
        $cliente = $locacao->cliente?->nome ?? 'Cliente';

        return "{$peca} ({$cliente})";
    }
}
