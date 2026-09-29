<?php

namespace App\Models;

use App\Models\Concerns\BelongsToLoja;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Acervo extends Model
{
    use HasFactory, BelongsToLoja;

    protected $table = 'acervos';

    // Estoque unificado: campos originais + campos migrados de `produtos`.
    protected $fillable = [
        'loja_id',
        'codigo',
        'nome',
        'categoria',
        'publico',
        'tamanho',
        'cor',
        'valor_locacao',
        'valor_venda',
        'caucao',
        'quantidade_estoque',
        'status',
        'foto',
        'localizacao',
        'observacoes',
        // Medidas femininas
        'med_busto', 'med_alt_busto', 'med_cintura', 'med_alt_corpo',
        'med_quadril', 'med_costas', 'med_alt_saia', 'med_alt_manga',
        // Medidas masculinas
        'med_ombro', 'med_torax', 'med_colarinho', 'med_comprimento_braco',
        // Infantil / sapatos
        'med_torax_infantil', 'med_cintura_infantil', 'med_comprimento_total',
        'idade_sugerida', 'numeracao_calcado', 'tipo_salto',
    ];

    protected function casts(): array
    {
        return [
            'valor_locacao' => 'decimal:2',
            'valor_venda' => 'decimal:2',
            'caucao' => 'decimal:2',
            'quantidade_estoque' => 'integer',
        ];
    }

    /**
     * Estados possíveis da peça (enum da migration create_acervos).
     */
    public const STATUS_DISPONIVEL = 'disponivel';
    public const STATUS_RESERVADA = 'reservada';
    public const STATUS_EM_PROVA = 'em_prova';
    public const STATUS_EM_AJUSTE = 'em_ajuste';
    public const STATUS_ALUGADA = 'alugada';
    public const STATUS_LAVANDERIA = 'lavanderia';
    public const STATUS_MANUTENCAO = 'manutencao';
    public const STATUS_INDISPONIVEL = 'indisponivel';

    /**
     * Locações desta peça.
     */
    public function locacoes(): HasMany
    {
        return $this->hasMany(Locacao::class);
    }

    /**
     * A peça está livre para uma nova reserva?
     */
    public function estaDisponivel(): bool
    {
        return $this->status === self::STATUS_DISPONIVEL;
    }
}
