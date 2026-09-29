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

    public function locacoes()
    {
        return $this->hasMany(Locacao::class)->latest();
    }

    public function agendamentos()
    {
        return $this->hasMany(Agendamento::class)->latest('data_hora');
    }

    public function provas()
    {
        return $this->hasMany(Prova::class)->latest('data_prova');
    }
}