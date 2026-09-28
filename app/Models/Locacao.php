<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Locacao extends Model
{
    use HasFactory;

    // Diz ao Laravel que estes campos são seguros para gravar em massa
    protected $fillable = [
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
}