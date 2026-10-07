<?php

namespace App\Http\Controllers;

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
        return view('welcome', compact('planos'));
    }

    /** Página pública que apresenta as funcionalidades do sistema. */
    public function funcionalidades()
    {
        return view('funcionalidades');
    }
}
