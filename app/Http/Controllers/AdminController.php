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
            // MRR = receita recorrente mensal (soma do valor efetivo /mês das lojas ativas)
            'faturamento_mensal' => $lojas->where('status', Loja::STATUS_ATIVO)
                ->sum(fn (Loja $l) => $l->valorEfetivo()),
            'novas_no_mes' => $lojas->where('created_at', '>=', now()->startOfMonth())->count(),
            'vencendo' => $lojas->filter(fn (Loja $l) => $l->venceEmBreve())->count(),
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
     * Estende o vencimento da loja em N meses (ação rápida do super_admin).
     */
    public function estenderVencimento(Request $request, Loja $loja)
    {
        $meses = (int) $request->input('meses', 1);
        $meses = max(1, min(24, $meses));

        $base = ($loja->data_vencimento && $loja->data_vencimento->isFuture())
            ? $loja->data_vencimento->copy()
            : now();

        $loja->update([
            'data_vencimento' => $base->addMonths($meses),
            'status' => Loja::STATUS_ATIVO,
        ]);

        return back()->with('success', "Vencimento estendido em {$meses} mês(es).");
    }

    /**
     * "Entrar como" a loja — o super_admin passa a navegar como o admin dela,
     * para dar suporte. Guarda o id original para poder voltar.
     */
    public function entrarComo(Loja $loja)
    {
        $adminDaLoja = User::where('loja_id', $loja->id)
            ->where('role', User::ROLE_ADMIN_LOJA)
            ->first();

        if (!$adminDaLoja) {
            return back()->withErrors(['erro' => 'Esta loja não possui um administrador para personificar.']);
        }

        session(['impersonator_id' => auth()->id()]);
        auth()->login($adminDaLoja);

        return redirect()->route('dashboard')->with('success', 'Você está navegando como ' . $loja->nome_fantasia);
    }

    /**
     * Painel de novos cadastros de loja (para o super_admin acompanhar).
     */
    public function cadastros()
    {
        $cadastros = AccessLog::with('loja')
            ->where('evento', 'nova_loja')
            ->latest()
            ->paginate(30);

        return view('admin.cadastros', compact('cadastros'));
    }

    /**
     * Página "Minha Conta" do super_admin (nome, e-mail e senha).
     */
    public function conta()
    {
        return view('admin.conta', ['user' => auth()->user()]);
    }

    /**
     * Atualiza dados/senha do super_admin.
     */
    public function contaUpdate(\Illuminate\Http\Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', \Illuminate\Validation\Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        }
        $user->save();

        return back()->with('success', 'Sua conta foi atualizada!');
    }

    /**
     * Volta da personificação ("Entrar como") para o super_admin original.
     */
    public function voltarPersonificacao()
    {
        if ($originalId = session('impersonator_id')) {
            auth()->loginUsingId($originalId);
            session()->forget('impersonator_id');
            return redirect()->route('admin.dashboard')->with('success', 'Você voltou ao painel Master.');
        }
        return redirect()->route('dashboard');
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

    /**
     * Relatórios globais do SaaS (super_admin): faturamento, status, planos.
     */
    public function relatorios()
    {
        $dados = $this->dadosRelatorio();
        return view('admin.relatorios', $dados);
    }

    /**
     * Versão de impressão (PDF pelo navegador) do relatório global.
     */
    public function relatoriosImprimir()
    {
        $dados = $this->dadosRelatorio();
        return view('admin.relatorios-imprimir', $dados);
    }

    /**
     * Export CSV (Excel) das lojas.
     */
    public function relatoriosExportar()
    {
        $lojas = Loja::withCount('users')->orderBy('nome_fantasia')->get();

        return response()->streamDownload(function () use ($lojas) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Loja', 'CNPJ/CPF', 'Responsável', 'Plano', 'Valor', 'Status', 'Vencimento', 'Usuários'], ';');
            foreach ($lojas as $l) {
                fputcsv($out, [
                    $l->nome_fantasia,
                    $l->cnpj_cpf,
                    $l->email_responsavel,
                    $l->plano,
                    number_format($l->valorEfetivo(), 2, ',', '.'),
                    $l->status,
                    optional($l->data_vencimento)->format('d/m/Y'),
                    $l->users_count,
                ], ';');
            }
            fclose($out);
        }, 'lojas_' . now()->format('Y-m-d') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * Dados agregados para os relatórios do super_admin.
     */
    private function dadosRelatorio(): array
    {
        $lojas = Loja::withCount('users')->orderBy('nome_fantasia')->get();

        $porStatus = [
            'ativo' => $lojas->where('status', Loja::STATUS_ATIVO)->count(),
            'inadimplente' => $lojas->where('status', Loja::STATUS_INADIMPLENTE)->count(),
            'bloqueado' => $lojas->where('status', Loja::STATUS_BLOQUEADO)->count(),
        ];

        // Receita recorrente (MRR) e receita por plano.
        $porPlano = $lojas->where('status', Loja::STATUS_ATIVO)
            ->groupBy('plano')
            ->map(fn ($grupo) => [
                'lojas' => $grupo->count(),
                'receita' => (float) $grupo->sum(fn (Loja $l) => $l->valorEfetivo()),
            ]);

        $mrr = (float) $lojas->where('status', Loja::STATUS_ATIVO)->sum(fn (Loja $l) => $l->valorEfetivo());

        return [
            'lojas' => $lojas,
            'porStatus' => $porStatus,
            'porPlano' => $porPlano,
            'mrr' => $mrr,
            'novasNoMes' => $lojas->where('created_at', '>=', now()->startOfMonth())->count(),
        ];
    }
}

