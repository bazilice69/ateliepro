<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\Cliente;

class ClienteController extends Controller
{
    public function index()
    {
        // O Global Scope (trait BelongsToLoja) já filtra pela loja do usuário logado.
        $clientes = Cliente::all();
        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    /**
     * Campos aceitos do formulário de cliente (whitelist).
     *
     * Usamos uma lista explícita em vez de $request->except() para NUNCA tentar
     * gravar um campo que não exista na tabela (o que causava erro SQL / página
     * em branco). loja_id é injetado automaticamente pela trait BelongsToLoja.
     */
    private function camposPermitidos(Request $request): array
    {
        return $request->only([
            'nome', 'cpf', 'rg', 'telefone', 'email', 'data_nascimento',
            'estado_civil', 'profissao', 'nacionalidade', 'tipo',
            'responsavel_nome', 'responsavel_parentesco',
            'cep', 'rua', 'numero', 'complemento', 'bairro', 'cidade', 'estado',
            'observacoes',
        ]);
    }

    public function store(Request $request)
    {
        $lojaId = Auth::user()->loja_id;

        $request->validate([
            'nome' => 'required|string|max:255',
            'telefone' => 'required|string|max:20',
            // CPF único DENTRO da loja (não global) — cada loja tem seus clientes.
            'cpf' => ['nullable', 'string', Rule::unique('clientes', 'cpf')->where('loja_id', $lojaId)],
        ]);

        // Só os campos conhecidos; loja_id é injetado pela trait BelongsToLoja.
        $cliente = Cliente::create($this->camposPermitidos($request));
        return redirect()->route('clientes.show', $cliente->id)->with('success', 'Cliente cadastrado com sucesso!');
    }

    public function show($id)
    {
        $cliente = Cliente::findOrFail($id);
        return view('clientes.show', compact('cliente'));
    }

    // NOVO: Abre a tela de edição com os dados atuais
    public function edit($id)
    {
        $cliente = Cliente::findOrFail($id);
        return view('clientes.edit', compact('cliente'));
    }

    // NOVO: Recebe os dados alterados e salva no banco
    public function update(Request $request, $id)
    {
        $lojaId = Auth::user()->loja_id;

        // CPF único dentro da loja, ignorando o próprio cliente.
        $request->validate([
            'nome' => 'required|string|max:255',
            'telefone' => 'required|string|max:20',
            'cpf' => ['nullable', 'string', Rule::unique('clientes', 'cpf')->where('loja_id', $lojaId)->ignore($id)],
        ]);

        // findOrFail já é escopado pela loja (Global Scope) => ID de outra loja vira 404.
        $cliente = Cliente::findOrFail($id);
        $cliente->update($this->camposPermitidos($request));

        return redirect()->route('clientes.show', $cliente->id)->with('success', 'Ficha atualizada com sucesso!');
    }
}