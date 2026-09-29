<?php

namespace App\Http\Controllers;

use App\Models\CategoriaFinanceira;
use App\Models\DespesaRecorrente;
use App\Services\DespesaRecorrenteService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Gestão das despesas recorrentes/fixas da loja (internet, luz, água...).
 * Tudo escopado pela loja (trait BelongsToLoja).
 */
class DespesaRecorrenteController extends Controller
{
    public function index()
    {
        $despesas = DespesaRecorrente::with('categoria')->orderBy('descricao')->get();
        $categorias = CategoriaFinanceira::where('tipo', 'Despesa')->orderBy('nome')->get();

        return view('financeiro.recorrentes', compact('despesas', 'categorias'));
    }

    public function store(Request $request)
    {
        $lojaId = $request->user()->loja_id;

        $dados = $request->validate([
            'descricao' => ['required', 'string', 'max:150'],
            'valor' => ['required', 'numeric', 'min:0.01'],
            'dia_vencimento' => ['required', 'integer', 'min:1', 'max:28'],
            'categoria_id' => ['nullable', Rule::exists('categoria_financeiras', 'id')->where('loja_id', $lojaId)],
        ]);

        $dados['ativo'] = true;
        DespesaRecorrente::create($dados);

        return back()->with('success', 'Despesa recorrente cadastrada!');
    }

    public function update(Request $request, DespesaRecorrente $recorrente)
    {
        $lojaId = $request->user()->loja_id;

        $dados = $request->validate([
            'descricao' => ['required', 'string', 'max:150'],
            'valor' => ['required', 'numeric', 'min:0.01'],
            'dia_vencimento' => ['required', 'integer', 'min:1', 'max:28'],
            'categoria_id' => ['nullable', Rule::exists('categoria_financeiras', 'id')->where('loja_id', $lojaId)],
            'ativo' => ['nullable', 'boolean'],
        ]);

        $dados['ativo'] = $request->boolean('ativo');
        $recorrente->update($dados);

        return back()->with('success', 'Despesa recorrente atualizada!');
    }

    public function destroy(DespesaRecorrente $recorrente)
    {
        $recorrente->delete();
        return back()->with('success', 'Despesa recorrente removida.');
    }

    /**
     * Gera os lançamentos do mês atual manualmente (botão "gerar agora").
     */
    public function gerarAgora(DespesaRecorrenteService $service)
    {
        $qtd = $service->gerarParaLojaAtual();

        return back()->with('success', $qtd > 0
            ? "{$qtd} lançamento(s) gerado(s) para este mês."
            : 'Nenhum lançamento novo — as despesas do mês já foram geradas.');
    }
}
