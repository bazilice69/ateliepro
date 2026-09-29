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

    public function store(Request $request)
    {
        $lojaId = Auth::user()->loja_id;

        $request->validate([
            'nome' => 'required|string|max:255',
            'telefone' => 'required|string|max:20',
            // CPF único DENTRO da loja (não global) — cada loja tem seus clientes.
            'cpf' => ['nullable', 'string', Rule::unique('clientes', 'cpf')->where('loja_id', $lojaId)],
        ]);

        // Nunca confiar em loja_id vindo do form: a trait injeta a loja do usuário.
        $cliente = Cliente::create($request->except('loja_id'));
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
        $cliente->update($request->except('loja_id'));

        return redirect()->route('clientes.show', $cliente->id)->with('success', 'Ficha atualizada com sucesso!');
    }
}