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

        // Orçamento é operação de LOJA. O super_admin (dono do SaaS) não tem
        // loja própria, então não há onde gravar — avisa em vez de dar 500.
        if (empty($lojaId)) {
            return back()->withInput()->with('error',
                'Orçamentos pertencem a uma loja. Entre com um usuário da loja (admin ou funcionário) para criar orçamentos.');
        }

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
        $dados['loja_id'] = $lojaId; // explícito (a trait não preenche p/ super_admin)

        // As colunas de custo são NOT NULL no banco (default 0). Dependendo do
        // modo escolhido, vários campos não são preenchidos no formulário e
        // chegam como null — o que viola a constraint. Normaliza para 0.
        foreach ([
            'custo_material', 'horas_trabalho', 'valor_hora', 'despesas_fixas',
            'custo_peca', 'custo_manutencao', 'margem_percent', 'taxa_cartao_percent',
            'custo_total', 'lucro_estimado', 'valor_final',
        ] as $campoNumerico) {
            $dados[$campoNumerico] = $dados[$campoNumerico] ?? 0;
        }

        $orcamento = Orcamento::create($dados);

        return redirect()->route('orcamentos.show', $orcamento)
            ->with('success', 'Orçamento salvo! Agora é só imprimir ou enviar ao cliente. 📄');
    }

    public function show(Orcamento $orcamento)
    {
        $orcamento->load(['cliente', 'acervo', 'contrato']);
        $temModelo = \App\Models\ModeloContrato::where('ativo', true)->exists();
        return view('orcamentos.show', compact('orcamento', 'temModelo'));
    }

    /**
     * Cliente aceitou: marca o orçamento como ACEITO e leva a lojista para a
     * geração do contrato já com o cliente e os valores pré-carregados.
     */
    public function aceitar(Orcamento $orcamento)
    {
        if ($orcamento->status !== Orcamento::STATUS_ACEITO) {
            $orcamento->update(['status' => Orcamento::STATUS_ACEITO]);
        }

        // Já tem contrato gerado? Vai direto pra ele.
        if ($orcamento->contrato_id) {
            return redirect()->route('contratos.show', $orcamento->contrato_id);
        }

        // Sem modelo de contrato cadastrado: orienta a cadastrar primeiro.
        // (A criação de modelos é restrita ao admin da loja; por isso avisamos
        // aqui em vez de redirecionar — evita um 403 para funcionários.)
        if (!\App\Models\ModeloContrato::where('ativo', true)->exists()) {
            return back()->with('error',
                'Para gerar o contrato, cadastre antes um MODELO de contrato em "Contratos → Modelos". '
                . 'Depois volte a este orçamento e clique em "Aceitar e gerar contrato".');
        }

        // Leva para a geração de contrato já vinculada a este orçamento.
        return redirect()->route('contratos.create', ['orcamento_id' => $orcamento->id]);
    }

    /** Cliente recusou o orçamento. */
    public function recusar(Orcamento $orcamento)
    {
        $orcamento->update(['status' => Orcamento::STATUS_RECUSADO]);
        return back()->with('success', 'Orçamento marcado como recusado.');
    }

    /** Versão imprimível/PDF — mostra APENAS o valor final (sem custos internos). */
    public function imprimir(Orcamento $orcamento)
    {
        $orcamento->load(['cliente', 'acervo']);
        $loja = Auth::user()->loja ?? null;
        return view('orcamentos.imprimir', compact('orcamento', 'loja'));
    }
}
