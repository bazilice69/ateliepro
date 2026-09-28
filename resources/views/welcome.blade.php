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
            <div class="flex flex-wrap gap-4 justify-center">
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="bg-slate-900 hover:bg-slate-800 text-white px-8 py-4 rounded-full font-black text-sm uppercase tracking-widest transition shadow-xl">
                        Começar agora
                    </a>
                @endif
                <a href="#planos" class="bg-white hover:bg-slate-100 text-slate-800 border border-slate-200 px-8 py-4 rounded-full font-black text-sm uppercase tracking-widest transition">
                    Ver planos
                </a>
            </div>
        </div>
    </section>

    <!-- SEÇÃO DE RECURSOS -->
    <section class="py-16 px-6">
        <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-6">
            @php
                $recursos = [
                    ['fa-tshirt', 'Acervo Completo', 'Cada vestido/terno com fotos, medidas, status e histórico próprio.'],
                    ['fa-calendar-check', 'Agenda de Provas', 'Provas, retiradas e devoluções organizadas por sala e evento.'],
                    ['fa-ruler', 'Ficha de Medidas', 'Histórico de medidas por prova — feminino, masculino e infantil.'],
                    ['fa-dollar-sign', 'Financeiro', 'Sinais, caução, parcelas e fluxo de caixa sem planilhas.'],
                ];
            @endphp
            @foreach($recursos as [$icone, $titulo, $desc])
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 hover:shadow-lg transition">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center mb-4">
                        <i class="fas {{ $icone }} text-amber-500 text-xl"></i>
                    </div>
                    <h3 class="font-black text-slate-800 mb-1">{{ $titulo }}</h3>
                    <p class="text-sm text-slate-500 font-medium">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <!-- SEÇÃO DE PLANOS (PREÇOS) -->
    <section id="planos" class="py-20 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-black text-slate-900 tracking-tighter uppercase mb-4">Escolha seu Plano</h2>
                <p class="text-slate-500 font-medium">Cancele quando quiser. Sem taxas escondidas.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                @forelse($planos as $plano)
                    @php $destaque = $plano->destaque; @endphp
                    <div class="rounded-3xl p-8 relative flex flex-col transition
                                {{ $destaque ? 'bg-slate-900 shadow-2xl border border-slate-800 md:-translate-y-4' : 'bg-white shadow-sm border border-slate-100 hover:shadow-xl' }}">
                        @if($destaque)
                            <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-amber-400 text-slate-900 font-black text-[10px] uppercase tracking-widest px-4 py-1 rounded-full">Mais Popular</div>
                        @endif
                        <h3 class="text-lg font-black uppercase tracking-tighter mb-2 {{ $destaque ? 'text-white' : 'text-slate-800' }}">{{ $plano->nome }}</h3>
                        <div class="text-4xl font-black mb-6 {{ $destaque ? 'text-white' : 'text-slate-900' }}">
                            R$ {{ number_format($plano->preco, 0, ',', '.') }}<span class="text-sm text-slate-400 font-medium tracking-normal">{{ $plano->periodo_label }}</span>
                        </div>
                        <ul class="space-y-4 mb-8 text-sm font-medium flex-1 {{ $destaque ? 'text-slate-300' : 'text-slate-600' }}">
                            @foreach(($plano->recursos ?? []) as $rec)
                                <li><i class="fas fa-check mr-2 {{ $destaque ? 'text-amber-400' : 'text-emerald-500' }}"></i>{{ $rec }}</li>
                            @endforeach
                        </ul>
                        <a href="{{ route('checkout.plano', $plano->slug) }}" class="w-full text-center font-black py-3 rounded-xl transition uppercase tracking-widest text-xs
                                  {{ $destaque ? 'bg-amber-400 hover:bg-amber-300 text-slate-900 shadow-lg' : 'bg-slate-100 hover:bg-slate-200 text-slate-800' }}">
                            Assinar {{ $plano->nome }}
                        </a>
                    </div>
                @empty
                    <p class="col-span-4 text-center text-slate-400">Planos em breve.</p>
                @endforelse
            </div>
        </div>
    </section>

    <!-- RODAPÉ -->
    <footer class="bg-slate-900 text-slate-400 py-10 px-6">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="text-xl font-black text-white tracking-tighter flex items-center">
                <i class="fas fa-cut mr-2 text-amber-500"></i> Ateliê<span class="text-emerald-500">Pro</span>
            </div>
            <p class="text-xs">&copy; {{ date('Y') }} AteliêPro — Gestão para ateliês e lojas de noivas.</p>
        </div>
    </footer>

</body>
</html>