<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Medida;

class MedidaController extends Controller
{
    // Função para salvar a nova medição no banco de dados
    public function store(Request $request, $cliente_id)
    {
        // Garante que a cliente pertence à loja do usuário logado.
        // O Global Scope faz o findOrFail retornar 404 para cliente de outra loja.
        $cliente = Cliente::findOrFail($cliente_id);

        $dados = $request->except(['loja_id', 'cliente_id']);
        $dados['cliente_id'] = $cliente->id; // Trava essa medida à cliente específica
        // loja_id é injetado automaticamente pela trait BelongsToLoja.

        Medida::create($dados);

        // Volta para a Ficha da Cliente, manda mensagem de sucesso e avisa para abrir direto na aba 'medidas'
        return redirect()->route('clientes.show', $cliente->id)
                         ->with('success', 'Nova medição registrada com sucesso no histórico!')
                         ->with('aba_ativa', 'medidas');
    }
}
