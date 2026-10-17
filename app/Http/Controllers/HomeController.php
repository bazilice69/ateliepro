<?php

namespace App\Http\Controllers;

use App\Models\Acervo;
use App\Models\Depoimento;
use App\Models\Loja;
use App\Models\Plano;

/**
 * Página inicial pública (landing) com os planos vindos do banco.
 * Em controller (não closure) para permitir route:cache em produção.
 */
class HomeController extends Controller
{
    public function index()
    {
        $planos = Plano::ativos()->get();
        // Prova social: elogios de lojistas já aprovados pelo super_admin.
        $depoimentos = Depoimento::aprovados()->latest()->take(12)->get();

        // Vitrine pública: lojas ATIVAS que optaram por aparecer. Para o CARD da
        // landing carregamos só uma peça de destaque (com foto) + o total de
        // peças com foto (para o selo "X peças"). A página da loja mostra tudo.
        $vitrine = Loja::where('vitrine_publico', true)
            ->where('status', Loja::STATUS_ATIVO)
            ->with(['acervos' => function ($q) {
                $q->whereHas('fotos', fn ($f) => $f->withoutGlobalScope('loja'))
                  ->with(['fotos' => fn ($f) => $f->withoutGlobalScope('loja')])
                  ->orderByRaw("CASE WHEN status = ? THEN 0 ELSE 1 END", [Acervo::STATUS_DISPONIVEL])
                  ->take(6);
            }])
            ->get()
            // Só mostra lojas que, no fim, têm ao menos 1 peça com foto.
            ->filter(fn ($loja) => $loja->acervos->isNotEmpty())
            ->values();

        return view('welcome', compact('planos', 'depoimentos', 'vitrine'));
    }

    /** Página pública que apresenta as funcionalidades do sistema. */
    public function funcionalidades()
    {
        return view('funcionalidades');
    }
}
