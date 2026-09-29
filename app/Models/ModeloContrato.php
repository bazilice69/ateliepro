<?php

namespace App\Models;

use App\Models\Concerns\BelongsToLoja;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo de contrato da loja (texto com variáveis {{ }}).
 */
class ModeloContrato extends Model
{
    use HasFactory, BelongsToLoja;

    protected $table = 'modelos_contrato';

    protected $fillable = [
        'loja_id',
        'titulo',
        'conteudo',
        'ativo',
    ];

    protected function casts(): array
    {
        return ['ativo' => 'boolean'];
    }
}
