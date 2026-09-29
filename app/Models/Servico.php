<?php

namespace App\Models;

use App\Models\Concerns\BelongsToLoja;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Serviço interno sobre uma peça: ajuste/costura, lavanderia ou manutenção.
 */
class Servico extends Model
{
    use HasFactory, BelongsToLoja;

    protected $table = 'servicos';

    protected $fillable = [
        'loja_id', 'acervo_id', 'cliente_id', 'locacao_id',
        'tipo', 'descricao', 'itens', 'responsavel', 'prazo',
        'custo', 'status', 'observacoes', 'fotos',
    ];

    protected function casts(): array
    {
        return [
            'itens' => 'array',
            'fotos' => 'array',
            'prazo' => 'date',
            'custo' => 'decimal:2',
        ];
    }

    public const TIPO_AJUSTE = 'ajuste';
    public const TIPO_LAVANDERIA = 'lavanderia';
    public const TIPO_MANUTENCAO = 'manutencao';

    public const STATUS_SOLICITADO = 'solicitado';
    public const STATUS_EM_ANDAMENTO = 'em_andamento';
    public const STATUS_PRONTO = 'pronto';
    public const STATUS_APROVADO = 'aprovado';
    public const STATUS_CANCELADO = 'cancelado';

    public function acervo(): BelongsTo
    {
        return $this->belongsTo(Acervo::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function locacao(): BelongsTo
    {
        return $this->belongsTo(Locacao::class);
    }

    /**
     * O serviço está finalizado (pronto ou aprovado)?
     */
    public function estaConcluido(): bool
    {
        return in_array($this->status, [self::STATUS_PRONTO, self::STATUS_APROVADO], true);
    }
}
