<?php

namespace App\Models;

use App\Models\Concerns\BelongsToLoja;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Contrato gerado. O `conteudo_final` é congelado no momento da criação.
 */
class Contrato extends Model
{
    use HasFactory, BelongsToLoja;

    protected $table = 'contratos';

    protected $fillable = [
        'loja_id',
        'cliente_id',
        'locacao_id',
        'modelo_contrato_id',
        'titulo',
        'conteudo_final',
        'status',
        'data_contrato',
    ];

    protected function casts(): array
    {
        return ['data_contrato' => 'date'];
    }

    public const STATUS_RASCUNHO = 'rascunho';
    public const STATUS_FINALIZADO = 'finalizado';
    public const STATUS_ASSINADO = 'assinado';
    public const STATUS_CANCELADO = 'cancelado';

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function locacao(): BelongsTo
    {
        return $this->belongsTo(Locacao::class);
    }

    public function modelo(): BelongsTo
    {
        return $this->belongsTo(ModeloContrato::class, 'modelo_contrato_id');
    }
}
