<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Plano de assinatura do SaaS (gerenciado pelo super_admin).
 * Não usa multi-tenancy: planos são globais para todas as lojas.
 */
class Plano extends Model
{
    use HasFactory;

    protected $table = 'planos';

    protected $fillable = [
        'nome',
        'slug',
        'preco',
        'meses',
        'periodo_label',
        'recursos',
        'destaque',
        'ativo',
        'ordem',
    ];

    protected function casts(): array
    {
        return [
            'preco' => 'decimal:2',
            'meses' => 'integer',
            'recursos' => 'array',
            'destaque' => 'boolean',
            'ativo' => 'boolean',
            'ordem' => 'integer',
        ];
    }

    /**
     * Apenas planos ativos, na ordem definida.
     */
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true)->orderBy('ordem');
    }

    /**
     * Preço mensal equivalente (para comparar planos de períodos diferentes).
     */
    public function precoMensalEquivalente(): float
    {
        return $this->meses > 0 ? (float) $this->preco / $this->meses : (float) $this->preco;
    }
}
