<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AteliêPro — Painel Master</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-100 min-h-screen flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-[#0a0f1d] text-white flex flex-col">
        <div class="p-6 border-b border-gray-800">
            <h1 class="text-xl font-black">ATELIÊ<span class="text-[#fbbf24]">PRO</span></h1>
            <p class="text-[10px] uppercase tracking-widest text-[#fbbf24] font-bold mt-1">Painel Master</p>
        </div>
        <nav class="flex-1 py-4">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('admin.dashboard') || request()->routeIs('admin.lojas.*') ? 'bg-[#1e293b] border-l-4 border-[#fbbf24] text-white' : 'text-gray-400 hover:bg-[#1e293b] hover:text-white' }} transition">
                <i class="fas fa-store mr-3 w-5"></i> Lojas
            </a>
            <a href="{{ route('admin.acessos') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('admin.acessos') ? 'bg-[#1e293b] border-l-4 border-[#fbbf24] text-white' : 'text-gray-400 hover:bg-[#1e293b] hover:text-white' }} transition">
                <i class="fas fa-right-to-bracket mr-3 w-5"></i> Atividade / Acessos
            </a>
        </nav>
        <div class="p-4 border-t border-gray-800 bg-[#070b16]">
            <div class="text-[10px] uppercase tracking-widest text-[#fbbf24] font-black">Super Admin</div>
            <div class="text-sm font-medium">{{ auth()->user()->name }}</div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-xs text-orange-400 hover:text-orange-300 mt-1 uppercase tracking-wider font-bold">Sair</button>
            </form>
        </div>
    </aside>

    <!-- Conteúdo -->
    <main class="flex-1 overflow-auto">
        <header class="bg-white shadow-sm border-b p-5">
            <h2 class="text-xl font-bold text-slate-800">@yield('titulo', 'Painel Master')</h2>
        </header>

        <div class="p-8">
            @if(session('success'))
                <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl font-medium text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-700 px-6 py-4 rounded-2xl font-medium text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            @yield('conteudo')
        </div>
    </main>
</body>
</html>
