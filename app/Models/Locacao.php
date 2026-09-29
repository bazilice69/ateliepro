<?php

namespace App\Models;

use App\Models\Concerns\BelongsToLoja;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'status',
        'observacoes'
    ];

    protected function casts(): array
    {
        return [
            'data_retirada' => 'date',
            'data_evento' => 'date',
            'data_devolucao' => 'date',
            'data_liberacao_prevista' => 'date',
            'valor_total' => 'decimal:2',
            'sinal_pago' => 'decimal:2',
        ];
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function acervo()
    {
        return $this->belongsTo(Acervo::class);
    }
}