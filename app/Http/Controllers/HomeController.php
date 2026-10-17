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

        // Vitrine pública: lojas ATIVAS que optaram por aparecer, com até 6 peças
        // disponíveis e COM foto (acervo sem imagem não entra). Carrega as fotos
        // já ordenadas (capa primeiro) para montar as miniaturas.
        $vitrine = Loja::where('vitrine_publico', true)
            ->where('status', Loja::STATUS_ATIVO)
            ->with(['acervos' => function ($q) {
                $q->where('status', Acervo::STATUS_DISPONIVEL)
                  ->whereHas('fotos', fn ($f) => $f->withoutGlobalScope('loja'))
                  ->with(['fotos' => fn ($f) => $f->withoutGlobalScope('loja')])
                  ->latest()
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
