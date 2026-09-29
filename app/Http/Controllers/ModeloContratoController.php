<?php

namespace App\Http\Controllers;

use App\Models\ModeloContrato;
use App\Services\ContratoService;
use Illuminate\Http\Request;

/**
 * CRUD dos MODELOS de contrato da loja (só admin_loja). Escopado por loja.
 */
class ModeloContratoController extends Controller
{
    public function index()
    {
        $modelos = ModeloContrato::orderBy('titulo')->get();
        return view('contratos.modelos.index', compact('modelos'));
    }

    public function create()
    {
        return view('contratos.modelos.form', [
            'modelo' => new ModeloContrato(['ativo' => true, 'conteudo' => $this->conteudoExemplo()]),
            'variaveis' => ContratoService::variaveisDisponiveis(),
        ]);
    }

    public function store(Request $request)
    {
        $dados = $this->validar($request);
        ModeloContrato::create($dados);

        return redirect()->route('contratos.modelos.index')->with('success', 'Modelo de contrato criado!');
    }

    public function edit(ModeloContrato $modelo)
    {
        return view('contratos.modelos.form', [
            'modelo' => $modelo,
            'variaveis' => ContratoService::variaveisDisponiveis(),
        ]);
    }

    public function update(Request $request, ModeloContrato $modelo)
    {
        $modelo->update($this->validar($request));

        return redirect()->route('contratos.modelos.index')->with('success', 'Modelo atualizado!');
    }

    public function destroy(ModeloContrato $modelo)
    {
        $modelo->delete();
        return back()->with('success', 'Modelo removido.');
    }

    private function validar(Request $request): array
    {
        $dados = $request->validate([
            'titulo' => ['required', 'string', 'max:150'],
            'conteudo' => ['required', 'string'],
            'ativo' => ['nullable', 'boolean'],
        ]);
        $dados['ativo'] = $request->boolean('ativo');

        return $dados;
    }

    private function conteudoExemplo(): string
    {
        return <<<TXT
CONTRATO DE LOCAÇÃO DE TRAJE

LOCADORA: {{loja_nome}}, CNPJ/CPF {{loja_cnpj}}, telefone {{loja_telefone}}.

LOCATÁRIO(A): {{cliente_nome}}, {{cliente_nacionalidade}}, {{cliente_estado_civil}},
{{cliente_profissao}}, portador(a) do RG {{cliente_rg}} e CPF {{cliente_cpf}},
residente em {{cliente_endereco}}, telefone {{cliente_telefone}}.

OBJETO: Locação da peça {{locacao_peca}}.

VALORES: Valor total de {{locacao_valor_total}}, com sinal de {{locacao_sinal}} já pago,
restando o saldo de {{locacao_saldo}}. Caução: {{locacao_caucao}}.

DATAS: Retirada em {{locacao_data_retirada}}, evento em {{locacao_data_evento}} e
devolução até {{locacao_data_devolucao}}.

O(a) locatário(a) declara ter recebido a peça em perfeitas condições e compromete-se a
devolvê-la no prazo acima, respondendo por eventuais danos.

{{cidade_hoje}}


_______________________________          _______________________________
        {{loja_nome}}                              {{cliente_nome}}
           LOCADORA                                LOCATÁRIO(A)
TXT;
    }
}
