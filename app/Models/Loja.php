<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loja extends Model
{
    use HasFactory;

    protected $table = 'lojas';

    protected $fillable = [
        'chave_licenca',
        'nome_fantasia',
        'cnpj_cpf',
        'email_responsavel',
        'telefone',
        'status',
        'plano',
        'valor_mensal',
        'desconto',
        'data_vencimento',
        'trial_termina_em',
        'observacoes_admin',
    ];

    protected function casts(): array
    {
        return [
            'valor_mensal' => 'decimal:2',
            'desconto' => 'decimal:2',
            'data_vencimento' => 'date',
            'trial_termina_em' => 'date',
        ];
    }

    public const DIAS_TRIAL = 7;

    /**
     * Valores possíveis para a coluna status (enum da migration).
     */
    public const STATUS_ATIVO = 'ativo';
    public const STATUS_INADIMPLENTE = 'inadimplente';
    public const STATUS_BLOQUEADO = 'bloqueado';

    /**
     * Valor efetivamente cobrado (mensal menos desconto).
     */
    public function valorEfetivo(): float
    {
        return max(0, (float) $this->valor_mensal - (float) $this->desconto);
    }

    /**
     * A assinatura está próxima do vencimento (<= 7 dias)?
     */
    public function venceEmBreve(): bool
    {
        return $this->data_vencimento
            && $this->status === self::STATUS_ATIVO
            && $this->data_vencimento->isBetween(now(), now()->addDays(7));
    }

    /**
     * Uma loja possui vários usuários (dono + funcionários).
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Uma loja tem um histórico de assinaturas.
     */
    public function assinaturas(): HasMany
    {
        return $this->hasMany(Assinatura::class);
    }

    /**
     * Retorna a assinatura mais recente da loja (a "vigente").
     */
    public function assinaturaAtual()
    {
        return $this->hasOne(Assinatura::class)->latestOfMany();
    }

    /**
     * Helper: a loja está com status ativo?
     */
    public function estaAtiva(): bool
    {
        return $this->status === self::STATUS_ATIVO;
    }

    /**
     * A loja está no período de teste grátis (trial ainda vigente)?
     */
    public function emTrial(): bool
    {
        return $this->trial_termina_em !== null
            && $this->trial_termina_em->endOfDay()->isFuture();
    }

    /**
     * Dias restantes de trial (0 se já expirou / não há trial).
     */
    public function diasDeTrial(): int
    {
        if (!$this->trial_termina_em) {
            return 0;
        }
        return max(0, (int) now()->startOfDay()->diffInDays($this->trial_termina_em->endOfDay(), false));
    }

    /**
     * A loja tem acesso liberado ao sistema?
     * Regra SaaS: status ativo E (dentro do trial OU com vencimento vigente).
     */
    public function temAcesso(): bool
    {
        if ($this->status !== self::STATUS_ATIVO) {
            return false;
        }

        // Em trial válido → acesso liberado.
        if ($this->emTrial()) {
            return true;
        }

        // Assinatura paga com vencimento no futuro → liberado.
        if ($this->data_vencimento && $this->data_vencimento->endOfDay()->isFuture()) {
            return true;
        }

        // Sem trial e sem vencimento definido ainda → considera liberado só se
        // nunca teve trial (retrocompat com dados antigos que já eram 'ativo').
        return $this->trial_termina_em === null && $this->data_vencimento === null;
    }
}
