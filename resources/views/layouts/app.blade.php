<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AteliêPro - Gestão Inteligente</title>
    <!-- Tailwind CSS para um visual moderno -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Alpine.js (interatividade: calculadora, dropdowns, etc.) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar (Menu Lateral) - Azul Marinho Premium -->
        <div class="w-64 bg-[#0f172a] text-white flex flex-col">
            <div class="p-6 text-2xl font-bold border-b border-gray-700 flex items-center">
                <i class="fas fa-cut mr-3 text-[#fbbf24]"></i> AteliêPro
            </div>
            
            <nav class="flex-1 overflow-y-auto py-4">
                <!-- Link Início (Dashboard) -->
                <a href="{{ route('dashboard') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('dashboard') ? 'bg-[#1e293b] border-l-4 border-[#fbbf24] text-white' : 'hover:bg-[#1e293b] text-gray-400 hover:text-white' }} transition">
                    <i class="fas fa-home mr-3 w-5"></i> Início
                </a>

                <!-- Link Agenda Master -->
                <a href="{{ route('agenda.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('agenda.index') ? 'bg-[#1e293b] border-l-4 border-[#fbbf24] text-white' : 'hover:bg-[#1e293b] text-gray-400 hover:text-white' }} transition">
                    <i class="fas fa-calendar-alt mr-3 w-5"></i> Agenda
                </a>

                <!-- Link Acervo Conectado -->
                <a href="{{ route('acervo.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('acervo.index') ? 'bg-[#1e293b] border-l-4 border-[#fbbf24] text-white' : 'hover:bg-[#1e293b] text-gray-400 hover:text-white' }} transition">
                    <i class="fas fa-tshirt mr-3 w-5"></i> Acervo/Estoque
                </a>

                <!-- Link Clientes Conectado -->
                <a href="{{ route('clientes.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('clientes.index') ? 'bg-[#1e293b] border-l-4 border-[#fbbf24] text-white' : 'hover:bg-[#1e293b] text-gray-400 hover:text-white' }} transition">
                    <i class="fas fa-users mr-3 w-5"></i> Clientes
                </a>

                <!-- Link Locações Conectado -->
                <a href="{{ route('locacao.create') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('locacao.create') ? 'bg-[#1e293b] border-l-4 border-[#fbbf24] text-white' : 'hover:bg-[#1e293b] text-gray-400 hover:text-white' }} transition">
                    <i class="fas fa-file-contract mr-3 w-5"></i> Locações
                </a>

                <!-- Contratos -->
                <a href="{{ route('contratos.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('contratos.*') ? 'bg-[#1e293b] border-l-4 border-[#fbbf24] text-white' : 'hover:bg-[#1e293b] text-gray-400 hover:text-white' }} transition">
                    <i class="fas fa-file-signature mr-3 w-5"></i> Contratos
                </a>

                <!-- Link Financeiro Conectado (Agora funcionando perfeitamente!) -->
                <a href="{{ route('financeiro.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('financeiro.index') ? 'bg-[#1e293b] border-l-4 border-[#fbbf24] text-white' : 'hover:bg-[#1e293b] text-gray-400 hover:text-white' }} transition">
                    <i class="fas fa-dollar-sign mr-3 w-5"></i> Financeiro
                </a>

                <!-- Despesas Fixas -->
                <a href="{{ route('recorrentes.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('recorrentes.*') ? 'bg-[#1e293b] border-l-4 border-[#fbbf24] text-white' : 'hover:bg-[#1e293b] text-gray-400 hover:text-white' }} transition">
                    <i class="fas fa-repeat mr-3 w-5"></i> Despesas Fixas
                </a>

                <!-- Relatórios -->
                <a href="{{ route('relatorios.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('relatorios.*') ? 'bg-[#1e293b] border-l-4 border-[#fbbf24] text-white' : 'hover:bg-[#1e293b] text-gray-400 hover:text-white' }} transition">
                    <i class="fas fa-chart-pie mr-3 w-5"></i> Relatórios
                </a>

                <!-- Equipe (apenas admin da loja) -->
                @if(auth()->user()?->isAdminLoja() || auth()->user()?->isSuperAdmin())
                    <a href="{{ route('funcionarios.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('funcionarios.*') ? 'bg-[#1e293b] border-l-4 border-[#fbbf24] text-white' : 'hover:bg-[#1e293b] text-gray-400 hover:text-white' }} transition">
                        <i class="fas fa-user-tie mr-3 w-5"></i> Equipe
                    </a>
                    <a href="{{ route('assinar.escolher') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('assinar.*') || request()->routeIs('checkout.*') ? 'bg-[#1e293b] border-l-4 border-[#fbbf24] text-white' : 'hover:bg-[#1e293b] text-gray-400 hover:text-white' }} transition">
                        <i class="fas fa-crown mr-3 w-5"></i> Assinatura
                    </a>
                @endif
            </nav>

            <!-- Rodapé do Menu com Loja + Usuário -->
            <div class="p-4 border-t border-gray-700 bg-[#0a0f1d]">
                @php
                    $lojaAtual = Auth::user()?->loja;
                    $papel = match(Auth::user()?->role) {
                        \App\Models\User::ROLE_SUPER_ADMIN => 'Super Admin',
                        \App\Models\User::ROLE_ADMIN_LOJA => 'Administrador',
                        \App\Models\User::ROLE_FUNCIONARIO => 'Funcionário',
                        default => '',
                    };
                @endphp
                @if($lojaAtual)
                    <div class="text-[10px] uppercase tracking-widest text-[#fbbf24] font-black">{{ $lojaAtual->nome_fantasia }}</div>
                @endif
                <div class="text-sm font-medium">{{ Auth::user()->name ?? 'Usuário' }}</div>
                @if($papel)
                    <div class="text-[11px] text-gray-400">{{ $papel }}</div>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-xs text-orange-400 hover:text-orange-300 mt-1 uppercase tracking-wider font-bold">
                        Sair do Sistema
                    </button>
                </form>
            </div>
        </div>

        <!-- Conteúdo Principal -->
        <div class="flex-1 flex flex-col overflow-hidden">
            @if(session('impersonator_id'))
                <div class="bg-amber-400 text-slate-900 px-6 py-2 flex justify-between items-center text-sm font-bold">
                    <span><i class="fas fa-user-secret mr-2"></i> Você está navegando como esta loja (modo suporte).</span>
                    <form method="POST" action="{{ route('admin.voltar') }}">
                        @csrf
                        <button class="underline uppercase text-xs tracking-widest">Voltar ao Painel Master</button>
                    </form>
                </div>
            @endif

            @php $lojaBanner = Auth::user()?->loja; @endphp
            @if($lojaBanner && $lojaBanner->emTrial())
                <div class="bg-emerald-500 text-white px-6 py-2 flex justify-between items-center text-sm font-bold">
                    <span><i class="fas fa-gift mr-2"></i> Você está no teste grátis — restam <strong>{{ $lojaBanner->diasDeTrial() }} dia(s)</strong>.</span>
                    <a href="{{ route('assinar.escolher') }}" class="bg-white text-emerald-600 px-4 py-1 rounded-lg uppercase text-xs tracking-widest hover:bg-emerald-50">Assinar agora</a>
                </div>
            @endif
            <!-- Header Superior -->
            <header class="bg-white shadow-sm border-b p-4 flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">Painel de Controle</h2>
                    @if(Auth::user()?->loja)
                        <p class="text-xs text-gray-400 font-medium">{{ Auth::user()->loja->nome_fantasia }}</p>
                    @endif
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('locacao.create') }}" class="bg-[#fbbf24] hover:bg-yellow-500 text-gray-900 px-4 py-2 rounded-lg font-bold text-sm transition shadow-md flex items-center">
                        <i class="fas fa-plus mr-2"></i> Nova Locação
                    </a>
                </div>
            </header>

            @isset($header)
                <div class="bg-white border-b px-8 py-4">
                    {{ $header }}
                </div>
            @endisset

            <!-- Área da Tela -->
            <main class="flex-1 overflow-y-auto p-8 bg-slate-50">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>