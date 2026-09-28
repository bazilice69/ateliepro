<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loja extends Model
{
    use HasFactory;

    protected $table = 'lojas';

    protected $fillable = [
        'chave_licenca',
        'nome_fantasia',
        'cnpj_cpf',
        'email_responsavel',
        'telefone',
        'status',
    ];

    /**
     * Valores possíveis para a coluna status (enum da migration).
     */
    public const STATUS_ATIVO = 'ativo';
    public const STATUS_INADIMPLENTE = 'inadimplente';
    public const STATUS_BLOQUEADO = 'bloqueado';

    /**
     * Uma loja possui vários usuários (dono + funcionários).
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Uma loja tem um histórico de assinaturas.
     */
    public function assinaturas(): HasMany
    {
        return $this->hasMany(Assinatura::class);
    }

    /**
     * Retorna a assinatura mais recente da loja (a "vigente").
     */
    public function assinaturaAtual()
    {
        return $this->hasOne(Assinatura::class)->latestOfMany();
    }

    /**
     * Helper: a loja está com acesso liberado?
     */
    public function estaAtiva(): bool
    {
        return $this->status === self::STATUS_ATIVO;
    }
}
