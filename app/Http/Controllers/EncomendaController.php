<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Encomenda;
use App\Models\Medida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/**
 * Encomendas / Sob Medida — peças criadas do zero para a cliente.
 *
 * Fluxo: pedido -> modelagem -> corte -> costura -> prova -> ajustes ->
 * acabamento -> pronta -> entregue. A peça pode ser compra ou aluguel.
 * Tudo escopado por loja pela trait BelongsToLoja.
 */
class EncomendaController extends Controller
{
    /**
     * Painel de encomendas: em produção primeiro, depois entregues/canceladas.
     */
    public function index()
    {
        $encomendas = Encomenda::with(['cliente'])
            ->orderByRaw("CASE WHEN etapa IN ('entregue','cancelada') THEN 1 ELSE 0 END")
            ->orderByRaw('data_entrega IS NULL')      // com prazo primeiro
            ->orderBy('data_entrega', 'asc')
            ->latest('id')
            ->get();

        return view('encomendas.index', compact('encomendas'));
    }

    public function create(Request $request)
    {
        $clientes = Cliente::orderBy('nome')->get();
        $clienteId = $request->query('cliente_id');

        return view('encomendas.create', [
            'clientes'  => $clientes,
            'clienteId' => $clienteId,
            'tipos'     => Encomenda::TIPOS,
        ]);
    }

    public function store(Request $request)
    {
        $lojaId = Auth::user()->loja_id;

        $dados = $request->validate([
            'cliente_id'   => ['required', Rule::exists('clientes', 'id')->where('loja_id', $lojaId)],
            'medida_id'    => ['nullable', Rule::exists('medidas', 'id')],
            'titulo'       => ['required', 'string', 'max:150'],
            'descricao'    => ['nullable', 'string'],
            'tipo'         => ['required', Rule::in(array_keys(Encomenda::TIPOS))],
            'data_pedido'  => ['nullable', 'date'],
            'data_prova'   => ['nullable', 'date'],
            'data_entrega' => ['nullable', 'date'],
            'valor'        => ['nullable', 'numeric', 'min:0'],
            'sinal'        => ['nullable', 'numeric', 'min:0'],
            'tecidos'      => ['nullable', 'string'],
            'observacoes'  => ['nullable', 'string'],
            'croqui'       => ['nullable', 'image', 'max:5120'], // até 5MB
        ]);

        // A ficha de medidas escolhida precisa pertencer à cliente (e à loja).
        if (! empty($dados['medida_id'])) {
            $medidaOk = Medida::where('id', $dados['medida_id'])
                ->where('cliente_id', $dados['cliente_id'])
                ->exists();
            if (! $medidaOk) {
                unset($dados['medida_id']);
            }
        }

        $dados['data_pedido'] = $dados['data_pedido'] ?? now()->toDateString();
        $dados['valor'] = $dados['valor'] ?? 0;
        $dados['sinal'] = $dados['sinal'] ?? 0;
        $dados['etapa'] = Encomenda::ETAPA_PEDIDO;

        // Upload do croqui (disco public).
        if ($request->hasFile('croqui')) {
            $dados['croqui_path'] = $request->file('croqui')
                ->store("lojas/{$lojaId}/encomendas", 'public');
        }
        unset($dados['croqui']);

        $encomenda = Encomenda::create($dados);

        return redirect()
            ->route('encomendas.show', $encomenda)
            ->with('success', 'Encomenda criada! Agora acompanhe a produção pela linha do tempo.');
    }

