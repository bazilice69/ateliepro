<?php

namespace App\Models;

use App\Models\Concerns\BelongsToLoja;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Despesa recorrente/fixa da loja (isolada por loja via BelongsToLoja).
 */
class DespesaRecorrente extends Model
{
    use HasFactory, BelongsToLoja;

    protected $table = 'despesas_recorrentes';

    protected $fillable = [
        'loja_id',
        'categoria_id',
        'descricao',
        'valor',
        'dia_vencimento',
        'ativo',
        'ultimo_lancamento_em',
    ];

    protected function casts(): array
    {
        return [
            'valor' => 'decimal:2',
            'dia_vencimento' => 'integer',
            'ativo' => 'boolean',
            'ultimo_lancamento_em' => 'date',
        ];
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriaFinanceira::class, 'categoria_id');
    }

    /**
     * Já foi lançada no mês/ano informado?
     */
    public function jaLancadaEm(int $ano, int $mes): bool
    {
        return $this->ultimo_lancamento_em
            && $this->ultimo_lancamento_em->year === $ano
            && $this->ultimo_lancamento_em->month === $mes;
    }
}
