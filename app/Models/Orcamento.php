<?php

namespace App\Models;

use App\Models\Concerns\BelongsToLoja;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Orcamento extends Model
{
    use HasFactory, BelongsToLoja;

    protected $table = 'orcamentos';

    protected $fillable = [
        'loja_id', 'cliente_id', 'acervo_id', 'modo', 'descricao',
        'valor_final',
        'custo_material', 'horas_trabalho', 'valor_hora', 'despesas_fixas',
        'custo_peca', 'num_alugueis', 'custo_manutencao',
        'margem_percent', 'taxa_cartao_percent', 'custo_total', 'lucro_estimado',
        'status', 'contrato_id', 'observacoes',
    ];

    protected function casts(): array
    {
        return [
            'valor_final' => 'decimal:2',
            'custo_material' => 'decimal:2',
            'horas_trabalho' => 'decimal:2',
            'valor_hora' => 'decimal:2',
            'despesas_fixas' => 'decimal:2',
            'custo_peca' => 'decimal:2',
            'custo_manutencao' => 'decimal:2',
            'margem_percent' => 'decimal:2',
            'taxa_cartao_percent' => 'decimal:2',
            'custo_total' => 'decimal:2',
            'lucro_estimado' => 'decimal:2',
        ];
    }

    public const STATUS_RASCUNHO = 'rascunho';
    public const STATUS_ENVIADO = 'enviado';
    public const STATUS_ACEITO = 'aceito';
    public const STATUS_RECUSADO = 'recusado';

    public const MODOS = [
        'sob_medida' => 'Sob Medida',
        'aluguel' => 'Aluguel',
        'ajuste' => 'Ajuste',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function acervo(): BelongsTo
    {
        return $this->belongsTo(Acervo::class);
    }

    public function contrato(): BelongsTo
    {
        return $this->belongsTo(Contrato::class);
    }

    public function modoLabel(): string
    {
        return self::MODOS[$this->modo] ?? $this->modo;
    }
}