    public function show(Encomenda $encomenda)
    {
        // Histórico de medições DESTA encomenda (uma por prova), recente -> antiga.
        $encomenda->load(['cliente', 'medidas']);

        $historico = $encomenda->medidas; // já ordenado desc pela relação

        // Monta a comparação de cada medição com a imediatamente anterior
        // (cronologicamente), para destacar o que mudou entre as provas.
        $comparacoes = [];
        $ordenadoAsc = $historico->reverse()->values(); // mais antiga -> mais nova
        foreach ($ordenadoAsc as $i => $medicao) {
            $anterior = $i > 0 ? $ordenadoAsc[$i - 1] : null;
            $comparacoes[$medicao->id] = $medicao->compararCom($anterior);
        }

        return view('encomendas.show', [
            'encomenda'   => $encomenda,
            'historico'   => $historico,
            'comparacoes' => $comparacoes,
            'etapas'      => Encomenda::ETAPAS,
        ]);
    }

    /**
     * Registra uma NOVA medição para a encomenda (normalmente após uma prova).
     * Nunca sobrescreve: cada registro entra no histórico para comparação.
     */
    public function registrarMedida(Request $request, Encomenda $encomenda)
    {
        $colunas = array_values(Medida::CAMPOS_FICHA);

        $regras = [
            'data_medicao'        => ['required', 'date'],
            'responsavel_medicao' => ['nullable', 'string', 'max:100'],
            'rotulo'              => ['nullable', 'string', 'max:60'],
            'observacoes'         => ['nullable', 'string'],
        ];
        foreach ($colunas as $col) {
            $regras[$col] = ['nullable', 'string', 'max:20'];
        }

        $dados = $request->validate($regras);

        // Amarra a medição à cliente e à encomenda; loja_id vem da trait.
        $dados['cliente_id']   = $encomenda->cliente_id;
        $dados['encomenda_id'] = $encomenda->id;

        $medida = Medida::create($dados);

        // A encomenda passa a apontar para a medição mais recente.
        $encomenda->update(['medida_id' => $medida->id]);

        return back()->with('success', 'Medição registrada no histórico da encomenda.');
    }

    /**
     * Avança/define a etapa de produção.
     */
    public function updateEtapa(Request $request, Encomenda $encomenda)
    {
        $request->validate([
            'etapa' => ['required', Rule::in(array_keys(Encomenda::ETAPAS))],
        ]);

        $encomenda->update(['etapa' => $request->etapa]);

        return back()->with('success', 'Etapa atualizada para: ' . $encomenda->etapaLabel() . '.');
    }

    /**
     * Edita dados da encomenda (datas, valores, tecidos, medida, croqui, etc.).
     */
    public function update(Request $request, Encomenda $encomenda)
    {
        $lojaId = Auth::user()->loja_id;

        $dados = $request->validate([
            'medida_id'    => ['nullable', Rule::exists('medidas', 'id')],
            'titulo'       => ['required', 'string', 'max:150'],
            'descricao'    => ['nullable', 'string'],
            'tipo'         => ['required', Rule::in(array_keys(Encomenda::TIPOS))],
            'data_prova'   => ['nullable', 'date'],
            'data_entrega' => ['nullable', 'date'],
            'valor'        => ['nullable', 'numeric', 'min:0'],
            'sinal'        => ['nullable', 'numeric', 'min:0'],
            'tecidos'      => ['nullable', 'string'],
            'observacoes'  => ['nullable', 'string'],
            'croqui'       => ['nullable', 'image', 'max:5120'],
        ]);

        if (! empty($dados['medida_id'])) {
            $medidaOk = Medida::where('id', $dados['medida_id'])
                ->where('cliente_id', $encomenda->cliente_id)
                ->exists();
            if (! $medidaOk) {
                unset($dados['medida_id']);
            }
        }

        $dados['valor'] = $dados['valor'] ?? 0;
        $dados['sinal'] = $dados['sinal'] ?? 0;

        if ($request->hasFile('croqui')) {
            // Remove o croqui antigo, se houver.
            if ($encomenda->croqui_path) {
                Storage::disk('public')->delete($encomenda->croqui_path);
            }
            $dados['croqui_path'] = $request->file('croqui')
                ->store("lojas/{$lojaId}/encomendas", 'public');
        }
        unset($dados['croqui']);

        $encomenda->update($dados);

        return back()->with('success', 'Encomenda atualizada.');
    }
}
