<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

/**
 * Tela exibida quando a loja está inadimplente/bloqueada (ou sem loja).
 * Substitui, no fluxo SaaS, a antiga tela de "licença por máquina".
 */
class LojaBloqueioController extends Controller
{
    public function index(): View
    {
        $loja = auth()->user()?->loja;
        return view('loja.bloqueada', compact('loja'));
    }
}
