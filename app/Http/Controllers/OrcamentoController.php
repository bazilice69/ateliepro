<?php

namespace App\Http\Controllers;

use App\Models\Acervo;
use App\Models\Cliente;
use App\Models\Orcamento;
use App\Support\Dinheiro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Orçamentos: a lojista monta o preço (calculadora conectada a cliente/peça),
 * gera um orçamento imprimível que mostra SÓ o valor final ao cliente, e — se
 * aceito — transforma em contrato (Bloco 2).
 */
class OrcamentoController extends Controller
{
    public function index()
    {
        $orcamentos = Orcamento::with(['cliente', 'acervo'])->latest()->get();
        return view('orcamentos.index', compact('orcamentos'));
    }

    public function create()
    {
        $clientes = Cliente::orderBy('nome')->get();
        $acervos = Acervo::where('status', '!=', Acervo::STATUS_INDISPONIVEL)->orderBy('nome')->get();
        return view('orcamentos.create', compact('clientes', 'acervos'));
    }

    public function store(Request $request)
    {
        $lojaId = Auth::user()->loja_id;

        // Normaliza os valores em formato BR antes de validar.
        $this->normalizarDinheiro($request, [
            'valor_final', 'custo_material', 'valor_hora', 'despesas_fixas',
            'custo_peca', 'custo_manutencao', 'custo_total', 'lucro_estimado',
        ]);

        $dados = $request->validate([
            'cliente_id' => ['nullable', Rule::exists('clientes', 'id')->where('loja_id', $lojaId)],
            'acervo_id' => ['nullable', Rule::exists('acervos', 'id')->where('loja_id', $lojaId)],
            'modo' => ['required', Rule::in(array_keys(Orcamento::MODOS))],
            'descricao' => ['nullable', 'string', 'max:255'],
            'valor_final' => ['required', 'numeric', 'min:0'],
            'custo_material' => ['nullable', 'numeric', 'min:0'],
            'horas_trabalho' => ['nullable', 'numeric', 'min:0'],
            'valor_hora' => ['nullable', 'numeric', 'min:0'],
            'despesas_fixas' => ['nullable', 'numeric', 'min:0'],
            'custo_peca' => ['nullable', 'numeric', 'min:0'],
            'num_alugueis' => ['nullable', 'integer', 'min:1'],
            'custo_manutencao' => ['nullable', 'numeric', 'min:0'],
            'margem_percent' => ['nullable', 'numeric', 'min:0'],
            'taxa_cartao_percent' => ['nullable', 'numeric', 'min:0'],
            'custo_total' => ['nullable', 'numeric', 'min:0'],
            'lucro_estimado' => ['nullable', 'numeric'],
            'observacoes' => ['nullable', 'string'],
        ]);

        $dados['status'] = Orcamento::STATUS_RASCUNHO;
        $orcamento = Orcamento::create($dados);

        return redirect()->route('orcamentos.show', $orcamento)
            ->with('success', 'Orçamento salvo! Agora é só imprimir ou enviar ao cliente. 📄');
    }

    public function show(Orcamento $orcamento)
    {
        $orcamento->load(['cliente', 'acervo', 'contrato']);
        return view('orcamentos.show', compact('orcamento'));
    }

    /** Versão imprimível/PDF — mostra APENAS o valor final (sem custos internos). */
    public function imprimir(Orcamento $orcamento)
    {
        $orcamento->load(['cliente', 'acervo']);
        $loja = Auth::user()->loja ?? null;
        return view('orcamentos.imprimir', compact('orcamento', 'loja'));
    }
}
