<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\Acervo;

class ProdutoController extends Controller
{
    // Lista as peças do acervo
    public function index()
    {
        $pecas = Acervo::orderBy('codigo')->get();
        return view('acervo.index', compact('pecas'));
    }

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
        $lojaId = Auth::user()->loja_id;

        $request->validate([
            // Código único DENTRO da loja (cada loja tem sua própria numeração).
            'codigo' => ['required', 'string', Rule::unique('acervos', 'codigo')->where('loja_id', $lojaId)],
            'nome' => 'required|string|max:255',
            'categoria' => 'required|string',
            'valor_locacao' => 'required|numeric',
        ]);

        Acervo::create($request->except('loja_id'));

        return redirect()->route('dashboard')->with('success', 'Peça cadastrada no acervo com sucesso!');
    }
}