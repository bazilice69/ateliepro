<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\LancamentoFinanceiro;
use App\Models\CategoriaFinanceira;
use App\Models\Cliente;

class FinanceiroController extends Controller
{
    public function index()
    {
        $lancamentos = LancamentoFinanceiro::with('categoria', 'cliente')
                        ->orderBy('data_vencimento', 'desc')
                        ->get();

        $categoriasReceita = CategoriaFinanceira::where('tipo', 'Receita')->get();
        $categoriasDespesa = CategoriaFinanceira::where('tipo', 'Despesa')->get();
        $clientes = Cliente::orderBy('nome')->get(); // Para vincular nas locações/receitas

        $recebidoMes = LancamentoFinanceiro::where('tipo', 'Receita')->where('status', 'Pago')->sum('valor_final');
        $aReceber = LancamentoFinanceiro::where('tipo', 'Receita')->where('status', 'Pendente')->sum('valor_final');
        $emAtraso = LancamentoFinanceiro::where('tipo', 'Receita')->where('status', 'Vencido')->sum('valor_final');
        $despesasPagas = LancamentoFinanceiro::where('tipo', 'Despesa')->where('status', 'Pago')->sum('valor_final');
        
        $saldoPrevisto = $recebidoMes - $despesasPagas;

        return view('financeiro.index', compact(
            'lancamentos', 'categoriasReceita', 'categoriasDespesa', 'clientes',
            'recebidoMes', 'aReceber', 'emAtraso', 'saldoPrevisto'
        ));
    }

    public function store(Request $request)
    {
        $lojaId = Auth::user()->loja_id;

        // Validação dos campos essenciais
        $request->validate([
            'tipo' => 'required|in:Receita,Despesa',
            // categoria precisa pertencer à mesma loja
            'categoria_financeira_id' => ['required', Rule::exists('categoria_financeiras', 'id')->where('loja_id', $lojaId)],
            'descricao' => 'required|string|max:255',
            'valor_original' => 'required|numeric|min:0.01',
            'data_vencimento' => 'required|date',
            'status' => 'required|in:Pendente,Pago,Parcialmente Pago,Vencido,Cancelado',
        ]);

        // Criação do lançamento financeiro
        LancamentoFinanceiro::create([
            'tipo' => $request->tipo,
            'categoria_id' => $request->categoria_financeira_id,
            'cliente_id' => $request->cliente_id,
            'descricao' => $request->descricao,
            'valor_original' => $request->valor_original,
            'desconto' => $request->desconto ?? 0,
            'acrescimo' => $request->acrescimo ?? 0,
            'valor_final' => $request->valor_original - ($request->desconto ?? 0) + ($request->acrescimo ?? 0),
            'data_vencimento' => $request->data_vencimento,
            'data_pagamento' => $request->status == 'Pago' ? now() : null,
            'forma_pagamento' => $request->forma_pagamento,
            'status' => $request->status,
            'observacoes' => $request->observacoes,
        ]);

        return redirect()->route('financeiro.index')->with('success', 'Lançamento financeiro registrado com sucesso!');
    }
}