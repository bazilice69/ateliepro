<?php

namespace App\Models;

use App\Models\Concerns\BelongsToLoja;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory, BelongsToLoja;

    // Libera todos os campos para gravação rápida
    protected $guarded = [];

    // MÁGICA: Avisa ao sistema que este cliente tem um histórico de medidas (ordenado da mais recente para a mais antiga)
    public function medidas()
    {
        return $this->hasMany(Medida::class)->orderBy('data_medicao', 'desc');
    }
}