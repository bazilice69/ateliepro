<?php

namespace App\Models;

use App\Models\Concerns\BelongsToLoja;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assinatura extends Model
{
    use HasFactory, BelongsToLoja;

    protected $table = 'assinaturas';

    protected $fillable = [
        'loja_id',
        'plano',
        'valor',
        'transacao_mp_id',
        'data_vencimento',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'valor' => 'decimal:2',
            'data_vencimento' => 'date',
        ];
    }

    /**
     * Valores possíveis para a coluna status (enum da migration).
     */
    public const STATUS_PENDENTE = 'pendente';
    public const STATUS_PAGA = 'paga';
    public const STATUS_CANCELADA = 'cancelada';

    // A relação loja() e o isolamento por loja vêm da trait BelongsToLoja.
}
