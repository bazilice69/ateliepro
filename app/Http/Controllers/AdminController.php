<?php

namespace App\Http\Controllers;

use App\Models\AccessLog;
use App\Models\Acervo;
use App\Models\Cliente;
use App\Models\Locacao;
use App\Models\Loja;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Painel do SUPER ADMIN (dono do SaaS).
 *
 * Aqui o super_admin enxerga TODAS as lojas, seus indicadores, define plano/
 * valor/desconto, controla o status (ativar/bloquear) e vê a atividade/acessos.
 * Protegido pelo middleware 'superadmin'.
 */
class AdminController extends Controller
{
    /**
     * Visão geral do SaaS + lista de lojas com status e indicadores.
     */
    public function dashboard()
    {
        $lojas = Loja::query()
            ->withCount('users')
            ->orderBy('nome_fantasia')
            ->get();

        // Contadores por loja (clientes, peças e locações) — sem o global scope,
        // pois o super_admin vê tudo.
        $clientesPorLoja = Cliente::withoutGlobalScope('loja')
            ->selectRaw('loja_id, count(*) as total')->groupBy('loja_id')->pluck('total', 'loja_id');
        $pecasPorLoja = Acervo::withoutGlobalScope('loja')
            ->selectRaw('loja_id, count(*) as total')->groupBy('loja_id')->pluck('total', 'loja_id');
        $locacoesPorLoja = Locacao::withoutGlobalScope('loja')
            ->selectRaw('loja_id, count(*) as total')->groupBy('loja_id')->pluck('total', 'loja_id');

        $resumo = [
            'total_lojas' => $lojas->count(),
            'ativas' => $lojas->where('status', Loja::STATUS_ATIVO)->count(),
            'inadimplentes' => $lojas->where('status', Loja::STATUS_INADIMPLENTE)->count(),
            'bloqueadas' => $lojas->where('status', Loja::STATUS_BLOQUEADO)->count(),
            'faturamento_mensal' => $lojas->where('status', Loja::STATUS_ATIVO)
                ->sum(fn (Loja $l) => $l->valorEfetivo()),
        ];

        return view('admin.dashboard', compact('lojas', 'resumo', 'clientesPorLoja', 'pecasPorLoja', 'locacoesPorLoja'));
    }

    /**
     * Detalhe de uma loja específica com seus indicadores.
     */
    public function show(Loja $loja)
    {
        $indicadores = [
            'usuarios' => User::where('loja_id', $loja->id)->count(),
            'clientes' => Cliente::withoutGlobalScope('loja')->where('loja_id', $loja->id)->count(),
            'pecas' => Acervo::withoutGlobalScope('loja')->where('loja_id', $loja->id)->count(),
            'locacoes' => Locacao::withoutGlobalScope('loja')->where('loja_id', $loja->id)->count(),
        ];

        $usuarios = User::where('loja_id', $loja->id)->orderBy('name')->get();

        return view('admin.loja', compact('loja', 'indicadores', 'usuarios'));
    }

    /**
     * Atualiza plano/valor/desconto/vencimento/observações da loja.
     */
    public function updatePlano(Request $request, Loja $loja)
    {
        $dados = $request->validate([
            'plano' => ['required', 'string', 'max:100'],
            'valor_mensal' => ['required', 'numeric', 'min:0'],
            'desconto' => ['nullable', 'numeric', 'min:0'],
            'data_vencimento' => ['nullable', 'date'],
            'observacoes_admin' => ['nullable', 'string'],
        ]);

        $loja->update($dados);

        return back()->with('success', 'Plano da loja atualizado com sucesso!');
    }

    /**
     * Altera o status da loja (ativar / inadimplente / bloquear).
     */
    public function updateStatus(Request $request, Loja $loja)
    {
        $dados = $request->validate([
            'status' => ['required', Rule::in([
                Loja::STATUS_ATIVO,
                Loja::STATUS_INADIMPLENTE,
                Loja::STATUS_BLOQUEADO,
            ])],
        ]);

        $loja->update($dados);

        return back()->with('success', 'Status da loja atualizado para: ' . $dados['status']);
    }

    /**
     * Área de Atividade / Acessos — quem entrou e em qual loja.
     */
    public function acessos()
    {
        $logs = AccessLog::with(['user', 'loja'])
            ->latest()
            ->paginate(50);

        return view('admin.acessos', compact('logs'));
    }
}
