<?php

namespace App\Services;

use App\Models\Assinatura;
use App\Models\Loja;
use App\Models\Plano;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Serviço de licenciamento do SaaS.
 *
 * Concentra a regra de ouro: quando um pagamento é confirmado, ativa a loja,
 * gera/renova a chave de licença (UUID) e estende o vencimento conforme o plano.
 * A licença pertence à LOJA (não ao computador).
 */
class LicenseService
{
    /**
     * Confirma o pagamento de uma assinatura e ativa a loja.
     *
     * @param  Assinatura  $assinatura  A assinatura pendente vinculada ao pagamento.
     * @param  string|null $transacaoMpId  ID da transação no Mercado Pago (idempotência).
     */
    public function confirmarPagamento(Assinatura $assinatura, ?string $transacaoMpId = null): void
    {
        DB::transaction(function () use ($assinatura, $transacaoMpId) {
            $loja = $assinatura->loja;
            $meses = $this->mesesDoPlano($assinatura->plano);

            // Estende a partir do vencimento atual (se ainda vigente) ou de hoje.
            $base = ($loja->data_vencimento && $loja->data_vencimento->isFuture())
                ? $loja->data_vencimento->copy()
                : now();
            $novoVencimento = $base->addMonths($meses);

            // Marca a assinatura como paga.
            $assinatura->update([
                'status' => Assinatura::STATUS_PAGA,
                'transacao_mp_id' => $transacaoMpId ?? $assinatura->transacao_mp_id,
                'data_vencimento' => $novoVencimento,
            ]);

            // Ativa a loja: status ativo, gera chave se não tiver, define vencimento.
            $loja->forceFill([
                'status' => Loja::STATUS_ATIVO,
                'chave_licenca' => $loja->chave_licenca ?: (string) Str::uuid(),
                'data_vencimento' => $novoVencimento,
                'plano' => $assinatura->plano,
                'valor_mensal' => $assinatura->valor,
            ])->save();
        });
    }

    /**
     * Cria uma assinatura PENDENTE para uma loja a partir de um plano.
     */
    public function criarAssinaturaPendente(Loja $loja, Plano $plano): Assinatura
    {
        return Assinatura::create([
            'loja_id' => $loja->id,
            'plano' => $plano->nome,
            'valor' => $plano->preco,
            'status' => Assinatura::STATUS_PENDENTE,
        ]);
    }

    /**
     * Descobre a duração (meses) a partir do nome do plano, consultando a
     * tabela de planos; fallback por heurística de nome.
     */
    private function mesesDoPlano(string $nomePlano): int
    {
        $plano = Plano::where('nome', $nomePlano)->first();
        if ($plano) {
            return max(1, $plano->meses);
        }

        return match (Str::lower($nomePlano)) {
            'anual' => 12,
            'semestral' => 6,
            'trimestral' => 3,
            default => 1,
        };
    }
}
