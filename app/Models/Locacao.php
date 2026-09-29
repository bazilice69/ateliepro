<?php

namespace App\Models;

use App\Models\Concerns\BelongsToLoja;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Locacao extends Model
{
    use HasFactory, BelongsToLoja;

    // Diz ao Laravel que estes campos são seguros para gravar em massa
    protected $fillable = [
        'loja_id',
        'cliente_id',
        'acervo_id',
        'data_retirada',
        'data_evento',
        'data_devolucao',
        'data_liberacao_prevista',
        'valor_total',
        'sinal_pago',
        'caucao',
        'caucao_status',
        'status',
        'observacoes',
        // Retirada
        'retirada_em', 'retirada_checklist', 'retirada_fotos',
        // Devolução / vistoria
        'devolucao_em', 'vistoria_estado', 'vistoria_observacoes', 'multa', 'devolucao_fotos',
    ];

    protected function casts(): array
    {
        return [
            'data_retirada' => 'date',
            'data_evento' => 'date',
            'data_devolucao' => 'date',
            'data_liberacao_prevista' => 'date',
            'retirada_em' => 'datetime',
            'devolucao_em' => 'datetime',
            'valor_total' => 'decimal:2',
            'sinal_pago' => 'decimal:2',
            'caucao' => 'decimal:2',
            'multa' => 'decimal:2',
            'retirada_fotos' => 'array',
            'devolucao_fotos' => 'array',
        ];
    }

    public const CAUCAO_PENDENTE = 'pendente';
    public const CAUCAO_RETIDA = 'retida';
    public const CAUCAO_DEVOLVIDA = 'devolvida';

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function acervo()
    {
        return $this->belongsTo(Acervo::class);
    }

    public function provas(): HasMany
    {
        return $this->hasMany(Prova::class)->latest('data_prova');
    }

    public function servicos(): HasMany
    {
        return $this->hasMany(Servico::class)->latest();
    }

    /**
     * Saldo a receber (valor total + multa − sinal pago).
     */
    public function saldo(): float
    {
        return max(0, (float) $this->valor_total + (float) $this->multa - (float) $this->sinal_pago);
    }
}
