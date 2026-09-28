<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;

class DashboardController extends Controller
{
    public function index()
    {
        // Conta quantos clientes reais existem no banco de dados hoje
        $totalClientes = Cliente::count();

        // Variável provisória para a tela não quebrar (depois vamos puxar da tabela de locações)
        $locacoesAtivas = 12; 

        // Manda os dados para a sua tela visual maravilhosa do Dashboard
        return view('dashboard', compact('totalClientes', 'locacoesAtivas'));
    }
}