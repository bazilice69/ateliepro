<?php

namespace App\Models;

use App\Models\Concerns\BelongsToLoja;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Encomenda / Sob Medida — peça criada do ZERO para uma cliente.
 *
 * Ao contrário da locação de peça de acervo, aqui a estilista modela e costura
 * a peça em cima das medidas da cliente, com croqui, tecidos, etapas de
 * produção e um prazo de entrega crítico. A peça pode ser comprada ou alugada.
 */
class Encomenda extends Model
{
    use HasFactory, BelongsToLoja;

    protected $guarded = [];

    protected $casts = [
        'data_pedido'  => 'date',
        'data_prova'   => 'date',
        'data_entrega' => 'date',
        'valor'        => 'decimal:2',
        'sinal'        => 'decimal:2',
    ];

    // --- Tipo da peça -------------------------------------------------------
    public const TIPO_COMPRA  = 'compra';
    public const TIPO_ALUGUEL = 'aluguel';

    public const TIPOS = [
        self::TIPO_COMPRA  => 'Compra (fica com a cliente)',
        self::TIPO_ALUGUEL => 'Aluguel (peça exclusiva do ateliê)',
    ];

    // --- Etapas de produção (na ordem do fluxo) -----------------------------
    public const ETAPA_PEDIDO     = 'pedido';
    public const ETAPA_MODELAGEM  = 'modelagem';
    public const ETAPA_CORTE      = 'corte';
    public const ETAPA_COSTURA    = 'costura';
    public const ETAPA_PROVA      = 'prova';
    public const ETAPA_AJUSTES    = 'ajustes';
    public const ETAPA_ACABAMENTO = 'acabamento';
    public const ETAPA_PRONTA     = 'pronta';
    public const ETAPA_ENTREGUE   = 'entregue';
    public const ETAPA_CANCELADA  = 'cancelada';

    public const ETAPAS = [
        self::ETAPA_PEDIDO     => 'Pedido',
        self::ETAPA_MODELAGEM  => 'Modelagem',
        self::ETAPA_CORTE      => 'Corte',
        self::ETAPA_COSTURA    => 'Costura',
        self::ETAPA_PROVA      => 'Prova',
        self::ETAPA_AJUSTES    => 'Ajustes',
        self::ETAPA_ACABAMENTO => 'Acabamento',
        self::ETAPA_PRONTA     => 'Pronta para entrega',
        self::ETAPA_ENTREGUE   => 'Entregue',
        self::ETAPA_CANCELADA  => 'Cancelada',
    ];

    /**
     * Etapas ativas (para exibir a linha do tempo de produção). Não inclui os
     * estados finais "entregue" e "cancelada".
     */
    public const ETAPAS_PRODUCAO = [
        self::ETAPA_PEDIDO,
        self::ETAPA_MODELAGEM,
        self::ETAPA_CORTE,
        self::ETAPA_COSTURA,
        self::ETAPA_PROVA,
        self::ETAPA_AJUSTES,
        self::ETAPA_ACABAMENTO,
        self::ETAPA_PRONTA,
    ];

    // --- Relações -----------------------------------------------------------

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function medida()
    {
        return $this->belongsTo(Medida::class);
    }

    /**
     * Histórico de medições desta encomenda (uma por prova), da mais recente
     * para a mais antiga. Cada prova gera uma nova medição — nunca sobrescreve.
     */
    public function medidas()
    {
        return $this->hasMany(Medida::class)->orderByDesc('data_medicao')->orderByDesc('id');
    }

    /**
     * A medição mais recente do histórico (a "atual" da peça).
     */
    public function medidaAtual(): ?Medida
    {
        return $this->relationLoaded('medidas')
            ? $this->medidas->first()
            : $this->medidas()->first();
    }

    // --- Helpers de negócio -------------------------------------------------

    /**
     * Saldo a receber = valor total - sinal já pago.
     */
    public function saldo(): float
    {
        return round((float) $this->valor - (float) $this->sinal, 2);
    }

    /**
     * Rótulo amigável da etapa atual.
     */
    public function etapaLabel(): string
    {
        return self::ETAPAS[$this->etapa] ?? ucfirst((string) $this->etapa);
    }

    /**
     * Rótulo amigável do tipo (compra/aluguel).
     */
    public function tipoLabel(): string
    {
        return self::TIPOS[$this->tipo] ?? ucfirst((string) $this->tipo);
    }

    /**
     * Índice da etapa atual na linha de produção (0-based); -1 se for um estado
     * final (entregue/cancelada) ou desconhecido.
     */
    public function etapaIndice(): int
    {
        $i = array_search($this->etapa, self::ETAPAS_PRODUCAO, true);

        return $i === false ? -1 : $i;
    }

    /**
     * A encomenda está atrasada? (prazo de entrega passou e ainda não entregue)
     */
    public function estaAtrasada(): bool
    {
        if (empty($this->data_entrega)) {
            return false;
        }

        if (in_array($this->etapa, [self::ETAPA_ENTREGUE, self::ETAPA_CANCELADA], true)) {
            return false;
        }

        return $this->data_entrega->isPast();
    }
}
