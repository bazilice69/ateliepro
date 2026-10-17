<?php

namespace App\Http\Controllers;

use App\Models\Acervo;
use App\Models\Loja;

/**
 * VITRINE PÚBLICA (sem login): página de uma loja parceira que optou por
 * aparecer. Mostra o acervo dela (peças com foto) para divulgação — o contato
 * é direto com a loja (WhatsApp/Instagram/site). Respeita o opt-in e só exibe
 * lojas ATIVAS.
 */
class VitrineController extends Controller
{
    /**
     * Página pública de UMA loja (perfil + acervo completo com foto).
     */
    public function loja(Loja $loja)
    {
        // Só lojas que optaram pela vitrine E estão ativas são visíveis.
        abort_unless($loja->vitrine_publico && $loja->status === Loja::STATUS_ATIVO, 404);

        // Peças com foto (independente do status — é vitrine/divulgação, não
        // venda em tempo real). Disponíveis primeiro, depois as demais.
        $pecas = Acervo::withoutGlobalScope('loja')
            ->where('loja_id', $loja->id)
            ->whereHas('fotos', fn ($f) => $f->withoutGlobalScope('loja'))
            ->with(['fotos' => fn ($f) => $f->withoutGlobalScope('loja')])
            ->orderByRaw("CASE WHEN status = ? THEN 0 ELSE 1 END", [Acervo::STATUS_DISPONIVEL])
            ->orderBy('nome')
            ->get();

        return view('vitrine.loja', compact('loja', 'pecas'));
    }
}
