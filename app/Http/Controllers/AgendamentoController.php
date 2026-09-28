<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Produto;
use App\Models\Evento;

class AgendamentoController extends Controller
{
    public function index()
    {
        // Puxa tudo do banco de dados (em ordem alfabética)
        $clientes = Cliente::orderBy('nome')->get();
        $produtos = Produto::where('status', 1)->get(); // Traz apenas os que estão Disponíveis
        $eventos = Evento::where('status', 'Ativo')->get();
        
        // Manda essas 3 listas para a nossa tela visual
        return view('agenda.index', compact('clientes', 'produtos', 'eventos'));
    }
}