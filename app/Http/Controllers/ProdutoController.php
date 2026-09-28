<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Acervo;

class ProdutoController extends Controller
{
    // Abre a tela de cadastro
    public function create()
    {
        // Gera um código automático simples (ex: VN-4592), mas a pessoa pode apagar e digitar outro
        $codigoSugerido = 'VN-' . rand(1000, 9999);
        return view('acervo.create', compact('codigoSugerido'));
    }

    // Salva no banco de dados
    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|unique:acervos,codigo',
            'nome' => 'required|string|max:255',
            'categoria' => 'required|string',
            'valor_locacao' => 'required|numeric',
        ]);

        Acervo::create($request->all());

        return redirect()->route('dashboard')->with('success', 'Peça cadastrada no acervo com sucesso!');
    }
}