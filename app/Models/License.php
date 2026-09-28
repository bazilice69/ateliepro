<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    protected $table = 'licenses';
    protected $fillable = ['chave_licenca', 'tipo_plano', 'data_expiracao', 'ativo'];
}