<?php

namespace App\Models;

use App\Models\Concerns\BelongsToLoja;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Prova de um cliente (registro do atendimento de prova).
 */
class Prova extends Model
{
    use HasFactory, BelongsToLoja;

    protected $table = 'provas';

    protected $fillable = [
        'loja_id', 'cliente_id', 'acervo_id', 'locacao_id',
        'data_prova', 'responsavel', 'status', 'resultado',
        'ajustes_necessarios', 'proxima_prova', 'observacoes', 'fotos',
    ];

    protected function casts(): array
    {
        return [
            'data_prova' => 'datetime',
            'proxima_prova' => 'date',
            'fotos' => 'array',
        ];
    }

    public const STATUS_AGENDADA = 'agendada';
    public const STATUS_REALIZADA = 'realizada';
    public const STATUS_REMARCADA = 'remarcada';
    public const STATUS_CANCELADA = 'cancelada';

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function acervo(): BelongsTo
    {
        return $this->belongsTo(Acervo::class);
    }

    public function locacao(): BelongsTo
    {
        return $this->belongsTo(Locacao::class);
    }
}
