<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::all();
        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'telefone' => 'required|string|max:20',
            'cpf' => 'nullable|string|unique:clientes,cpf',
        ]);

        $cliente = Cliente::create($request->all());
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
        // A regra de validação do CPF ignora o próprio cliente para não dar erro de duplicidade com ele mesmo
        $request->validate([
            'nome' => 'required|string|max:255',
            'telefone' => 'required|string|max:20',
            'cpf' => 'nullable|string|unique:clientes,cpf,' . $id,
        ]);

        $cliente = Cliente::findOrFail($id);
        $cliente->update($request->all());

        return redirect()->route('clientes.show', $cliente->id)->with('success', 'Ficha atualizada com sucesso!');
    }
}