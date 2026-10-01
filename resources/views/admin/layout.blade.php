<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ \App\Models\Setting::nomeSistema() }} — Painel Master</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="bg-slate-100 min-h-screen">
    <div class="flex min-h-screen" x-data="{ menuAberto: false }">

        <!-- Overlay mobile -->
        <div x-show="menuAberto" x-cloak @click="menuAberto = false" class="fixed inset-0 bg-black/50 z-40 lg:hidden"></div>

        <!-- Sidebar (drawer no mobile) -->
        <aside :class="menuAberto ? 'translate-x-0' : '-translate-x-full'"
               class="fixed lg:static inset-y-0 left-0 w-64 bg-[#0a0f1d] text-white flex flex-col z-50 transform transition-transform duration-200 lg:translate-x-0">
            <div class="p-6 border-b border-gray-800 flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-black">ATELIÊ<span class="text-[#fbbf24]">PRO</span></h1>
                    <p class="text-[10px] uppercase tracking-widest text-[#fbbf24] font-bold mt-1">Painel Master</p>
                </div>
                <button @click="menuAberto = false" class="lg:hidden text-gray-400 hover:text-white text-xl">&times;</button>
            </div>
            <nav class="flex-1 py-4 overflow-y-auto">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('admin.dashboard') || request()->routeIs('admin.lojas.*') ? 'bg-[#1e293b] border-l-4 border-[#fbbf24] text-white' : 'text-gray-400 hover:bg-[#1e293b] hover:text-white' }} transition">
                    <i class="fas fa-store mr-3 w-5"></i> Lojas
                </a>
                <a href="{{ route('admin.cadastros') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('admin.cadastros') ? 'bg-[#1e293b] border-l-4 border-[#fbbf24] text-white' : 'text-gray-400 hover:bg-[#1e293b] hover:text-white' }} transition">
                    <i class="fas fa-bell mr-3 w-5"></i> Novos Cadastros
                </a>
                <a href="{{ route('admin.planos.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('admin.planos.*') ? 'bg-[#1e293b] border-l-4 border-[#fbbf24] text-white' : 'text-gray-400 hover:bg-[#1e293b] hover:text-white' }} transition">
                    <i class="fas fa-tags mr-3 w-5"></i> Planos & Preços
                </a>
                <a href="{{ route('admin.relatorios') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('admin.relatorios*') ? 'bg-[#1e293b] border-l-4 border-[#fbbf24] text-white' : 'text-gray-400 hover:bg-[#1e293b] hover:text-white' }} transition">
                    <i class="fas fa-chart-pie mr-3 w-5"></i> Relatórios
                </a>
                <a href="{{ route('admin.acessos') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('admin.acessos') ? 'bg-[#1e293b] border-l-4 border-[#fbbf24] text-white' : 'text-gray-400 hover:bg-[#1e293b] hover:text-white' }} transition">
                    <i class="fas fa-right-to-bracket mr-3 w-5"></i> Atividade / Acessos
                </a>
                <a href="{{ route('admin.config.edit') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('admin.config.*') ? 'bg-[#1e293b] border-l-4 border-[#fbbf24] text-white' : 'text-gray-400 hover:bg-[#1e293b] hover:text-white' }} transition">
                    <i class="fas fa-gear mr-3 w-5"></i> Configurações
                </a>
            </nav>
            <div class="p-4 border-t border-gray-800 bg-[#070b16]">
                <div class="text-[10px] uppercase tracking-widest text-[#fbbf24] font-black">Super Admin</div>
                <div class="text-sm font-medium">{{ auth()->user()->name }}</div>
                <a href="{{ route('admin.conta') }}" class="inline-block text-xs text-gray-300 hover:text-white font-bold mt-2">
                    <i class="fas fa-user-gear mr-1"></i> Minha Conta
                </a>
                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button type="submit" class="text-xs text-orange-400 hover:text-orange-300 uppercase tracking-wider font-bold">Sair</button>
                </form>
            </div>
        </aside>

        <!-- Conteúdo -->
        <main class="flex-1 overflow-auto w-full">
            <header class="bg-white shadow-sm border-b p-4 sm:p-5 flex items-center gap-3">
                <button @click="menuAberto = true" class="lg:hidden text-slate-600 text-xl"><i class="fas fa-bars"></i></button>
                <h2 class="text-lg sm:text-xl font-bold text-slate-800">@yield('titulo', 'Painel Master')</h2>
            </header>

            <div class="p-4 sm:p-8">
                @if(session('success'))
                    <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl font-medium text-sm">{{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-700 px-6 py-4 rounded-2xl font-medium text-sm">{{ $errors->first() }}</div>
                @endif

                @yield('conteudo')
            </div>
        </main>
    </div>
</body>
</html>
