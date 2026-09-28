<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Agendamento extends Model
{
    use HasFactory;

    protected $table = 'agendamentos';

    protected $fillable = [
        'cliente_id',
        'evento_id',
        'produto_id',
        'data_hora',
        'duracao_minutos',
        'tipo_atendimento',
        'status_cor',
        'sala_atendimento',
        'observacoes_checklist',
    ];

    protected function casts(): array
    {
        return [
            'data_hora' => 'datetime',
            'duracao_minutos' => 'integer',
        ];
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function evento(): BelongsTo
    {
        return $this->belongsTo(Evento::class);
    }

    /**
     * Peça vinculada ao agendamento. O estoque foi unificado em `acervos`,
     * então a coluna `produto_id` referencia agora a tabela `acervos`.
     */
    public function produto(): BelongsTo
    {
        return $this->belongsTo(Acervo::class, 'produto_id');
    }

    /**
     * Alias semântico para a peça do acervo.
     */
    public function acervo(): BelongsTo
    {
        return $this->belongsTo(Acervo::class, 'produto_id');
    }
}
