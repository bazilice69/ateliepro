<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Evento extends Model
{
    use HasFactory;

    protected $table = 'eventos';

    protected $fillable = [
        'nome_evento',
        'data_evento',
        'tipo_evento',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'data_evento' => 'date',
        ];
    }

    /**
     * Agendamentos vinculados a este evento (provas, retirada, devolução...).
     */
    public function agendamentos(): HasMany
    {
        return $this->hasMany(Agendamento::class);
    }
}
