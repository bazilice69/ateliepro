<?php

namespace App\Http\Controllers;

use App\Models\Acervo;
use App\Models\Cliente;
use App\Models\Locacao;
use App\Models\Prova;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Registro e acompanhamento das provas (escopado por loja).
 */
class ProvaController extends Controller
{
    // Agenda de provas (as próximas primeiro)
    public function index()
    {
        $provas = Prova::with(['cliente', 'acervo'])
            ->orderByRaw("CASE WHEN status = 'agendada' THEN 0 ELSE 1 END")
            ->orderBy('data_prova', 'desc')
            ->get();

        return view('provas.index', compact('provas'));
    }

    public function create(Request $request)
    {
        $clientes = Cliente::orderBy('nome')->get();
        $acervos = Acervo::orderBy('nome')->get();
        $locacaoId = $request->query('locacao_id');
        $clienteId = $request->query('cliente_id');

        return view('provas.create', compact('clientes', 'acervos', 'locacaoId', 'clienteId'));
    }

    public function store(Request $request)
    {
        $lojaId = Auth::user()->loja_id;

        $dados = $request->validate([
            'cliente_id' => ['required', Rule::exists('clientes', 'id')->where('loja_id', $lojaId)],
            'acervo_id' => ['nullable', Rule::exists('acervos', 'id')->where('loja_id', $lojaId)],
            'locacao_id' => ['nullable', Rule::exists('locacaos', 'id')->where('loja_id', $lojaId)],
            'data_prova' => ['required', 'date'],
            'responsavel' => ['nullable', 'string', 'max:100'],
            'observacoes' => ['nullable', 'string'],
        ]);

        $dados['status'] = Prova::STATUS_AGENDADA;
        $prova = Prova::create($dados);

        // Marca a peça como "em prova" (se houver).
        if ($prova->acervo_id) {
            Acervo::where('id', $prova->acervo_id)->update(['status' => Acervo::STATUS_EM_PROVA]);
        }

        return redirect()->to($this->voltarPara($prova))->with('success', 'Prova agendada!');
    }

    // Registra o RESULTADO da prova (realizada, ajustes, próxima prova)
    public function registrar(Request $request, Prova $prova)
    {
        $request->validate([
            'resultado' => ['required', 'in:aprovada,precisa_ajuste'],
            'ajustes_necessarios' => ['nullable', 'string'],
            'proxima_prova' => ['nullable', 'date'],
            'observacoes' => ['nullable', 'string'],
        ]);

        $prova->update([
            'status' => Prova::STATUS_REALIZADA,
            'resultado' => $request->resultado,
            'ajustes_necessarios' => $request->ajustes_necessarios,
            'proxima_prova' => $request->proxima_prova,
            'observacoes' => $request->observacoes,
        ]);

        return back()->with('success', 'Prova registrada! ' . ($request->resultado === 'precisa_ajuste'
            ? 'Você pode abrir um ajuste na fila da oficina.' : 'Peça aprovada. 🎉'));
    }

    public function destroy(Prova $prova)
    {
        $prova->delete();
        return back()->with('success', 'Prova removida.');
    }

    private function voltarPara(Prova $prova): string
    {
        return $prova->locacao_id
            ? route('locacao.show', $prova->locacao_id)
            : route('provas.index');
    }
}
