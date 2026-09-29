<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AteliêPro - Gestão Inteligente</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="bg-gray-100 font-sans">
    @php
        $lojaAtual = Auth::user()?->loja;
        $papel = match(Auth::user()?->role) {
            \App\Models\User::ROLE_SUPER_ADMIN => 'Super Admin',
            \App\Models\User::ROLE_ADMIN_LOJA => 'Administrador',
            \App\Models\User::ROLE_FUNCIONARIO => 'Funcionário',
            default => '',
        };
        $ehAdmin = auth()->user()?->isAdminLoja() || auth()->user()?->isSuperAdmin();
    @endphp

    <div class="flex h-screen overflow-hidden" x-data="{ menuAberto: false }">

        <!-- Overlay escuro no mobile quando o menu está aberto -->
        <div x-show="menuAberto" x-cloak @click="menuAberto = false"
             x-transition:enter="transition-opacity ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black/50 z-40 lg:hidden"></div>

        <!-- Sidebar: drawer no mobile (fixed + translate), fixa no desktop -->
        <aside :class="menuAberto ? 'translate-x-0' : '-translate-x-full'"
               class="fixed lg:static inset-y-0 left-0 w-64 bg-[#0f172a] text-white flex flex-col z-50 transform transition-transform duration-200 lg:translate-x-0">

            <!-- Cabeçalho do menu: logo da loja OU marca do sistema -->
            <div class="p-5 border-b border-gray-700 flex items-center justify-between">
                <div class="flex items-center gap-2 min-w-0">
                    @if($lojaAtual && $lojaAtual->logo)
                        <img src="{{ asset('storage/'.$lojaAtual->logo) }}" alt="Logo" class="w-9 h-9 rounded-lg object-contain bg-white/10 p-1 shrink-0">
                        <span class="font-bold text-sm truncate">{{ $lojaAtual->nome_fantasia }}</span>
                    @else
                        <i class="fas fa-cut text-[#fbbf24] text-xl"></i>
                        <span class="text-xl font-bold">AteliêPro</span>
                    @endif
                </div>
                <button @click="menuAberto = false" class="lg:hidden text-gray-400 hover:text-white text-xl">&times;</button>
            </div>

            <nav class="flex-1 overflow-y-auto py-4">
                @php
                    $links = [
                        ['dashboard', 'dashboard', 'fa-home', 'Início'],
                        ['agenda.index', 'agenda.index', 'fa-calendar-alt', 'Agenda'],
                        ['acervo.index', 'acervo.index', 'fa-tshirt', 'Acervo/Estoque'],
                        ['clientes.index', 'clientes.index', 'fa-users', 'Clientes'],
                        ['locacao.create', 'locacao.create', 'fa-file-contract', 'Locações'],
                        ['contratos.index', 'contratos.*', 'fa-file-signature', 'Contratos'],
                        ['financeiro.index', 'financeiro.index', 'fa-dollar-sign', 'Financeiro'],
                        ['recorrentes.index', 'recorrentes.*', 'fa-repeat', 'Despesas Fixas'],
                        ['relatorios.index', 'relatorios.*', 'fa-chart-pie', 'Relatórios'],
                        ['assistente.index', 'assistente.*', 'fa-robot', 'Assistente IA'],
                    ];
                @endphp
                @foreach($links as [$rota, $padrao, $icone, $rotulo])
                    <a href="{{ route($rota) }}" class="flex items-center px-6 py-3 {{ request()->routeIs($padrao) ? 'bg-[#1e293b] border-l-4 border-[#fbbf24] text-white' : 'hover:bg-[#1e293b] text-gray-400 hover:text-white' }} transition">
                        <i class="fas {{ $icone }} mr-3 w-5"></i> {{ $rotulo }}
                    </a>
                @endforeach

                @if($ehAdmin)
                    <div class="px-6 pt-4 pb-1 text-[10px] font-black uppercase tracking-widest text-gray-600">Administração</div>
                    <a href="{{ route('funcionarios.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('funcionarios.*') ? 'bg-[#1e293b] border-l-4 border-[#fbbf24] text-white' : 'hover:bg-[#1e293b] text-gray-400 hover:text-white' }} transition">
                        <i class="fas fa-user-tie mr-3 w-5"></i> Equipe
                    </a>
                    <a href="{{ route('loja.config.edit') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('loja.config.*') ? 'bg-[#1e293b] border-l-4 border-[#fbbf24] text-white' : 'hover:bg-[#1e293b] text-gray-400 hover:text-white' }} transition">
                        <i class="fas fa-gear mr-3 w-5"></i> Configurações
                    </a>
                    <a href="{{ route('assinar.escolher') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('assinar.*') || request()->routeIs('checkout.*') ? 'bg-[#1e293b] border-l-4 border-[#fbbf24] text-white' : 'hover:bg-[#1e293b] text-gray-400 hover:text-white' }} transition">
                        <i class="fas fa-crown mr-3 w-5"></i> Assinatura
                    </a>
                @endif
            </nav>

            <!-- Rodapé do menu com loja + usuário -->
            <div class="p-4 border-t border-gray-700 bg-[#0a0f1d]">
                @if($lojaAtual)
                    <div class="text-[10px] uppercase tracking-widest text-[#fbbf24] font-black truncate">{{ $lojaAtual->nome_fantasia }}</div>
                @endif
                <div class="text-sm font-medium">{{ Auth::user()->name ?? 'Usuário' }}</div>
                @if($papel)
                    <div class="text-[11px] text-gray-400">{{ $papel }}</div>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-xs text-orange-400 hover:text-orange-300 mt-1 uppercase tracking-wider font-bold">Sair do Sistema</button>
                </form>
            </div>
        </aside>

        <!-- Conteúdo Principal -->
        <div class="flex-1 flex flex-col overflow-hidden w-full">

            @if(session('impersonator_id'))
                <div class="bg-amber-400 text-slate-900 px-4 py-2 flex justify-between items-center text-xs sm:text-sm font-bold">
                    <span><i class="fas fa-user-secret mr-2"></i> Navegando como esta loja (suporte).</span>
                    <form method="POST" action="{{ route('admin.voltar') }}">
                        @csrf
                        <button class="underline uppercase text-[10px] sm:text-xs tracking-widest">Voltar ao Master</button>
                    </form>
                </div>
            @endif

            @if($lojaAtual && $lojaAtual->emTrial())
                <div class="bg-emerald-500 text-white px-4 py-2 flex justify-between items-center text-xs sm:text-sm font-bold gap-2">
                    <span class="truncate"><i class="fas fa-gift mr-2"></i> Teste grátis — restam <strong>{{ $lojaAtual->diasDeTrial() }} dia(s)</strong>.</span>
                    <a href="{{ route('assinar.escolher') }}" class="bg-white text-emerald-600 px-3 py-1 rounded-lg uppercase text-[10px] sm:text-xs tracking-widest hover:bg-emerald-50 shrink-0">Assinar</a>
                </div>
            @endif

            <!-- Header Superior (com botão hamburguer no mobile) -->
            <header class="bg-white shadow-sm border-b p-4 flex justify-between items-center gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <button @click="menuAberto = true" class="lg:hidden text-slate-600 text-xl shrink-0"><i class="fas fa-bars"></i></button>
                    <div class="min-w-0">
                        <h2 class="text-lg sm:text-xl font-semibold text-gray-800 truncate">Painel de Controle</h2>
                        @if($lojaAtual)
                            <p class="text-xs text-gray-400 font-medium truncate">{{ $lojaAtual->nome_fantasia }}</p>
                        @endif
                    </div>
                </div>
                <a href="{{ route('locacao.create') }}" class="bg-[#fbbf24] hover:bg-yellow-500 text-gray-900 px-3 sm:px-4 py-2 rounded-lg font-bold text-xs sm:text-sm transition shadow-md flex items-center shrink-0">
                    <i class="fas fa-plus sm:mr-2"></i> <span class="hidden sm:inline">Nova Locação</span>
                </a>
            </header>

            @isset($header)
                <div class="bg-white border-b px-4 sm:px-8 py-4">
                    {{ $header }}
                </div>
            @endisset

            <!-- Área da Tela -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-8 bg-slate-50">
                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- Assistente virtual flutuante (Valentina) — em todas as telas da loja -->
    @includeWhen(!(Auth::user()?->isSuperAdmin()), 'partials.assistente-widget')
</body>
</html>
