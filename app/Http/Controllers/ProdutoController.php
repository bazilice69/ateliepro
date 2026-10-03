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

        return redirect()->route('acervo.index')->with('success', 'Peça cadastrada no acervo com sucesso!');
    }

    /**
     * Cadastro RÁPIDO de peça (via modal, dentro da tela de Locação).
     * Pede o essencial e devolve JSON para o front adicionar no seletor sem
     * recarregar a página.
     */
    public function storeRapido(Request $request)
    {
        $lojaId = Auth::user()->loja_id;

        $dados = $request->validate([
            'codigo' => ['required', 'string', 'max:50', Rule::unique('acervos', 'codigo')->where('loja_id', $lojaId)],
            'nome' => 'required|string|max:255',
            'categoria' => 'required|string|max:100',
            'valor_locacao' => 'required|numeric|min:0',
            'caucao' => 'nullable|numeric|min:0',
        ]);

        $dados['status'] = Acervo::STATUS_DISPONIVEL;
        $peca = Acervo::create($dados);

        return response()->json([
            'id' => $peca->id,
            'label' => "[{$peca->codigo}] {$peca->nome} (R$ " . number_format((float) $peca->valor_locacao, 2, ',', '.') . ')',
        ]);
    }

    // Histórico / "vida" da peça: locações, provas e serviços.
    public function show(Acervo $peca)
    {
        $peca->load([
            'locacoes.cliente',
            'provas.cliente',
            'servicos',
        ]);

        return view('acervo.show', compact('peca'));
    }
}