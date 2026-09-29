<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Registro de acesso (login) — para a área de Atividade/Acessos do super_admin.
 * Não usa a trait BelongsToLoja de propósito: o super_admin precisa ver os
 * acessos de TODAS as lojas.
 */
class AccessLog extends Model
{
    protected $table = 'access_logs';

    protected $fillable = [
        'user_id',
        'loja_id',
        'user_nome',
        'loja_nome',
        'evento',
        'ip',
        'user_agent',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function loja(): BelongsTo
    {
        return $this->belongsTo(Loja::class);
    }
}
