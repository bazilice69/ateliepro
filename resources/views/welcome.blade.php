<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AteliêPro - Gestão Inteligente para Ateliês</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 font-sans antialiased text-slate-800 selection:bg-amber-400 selection:text-slate-900">

    <!-- CABEÇALHO / NAVEGAÇÃO -->
    <nav class="absolute top-0 w-full z-50">
        <div class="max-w-7xl mx-auto px-6 py-6 flex justify-between items-center">
            <div class="text-2xl font-black text-slate-900 tracking-tighter flex items-center">
                <i class="fas fa-cut mr-2 text-amber-500"></i> Ateliê<span class="text-emerald-600">Pro</span>
            </div>
            
            <div class="flex items-center gap-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="font-bold text-slate-600 hover:text-slate-900 transition">Acessar Painel</a>
                    @else
                        <a href="{{ route('login') }}" class="font-bold text-sm text-slate-600 hover:text-slate-900 transition">Entrar</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="bg-slate-900 hover:bg-slate-800 text-white px-6 py-2.5 rounded-full font-black text-xs uppercase tracking-widest transition shadow-lg">Cadastre-se</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <!-- SEÇÃO HERO -->
    <section class="pt-32 pb-20 px-6 bg-gradient-to-b from-slate-100 to-slate-50">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-5xl md:text-6xl font-black text-slate-900 tracking-tighter leading-tight mb-6">
                A gestão completa do seu ateliê em um <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-500 to-emerald-600">único lugar.</span>
            </h1>
            <p class="text-lg md:text-xl text-slate-500 font-medium mb-10 max-w-2xl mx-auto">
                Controle de acervo, agenda de provas, gestão financeira e contratos de locação. Automatize sua loja e foque no que realmente importa.
            </p>
        </div>
    </section>

    <!-- SEÇÃO DE PLANOS (PREÇOS) -->
    <section class="py-20 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-black text-slate-900 tracking-tighter uppercase mb-4">Escolha seu Plano</h2>
                <p class="text-slate-500 font-medium">Cancele quando quiser. Sem taxas escondidas.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Plano Mensal -->
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 hover:shadow-xl transition relative flex flex-col">
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-tighter mb-2">Mensal</h3>
                    <div class="text-4xl font-black text-slate-900 mb-6">R$ 89<span class="text-sm text-slate-400 font-medium tracking-normal">/mês</span></div>
                    <ul class="space-y-4 mb-8 text-sm font-medium text-slate-600 flex-1">
                        <li><i class="fas fa-check text-emerald-500 mr-2"></i>Gestão de Locações</li>
                        <li><i class="fas fa-check text-emerald-500 mr-2"></i>Controle Financeiro</li>
                        <li><i class="fas fa-check text-emerald-500 mr-2"></i>Suporte por Email</li>
                    </ul>
                    <button class="w-full bg-slate-100 hover:bg-slate-200 text-slate-800 font-black py-3 rounded-xl transition uppercase tracking-widest text-xs">Assinar Mensal</button>
                </div>

                <!-- Plano Trimestral -->
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 hover:shadow-xl transition relative flex flex-col">
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-tighter mb-2">Trimestral</h3>
                    <div class="text-4xl font-black text-slate-900 mb-6">R$ 239<span class="text-sm text-slate-400 font-medium tracking-normal">/tri</span></div>
                    <ul class="space-y-4 mb-8 text-sm font-medium text-slate-600 flex-1">
                        <li><i class="fas fa-check text-emerald-500 mr-2"></i>Todos os recursos</li>
                        <li><i class="fas fa-check text-emerald-500 mr-2"></i>Economia de 10%</li>
                    </ul>
                    <button class="w-full bg-slate-100 hover:bg-slate-200 text-slate-800 font-black py-3 rounded-xl transition uppercase tracking-widest text-xs">Assinar Trimestral</button>
                </div>

                <!-- Plano Semestral -->
                <div class="bg-slate-900 rounded-3xl p-8 shadow-2xl border border-slate-800 relative flex flex-col transform md:-translate-y-4">
                    <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-amber-400 text-slate-900 font-black text-[10px] uppercase tracking-widest px-4 py-1 rounded-full">Mais Popular</div>
                    <h3 class="text-lg font-black text-white uppercase tracking-tighter mb-2">Semestral</h3>
                    <div class="text-4xl font-black text-white mb-6">R$ 429<span class="text-sm text-slate-400 font-medium tracking-normal">/sem</span></div>
                    <ul class="space-y-4 mb-8 text-sm font-medium text-slate-300 flex-1">
                        <li><i class="fas fa-check text-amber-400 mr-2"></i>Todos os recursos</li>
                        <li><i class="fas fa-check text-amber-400 mr-2"></i>Economia de 20%</li>
                        <li><i class="fas fa-check text-amber-400 mr-2"></i>Suporte Prioritário</li>
                    </ul>
                    <button class="w-full bg-amber-400 hover:bg-amber-300 text-slate-900 font-black py-3 rounded-xl transition uppercase tracking-widest text-xs shadow-lg">Assinar Semestral</button>
                </div>

                <!-- Plano Anual -->
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 hover:shadow-xl transition relative flex flex-col">
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-tighter mb-2">Anual</h3>
                    <div class="text-4xl font-black text-slate-900 mb-6">R$ 749<span class="text-sm text-slate-400 font-medium tracking-normal">/ano</span></div>
                    <ul class="space-y-4 mb-8 text-sm font-medium text-slate-600 flex-1">
                        <li><i class="fas fa-check text-emerald-500 mr-2"></i>Todos os recursos</li>
                        <li><i class="fas fa-check text-emerald-500 mr-2"></i>Economia de 30%</li>
                        <li><i class="fas fa-check text-emerald-500 mr-2"></i>Treinamento VIP</li>
                    </ul>
                    <button class="w-full bg-slate-100 hover:bg-slate-200 text-slate-800 font-black py-3 rounded-xl transition uppercase tracking-widest text-xs">Assinar Anual</button>
                </div>
            </div>
        </div>
    </section>

</body>
</html>