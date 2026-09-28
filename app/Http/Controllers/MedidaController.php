<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Medida;

class MedidaController extends Controller
{
    // Função para salvar a nova medição no banco de dados
    public function store(Request $request, $cliente_id)
    {
        $dados = $request->all();
        $dados['cliente_id'] = $cliente_id; // Trava essa medida à cliente específica

        Medida::create($dados);

        // Volta para a Ficha da Cliente, manda mensagem de sucesso e avisa para abrir direto na aba 'medidas'
        return redirect()->route('clientes.show', $cliente_id)
                         ->with('success', 'Nova medição registrada com sucesso no histórico!')
                         ->with('aba_ativa', 'medidas');
    }
}