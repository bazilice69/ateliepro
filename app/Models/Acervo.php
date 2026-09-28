<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Acervo extends Model
{
    use HasFactory;

    // Liberando a atualização de status da peça
    protected $fillable = [
        'codigo',
        'nome',
        'categoria',
        'tamanho',
        'cor',
        'valor_locacao',
        'status',
        'observacoes'
    ];
}