<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Depoimento/elogio de um lojista. Começa pendente e, após aprovação do
 * super_admin, aparece como prova social na landing page.
 *
 * OBS.: NÃO usa a trait BelongsToLoja de propósito — a leitura pública (landing)
 * e a moderação (super_admin) precisam enxergar depoimentos de todas as lojas.
 * O vínculo com a loja é apenas informativo/para contato.
 */
class Depoimento extends Model
{
    use HasFactory;

    protected $table = 'depoimentos';

    protected $fillable = [
        'loja_id',
        'autor_nome',
        'loja_nome',
        'mensagem',
        'link_loja',
        'aprovado',
    ];

    protected function casts(): array
    {
        return [
            'aprovado' => 'boolean',
        ];
    }

    public function loja(): BelongsTo
    {
        return $this->belongsTo(Loja::class);
    }

    /**
     * Escopo: só os aprovados (para a vitrine pública).
     */
    public function scopeAprovados($query)
    {
        return $query->where('aprovado', true);
    }

    /**
     * Normaliza o link da loja para uma URL clicável segura (https://).
     * Retorna null se não houver link.
     */
    public function linkNormalizado(): ?string
    {
        $link = trim((string) $this->link_loja);
        if ($link === '') {
            return null;
        }

        // Instagram sem URL: "@fulano" ou "fulano" -> perfil do Instagram.
        if (str_starts_with($link, '@')) {
            return 'https://instagram.com/' . ltrim($link, '@');
        }

        // Já tem protocolo? mantém. Senão, prefixa https://.
        if (!preg_match('#^https?://#i', $link)) {
            $link = 'https://' . $link;
        }

        return filter_var($link, FILTER_VALIDATE_URL) ? $link : null;
    }

    /**
     * Rótulo amigável do link (sem protocolo), para exibir no botão.
     */
    public function linkRotulo(): ?string
    {
        $url = $this->linkNormalizado();
        if ($url === null) {
            return null;
        }

        return rtrim(preg_replace('#^https?://(www\.)?#i', '', $url), '/');
    }
}
