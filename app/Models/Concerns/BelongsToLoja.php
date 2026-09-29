<?php

namespace App\Models\Concerns;

use App\Models\Loja;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * Trait de multi-tenancy (isolamento por loja).
 *
 * Aplique em qualquer model de negócio que deva pertencer a uma loja. Ela:
 *
 *  1. Adiciona um GLOBAL SCOPE que filtra AUTOMATICAMENTE todas as consultas
 *     pela loja do usuário autenticado. Isso garante o isolamento no BACKEND —
 *     mesmo que alguém tente acessar o ID de outra loja pela URL, o registro
 *     simplesmente "não existe" para aquele usuário (findOrFail => 404).
 *
 *  2. Preenche `loja_id` automaticamente ao CRIAR um registro, usando a loja do
 *     usuário logado. Assim os controllers não precisam (e não devem) confiar em
 *     um `loja_id` vindo do formulário.
 *
 * O super_admin (dono do SaaS) é ISENTO do escopo: enxerga todas as lojas.
 * Requisições sem usuário autenticado (CLI, seeders, filas) também não aplicam o
 * filtro — nesses contextos o `loja_id` deve ser informado explicitamente.
 */
trait BelongsToLoja
{
    public static function bootBelongsToLoja(): void
    {
        // 1) Filtro automático de leitura por loja.
        static::addGlobalScope('loja', function (Builder $builder) {
            $lojaId = static::lojaIdDoContexto();

            if ($lojaId !== null) {
                $builder->where($builder->getModel()->getTable() . '.loja_id', $lojaId);
            }
        });

        // 2) Preenchimento automático de loja_id ao criar.
        static::creating(function ($model) {
            if (empty($model->loja_id)) {
                $lojaId = static::lojaIdDoContexto();
                if ($lojaId !== null) {
                    $model->loja_id = $lojaId;
                }
            }
        });
    }

    /**
     * Retorna o loja_id que deve ser aplicado no contexto atual, ou null quando
     * o escopo NÃO deve ser aplicado (sem auth, ou super_admin).
     */
    protected static function lojaIdDoContexto(): ?int
    {
        $user = Auth::user();

        if ($user === null) {
            return null; // CLI / seeder / fila: sem tenant implícito.
        }

        // Super admin enxerga tudo — não filtra por loja.
        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return null;
        }

        return $user->loja_id ? (int) $user->loja_id : null;
    }

    /**
     * Relação com a loja dona do registro.
     */
    public function loja(): BelongsTo
    {
        return $this->belongsTo(Loja::class);
    }

    /**
     * Escopo auxiliar para consultar explicitamente por uma loja (útil para o
     * super_admin, seeders e comandos): Model::daLoja($id)->get().
     */
    public function scopeDaLoja(Builder $query, int $lojaId): Builder
    {
        return $query->withoutGlobalScope('loja')->where($this->getTable() . '.loja_id', $lojaId);
    }

    /**
     * Escopo para o super_admin/CLI enxergar todas as lojas explicitamente.
     */
    public function scopeTodasAsLojas(Builder $query): Builder
    {
        return $query->withoutGlobalScope('loja');
    }
}
