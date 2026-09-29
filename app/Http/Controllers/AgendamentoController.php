<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Acervo;
use App\Models\Evento;

class AgendamentoController extends Controller
{
    public function index()
    {
        // Puxa tudo do banco de dados (em ordem alfabética)
        $clientes = Cliente::orderBy('nome')->get();
        // Estoque unificado em `acervos`: traz apenas as peças disponíveis.
        // (a variável continua chamada $produtos para compatibilidade com a view)
        $produtos = Acervo::where('status', 'disponivel')->orderBy('nome')->get();
        $eventos = Evento::where('status', 'Ativo')->get();

        // Manda essas 3 listas para a nossa tela visual
        return view('agenda.index', compact('clientes', 'produtos', 'eventos'));
    }
}