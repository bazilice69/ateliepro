<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Contrato;
use App\Models\Locacao;
use App\Models\ModeloContrato;
use App\Services\ContratoService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Geração e gestão dos CONTRATOS (escopado por loja).
 * O conteúdo é preenchido a partir do cliente/locação e CONGELADO ao salvar.
 */
class ContratoController extends Controller
{
    public function __construct(private ContratoService $service)
    {
    }

    public function index()
    {
        $contratos = Contrato::with(['cliente'])->latest()->get();
        return view('contratos.index', compact('contratos'));
    }

    /**
     * Tela para gerar um contrato: escolher cliente, (locação) e modelo.
     */
    public function create(Request $request)
    {
        $clientes = Cliente::orderBy('nome')->get();
        $modelos = ModeloContrato::where('ativo', true)->orderBy('titulo')->get();
        $locacoes = Locacao::with('acervo')->latest()->get();

        // Gerando a partir de um orçamento aceito? Pré-seleciona o cliente.
        $orcamento = $request->filled('orcamento_id')
            ? \App\Models\Orcamento::with(['cliente', 'acervo'])->find($request->orcamento_id)
            : null;
        $cliente = $orcamento?->cliente;

        return view('contratos.create', compact('clientes', 'modelos', 'locacoes', 'orcamento', 'cliente'));
    }

    /**
     * Pré-visualiza o conteúdo preenchido (antes de salvar) — permite editar.
     */
    public function preview(Request $request)
    {
        $lojaId = $request->user()->loja_id;

        $request->validate([
            'modelo_contrato_id' => ['required', Rule::exists('modelos_contrato', 'id')->where('loja_id', $lojaId)],
            'cliente_id' => ['nullable', Rule::exists('clientes', 'id')->where('loja_id', $lojaId)],
            'locacao_id' => ['nullable', Rule::exists('locacaos', 'id')->where('loja_id', $lojaId)],
            'orcamento_id' => ['nullable', Rule::exists('orcamentos', 'id')->where('loja_id', $lojaId)],
        ]);

        $modelo = ModeloContrato::findOrFail($request->modelo_contrato_id);
        $cliente = $request->cliente_id ? Cliente::find($request->cliente_id) : null;
        $locacao = $request->locacao_id ? Locacao::with('acervo')->find($request->locacao_id) : null;
        $orcamento = $request->orcamento_id ? \App\Models\Orcamento::with(['cliente', 'acervo'])->find($request->orcamento_id) : null;

        // Se veio de um orçamento e nenhum cliente foi escolhido, usa o do orçamento.
        if (!$cliente && $orcamento?->cliente) {
            $cliente = $orcamento->cliente;
        }

        $valores = $this->service->valores($request->user()->loja, $cliente, $locacao, $orcamento);
        $conteudoPreenchido = $this->service->preencher($modelo->conteudo, $valores);

        $clientes = Cliente::orderBy('nome')->get();
        $modelos = ModeloContrato::where('ativo', true)->orderBy('titulo')->get();
        $locacoes = Locacao::with('acervo')->latest()->get();

        return view('contratos.create', compact(
            'clientes', 'modelos', 'locacoes', 'conteudoPreenchido', 'modelo', 'cliente', 'locacao', 'orcamento'
        ));
    }

    /**
     * Salva o contrato com o conteúdo final CONGELADO.
     */
    public function store(Request $request)
    {
        $lojaId = $request->user()->loja_id;

        $dados = $request->validate([
            'titulo' => ['required', 'string', 'max:150'],
            'conteudo_final' => ['required', 'string'],
            'cliente_id' => ['nullable', Rule::exists('clientes', 'id')->where('loja_id', $lojaId)],
            'locacao_id' => ['nullable', Rule::exists('locacaos', 'id')->where('loja_id', $lojaId)],
            'modelo_contrato_id' => ['nullable', Rule::exists('modelos_contrato', 'id')->where('loja_id', $lojaId)],
            'orcamento_id' => ['nullable', Rule::exists('orcamentos', 'id')->where('loja_id', $lojaId)],
        ]);

        $orcamentoId = $dados['orcamento_id'] ?? null;
        unset($dados['orcamento_id']); // não é coluna de contratos

        $dados['status'] = Contrato::STATUS_FINALIZADO;
        $dados['data_contrato'] = now();

        $contrato = Contrato::create($dados);

        // Veio de um orçamento? Vincula nos dois sentidos e marca como aceito.
        if ($orcamentoId) {
            \App\Models\Orcamento::where('id', $orcamentoId)->update([
                'contrato_id' => $contrato->id,
                'status' => \App\Models\Orcamento::STATUS_ACEITO,
            ]);
        }

        return redirect()->route('contratos.show', $contrato)->with('success', 'Contrato gerado com sucesso!');
    }

    public function show(Contrato $contrato)
    {
        return view('contratos.show', compact('contrato'));
    }

    public function imprimir(Contrato $contrato)
    {
        return view('contratos.imprimir', compact('contrato'));
    }

    public function destroy(Contrato $contrato)
    {
        $contrato->delete();
        return redirect()->route('contratos.index')->with('success', 'Contrato removido.');
    }
}
