<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ \App\Models\Setting::nomeSistema() }} — Gestão premium para ateliês e lojas de noivas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root { --ouro: #c9a24b; --ouro-claro: #e8d9a8; }
        body { font-family: 'Inter', sans-serif; }
        .serif { font-family: 'Cormorant Garamond', serif; }
        .text-ouro { color: var(--ouro); }
        .bg-ouro { background-color: var(--ouro); }
        .border-ouro { border-color: var(--ouro); }
        .ouro-gradient { background: linear-gradient(135deg, var(--ouro-claro), var(--ouro)); -webkit-background-clip: text; background-clip: text; color: transparent; }
        .ring-ouro\/30 { --tw-ring-color: rgba(201,162,75,.3); }
    </style>
</head>
<body class="bg-[#0b0b0d] font-sans antialiased text-slate-200 selection:bg-[#c9a24b] selection:text-black">

    @php $marca = \App\Models\Setting::nomeSistema(); @endphp

    <!-- CABEÇALHO / NAVEGAÇÃO -->
    <nav class="absolute top-0 w-full z-50">
        <div class="max-w-7xl mx-auto px-6 py-6 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-full border border-ouro flex items-center justify-center text-ouro serif text-xl font-bold">B</span>
                <span class="serif text-2xl font-bold tracking-wide text-white">{{ $marca }}</span>
            </div>

            <div class="flex items-center gap-5">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="font-semibold text-sm text-slate-300 hover:text-ouro transition">Acessar Painel</a>
                    @else
                        <a href="{{ route('login') }}" class="font-semibold text-sm text-slate-300 hover:text-ouro transition">Entrar</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="bg-ouro hover:brightness-110 text-black px-6 py-2.5 rounded-full font-bold text-xs uppercase tracking-widest transition shadow-lg shadow-[#c9a24b]/20">Cadastre-se</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <!-- SEÇÃO HERO -->
    <section class="relative pt-36 pb-24 px-6 overflow-hidden">
        <!-- brilho dourado de fundo -->
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-0 right-0 w-[700px] h-[700px] rounded-full blur-[160px] opacity-20" style="background: radial-gradient(circle, #c9a24b, transparent 70%);"></div>
        </div>
        <div class="relative max-w-7xl mx-auto grid lg:grid-cols-2 gap-12 items-center">
            <!-- Texto -->
            <div class="text-center lg:text-left">
                <p class="text-ouro text-xs font-bold uppercase tracking-[0.3em] mb-6">Alta gestão para ateliês de luxo</p>
                <h1 class="serif text-5xl md:text-6xl font-bold text-white leading-[1.05] mb-8">
                    A elegância da sua loja merece uma gestão <span class="ouro-gradient">impecável.</span>
                </h1>
                <p class="text-lg text-slate-400 font-medium mb-10 max-w-xl mx-auto lg:mx-0">
                    Acervo, agenda de provas, encomendas sob medida, contratos e financeiro — tudo em um só lugar, com a sofisticação que o seu ateliê pede.
                </p>
                <div class="flex flex-wrap gap-4 justify-center lg:justify-start">
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="bg-ouro hover:brightness-110 text-black px-9 py-4 rounded-full font-bold text-sm uppercase tracking-widest transition shadow-xl shadow-[#c9a24b]/25">
                            Começar agora
                        </a>
                    @endif
                    <a href="#planos" class="border border-slate-700 hover:border-ouro text-slate-200 px-9 py-4 rounded-full font-bold text-sm uppercase tracking-widest transition">
                        Ver planos
                    </a>
                </div>
            </div>

            <!-- Preview do SISTEMA (somos TI — mostramos o produto, não fotos de ateliê) -->
            <div class="relative">
                <div class="absolute -inset-4 rounded-[3rem] opacity-30 blur-2xl" style="background: linear-gradient(135deg, #c9a24b, transparent);"></div>

                <!-- Mockup de janela do sistema -->
                <div class="relative bg-[#141418] rounded-[2rem] border border-ouro/20 shadow-2xl overflow-hidden">
                    <!-- barra da janela -->
                    <div class="flex items-center gap-2 px-5 py-3 border-b border-slate-800">
                        <span class="w-3 h-3 rounded-full bg-rose-400/70"></span>
                        <span class="w-3 h-3 rounded-full bg-amber-400/70"></span>
                        <span class="w-3 h-3 rounded-full bg-emerald-400/70"></span>
                        <span class="ml-3 text-[10px] text-slate-500 font-mono">painel.bonaccirental</span>
                    </div>
                    <!-- "tela" do dashboard simulada -->
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="h-2.5 w-28 bg-slate-700 rounded-full"></div>
                                <div class="h-2 w-20 bg-slate-800 rounded-full mt-2"></div>
                            </div>
                            <div class="w-8 h-8 rounded-full border border-ouro/40 flex items-center justify-center text-ouro serif text-sm font-bold">B</div>
                        </div>
                        <!-- mini cards operacionais -->
                        <div class="grid grid-cols-3 gap-3">
                            @foreach([['fa-ruler-combined','5','Provas'],['fa-box-open','3','Retirar'],['fa-pen-ruler','2','Produção']] as [$ic,$n,$l])
                                <div class="bg-[#0b0b0d] rounded-xl p-3 border border-slate-800">
                                    <i class="fas {{ $ic }} text-ouro text-xs"></i>
                                    <p class="text-white font-black text-xl mt-1 leading-none">{{ $n }}</p>
                                    <p class="text-[8px] text-slate-500 uppercase tracking-wider mt-1">{{ $l }}</p>
                                </div>
                            @endforeach
                        </div>
                        <!-- barra financeira -->
                        <div class="bg-gradient-to-r from-[#1c1a14] to-[#0b0b0d] rounded-xl p-4 border border-ouro/20">
                            <p class="text-[9px] text-ouro uppercase tracking-widest">Recebido no mês</p>
                            <p class="text-white font-black text-2xl mt-1">R$ 12.480</p>
                            <div class="h-1.5 w-full bg-slate-800 rounded-full mt-3 overflow-hidden">
                                <div class="h-full w-3/4 bg-ouro rounded-full"></div>
                            </div>
                        </div>
                        <!-- linhas de lista -->
                        <div class="space-y-2">
                            @foreach(['w-5/6','w-4/6','w-3/6'] as $w)
                                <div class="flex items-center gap-3">
                                    <div class="w-7 h-7 rounded-lg bg-slate-800 shrink-0"></div>
                                    <div class="h-2 {{ $w }} bg-slate-800 rounded-full"></div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- selo flutuante -->
                <div class="absolute -bottom-5 -left-5 bg-[#141418] border border-ouro/30 rounded-2xl px-5 py-3 shadow-xl hidden sm:block">
                    <p class="text-ouro serif text-2xl font-bold leading-none">100%</p>
                    <p class="text-[10px] text-slate-400 uppercase tracking-widest mt-1">no seu controle</p>
                </div>
            </div>
        </div>
    </section>

    <!-- SEÇÃO DE RECURSOS -->
    <section class="py-16 px-6">
        <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-6">
            @php
                $recursos = [
                    ['fa-gem', 'Acervo Completo', 'Cada peça com fotos, medidas, status e histórico próprio.'],
                    ['fa-calendar-check', 'Agenda de Provas', 'Provas, retiradas e devoluções organizadas por evento.'],
                    ['fa-ruler', 'Sob Medida', 'Histórico de medidas por prova e etapas de produção.'],
                    ['fa-sack-dollar', 'Financeiro', 'Sinais, caução, parcelas e fluxo de caixa sem planilhas.'],
                ];
            @endphp
            @foreach($recursos as [$icone, $titulo, $desc])
                <div class="bg-[#141418] rounded-3xl p-7 border border-slate-800 hover:border-ouro/40 transition group">
                    <div class="w-12 h-12 rounded-2xl border border-ouro/30 flex items-center justify-center mb-5 group-hover:bg-ouro/10 transition">
                        <i class="fas {{ $icone }} text-ouro text-xl"></i>
                    </div>
                    <h3 class="font-bold text-white text-lg mb-1">{{ $titulo }}</h3>
                    <p class="text-sm text-slate-400 font-medium">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <!-- SEÇÃO DE PLANOS (PREÇOS) -->
    <section id="planos" class="py-24 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <p class="text-ouro text-xs font-bold uppercase tracking-[0.3em] mb-3">Investimento</p>
                <h2 class="serif text-4xl md:text-5xl font-bold text-white mb-4">Escolha seu plano</h2>
                <p class="text-slate-400 font-medium">Cancele quando quiser. Sem taxas escondidas.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                @forelse($planos as $plano)
                    @php $destaque = $plano->destaque; @endphp
                    <div class="rounded-3xl p-8 relative flex flex-col transition
                                {{ $destaque
                                    ? 'bg-gradient-to-b from-[#1c1a14] to-[#141418] border border-ouro/50 shadow-2xl shadow-[#c9a24b]/10 md:-translate-y-4'
                                    : 'bg-[#141418] border border-slate-800 hover:border-slate-700' }}">
                        @if($destaque)
                            <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-ouro text-black font-bold text-[10px] uppercase tracking-widest px-4 py-1 rounded-full">Mais Popular</div>
                        @endif
                        <h3 class="serif text-2xl font-bold mb-2 {{ $destaque ? 'text-ouro' : 'text-white' }}">{{ $plano->nome }}</h3>
                        <div class="text-4xl font-black mb-6 text-white">
                            R$ {{ number_format($plano->preco, 0, ',', '.') }}<span class="text-sm text-slate-500 font-medium">{{ $plano->periodo_label }}</span>
                        </div>
                        <ul class="space-y-4 mb-8 text-sm font-medium flex-1 text-slate-300">
                            @foreach(($plano->recursos ?? []) as $rec)
                                <li><i class="fas fa-check mr-2 text-ouro"></i>{{ $rec }}</li>
                            @endforeach
                        </ul>
                        <a href="{{ route('checkout.plano', $plano->slug) }}" class="w-full text-center font-bold py-3.5 rounded-xl transition uppercase tracking-widest text-xs
                                  {{ $destaque ? 'bg-ouro hover:brightness-110 text-black shadow-lg shadow-[#c9a24b]/20' : 'border border-slate-700 hover:border-ouro text-slate-200' }}">
                            Assinar {{ $plano->nome }}
                        </a>
                    </div>
                @empty
                    <p class="col-span-4 text-center text-slate-500">Planos em breve.</p>
                @endforelse
            </div>
        </div>
    </section>

    <!-- RODAPÉ -->
    <footer class="border-t border-slate-800 py-10 px-6">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-full border border-ouro flex items-center justify-center text-ouro serif font-bold">B</span>
                <span class="serif text-xl font-bold text-white">{{ $marca }}</span>
            </div>
            <p class="text-xs text-slate-500">&copy; {{ date('Y') }} {{ $marca }} — Gestão premium para ateliês e lojas de noivas.</p>
        </div>
    </footer>

</body>
</html>
