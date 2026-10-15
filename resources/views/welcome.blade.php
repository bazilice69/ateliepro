<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @php
        $marcaSeo = \App\Models\Setting::nomeSistema();
        $tituloSeo = $marcaSeo.' — Sistema de gestão para ateliês e lojas de noivas';
        $descSeo = 'Software completo para lojas de aluguel e venda de vestidos de noiva e ateliês: acervo, agenda de provas, encomendas sob medida, contratos e financeiro. Teste grátis por 7 dias.';
        // Preview ao compartilhar o link. Se existir um og-image.jpg (recomendado
        // para WhatsApp/Instagram), usa ele; senão cai no SVG versionado.
        $ogImg = file_exists(public_path('og-image.jpg'))
            ? asset('og-image.jpg')
            : asset('og-image.svg');

        // Monta o Schema.org em PHP (evita escrever @context/@type/@if direto no
        // Blade, que o compilador confundiria com diretivas).
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'SoftwareApplication',
            'name' => $marcaSeo,
            'applicationCategory' => 'BusinessApplication',
            'operatingSystem' => 'Web',
            'description' => $descSeo,
            'url' => url('/'),
        ];
        if (isset($planos) && $planos->count()) {
            $schema['offers'] = [
                '@type' => 'AggregateOffer',
                'priceCurrency' => 'BRL',
                'lowPrice' => number_format($planos->min('preco'), 2, '.', ''),
                'highPrice' => number_format($planos->max('preco'), 2, '.', ''),
                'offerCount' => (string) $planos->count(),
            ];
        } else {
            $schema['offers'] = ['@type' => 'AggregateOffer', 'priceCurrency' => 'BRL'];
        }
    @endphp

    <title>{{ $tituloSeo }}</title>
    <meta name="description" content="{{ $descSeo }}">
    <meta name="keywords" content="sistema para loja de noivas, software aluguel de vestidos, gestão de ateliê, programa para loja de vestido de noiva, sistema para ateliê de costura, controle de locação de vestidos">
    <meta name="author" content="{{ $marcaSeo }}">
    <meta name="robots" content="index, follow">
    @php $googleVerify = \App\Models\Setting::get('google_site_verification', 'mktocZSAknL-AHr0zkgTCqhSsBUEq4dLlO0zNPiRtLE'); @endphp
    @if($googleVerify)
        <meta name="google-site-verification" content="{{ $googleVerify }}">
    @endif
    <link rel="canonical" href="{{ url('/') }}">

    {{-- Favicon --}}
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">

    {{-- Open Graph (WhatsApp, Facebook, Instagram) --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $marcaSeo }}">
    <meta property="og:title" content="{{ $tituloSeo }}">
    <meta property="og:description" content="{{ $descSeo }}">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:image" content="{{ $ogImg }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="pt_BR">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $tituloSeo }}">
    <meta name="twitter:description" content="{{ $descSeo }}">
    <meta name="twitter:image" content="{{ $ogImg }}">

    {{-- Dados estruturados (Schema.org) --}}
    <script type="application/ld+json">
    {!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>

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
                <a href="#historia" class="hidden sm:inline font-semibold text-sm text-slate-300 hover:text-ouro transition">Nossa história</a>
                <a href="{{ route('funcionalidades') }}" class="hidden sm:inline font-semibold text-sm text-slate-300 hover:text-ouro transition">Funcionalidades</a>
                @if(isset($depoimentos) && $depoimentos->count())
                    <a href="#depoimentos" class="hidden sm:inline font-semibold text-sm text-slate-300 hover:text-ouro transition">Depoimentos</a>
                @endif
                <a href="#planos" class="hidden sm:inline font-semibold text-sm text-slate-300 hover:text-ouro transition">Planos</a>
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

            <!-- Imagem elegante (alta resolução) -->
            <div class="relative">
                <div class="absolute -inset-4 rounded-[3rem] opacity-30 blur-2xl" style="background: linear-gradient(135deg, #c9a24b, transparent);"></div>
                <img src="https://images.unsplash.com/photo-1594552072238-b8a33785b261?q=80&w=1200&auto=format&fit=crop"
                     alt="Ateliê de alta-costura"
                     class="relative w-full h-[480px] object-cover rounded-[2.5rem] border border-ouro/20 shadow-2xl">
                <!-- selo flutuante -->
                <div class="absolute -bottom-5 -left-5 bg-[#141418] border border-ouro/30 rounded-2xl px-5 py-3 shadow-xl hidden sm:block">
                    <p class="text-ouro serif text-2xl font-bold leading-none">+100</p>
                    <p class="text-[10px] text-slate-400 uppercase tracking-widest mt-1">peças sob controle</p>
                </div>
            </div>
        </div>
    </section>

    <!-- SEÇÃO QUEM SOMOS -->
    <section id="historia" class="py-24 px-6 relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-1/3 left-0 w-[600px] h-[600px] rounded-full blur-[180px] opacity-10" style="background: radial-gradient(circle, #c9a24b, transparent 70%);"></div>
        </div>
        <div class="relative max-w-4xl mx-auto">
            <div class="text-center mb-14">
                <p class="text-ouro text-xs font-bold uppercase tracking-[0.3em] mb-4">Nossa história</p>
                <h2 class="serif text-4xl md:text-5xl font-bold text-white leading-tight">Quem é a {{ $marca }}</h2>
                <p class="text-slate-400 font-medium mt-4 max-w-2xl mx-auto">
                    A {{ $marca }} nasceu de uma história que começou muito antes de existir qualquer sistema.
                </p>
            </div>

            <!-- Números de autoridade -->
            <div class="grid grid-cols-3 gap-4 mb-16">
                <div class="text-center bg-[#141418] border border-slate-800 rounded-3xl p-6">
                    <p class="ouro-gradient serif text-4xl md:text-5xl font-bold">+20</p>
                    <p class="text-[11px] text-slate-400 uppercase tracking-widest mt-1">anos de mercado</p>
                </div>
                <div class="text-center bg-[#141418] border border-slate-800 rounded-3xl p-6">
                    <p class="ouro-gradient serif text-4xl md:text-5xl font-bold">+9 mil</p>
                    <p class="text-[11px] text-slate-400 uppercase tracking-widest mt-1">clientes atendidos</p>
                </div>
                <div class="text-center bg-[#141418] border border-slate-800 rounded-3xl p-6">
                    <p class="ouro-gradient serif text-4xl md:text-5xl font-bold">Brasil</p>
                    <p class="text-[11px] text-slate-400 uppercase tracking-widest mt-1">de ponta a ponta</p>
                </div>
            </div>

            <!-- Texto da história -->
            <div class="space-y-6 text-slate-300 leading-relaxed text-[15px] md:text-base">
                <p><strong class="text-white">Andreia é estilista e está no ramo das noivas desde 2003.</strong> Ao longo de mais de duas décadas, construiu sua experiência dentro do próprio mercado, vivendo de perto a rotina de uma loja física voltada para noivas, vestidos de festa, ternos e eventos.</p>

                <p>Ela conhece esse universo não apenas como profissional, mas como alguém que vive diariamente os desafios e as responsabilidades que existem por trás de cada atendimento. Porque trabalhar com noivas é muito mais do que vender ou alugar uma roupa:</p>

                <ul class="grid sm:grid-cols-2 gap-3 py-2">
                    @foreach(['É ouvir sonhos', 'É entender expectativas', 'É acompanhar medidas, provas e ajustes', 'É cuidar de cada detalhe'] as $item)
                        <li class="flex items-center gap-3 bg-[#141418]/60 border border-slate-800 rounded-xl px-4 py-3 text-sm">
                            <i class="fas fa-heart text-ouro"></i>{{ $item }}
                        </li>
                    @endforeach
                </ul>

                <p>É organizar peças, agendas, contratos, pagamentos, retiradas e devoluções. E é, principalmente, entender que cada cliente está confiando à loja uma parte muito importante da sua história.</p>

                <h3 class="serif text-2xl font-bold text-white pt-6">Foi dessa experiência que nasceu a {{ $marca }}</h3>
                <p>Depois de tantos anos no segmento, Andreia percebeu algo que muitos profissionais conhecem bem: <strong class="text-white">administrar um ateliê ou uma loja pode ser tão desafiador quanto atender bem seus clientes.</strong> Papéis, cadernos, planilhas, mensagens e informações espalhadas transformam uma rotina que deveria ser organizada em uma verdadeira correria.</p>
                <p>Foi então que surgiu a vontade de criar uma ferramenta que tornasse esse dia a dia mais simples. Não para substituir o trabalho de ninguém — <strong class="text-ouro">mas para agregar.</strong></p>

                <h3 class="serif text-2xl font-bold text-white pt-6">Tecnologia para ajudar, não para substituir</h3>
                <p>A {{ $marca }} acredita que a tecnologia deve estar ao lado do profissional, nunca no lugar dele. O conhecimento, a experiência, o olhar da estilista, o atendimento e o carinho continuam sendo insubstituíveis. A tecnologia entra para cuidar daquilo que pode ser organizado, automatizado e centralizado — permitindo que você tenha mais tempo para o que nenhuma tecnologia faz igual: <strong class="text-white">cuidar de pessoas.</strong></p>

                <div class="bg-gradient-to-r from-[#1c1a14] to-[#141418] border border-ouro/30 rounded-2xl p-6 my-4">
                    <p class="text-sm text-slate-400 mb-3">Por isso reunimos num só lugar tudo o que faz parte da rotina da sua loja:</p>
                    <p class="serif text-xl text-white leading-relaxed">Clientes · Medidas · Eventos · Peças · Provas · Ajustes · Agenda · Contratos · Pagamentos · Retiradas · Devoluções · Histórico.</p>
                    <p class="text-ouro text-xs font-bold uppercase tracking-widest mt-3">Tudo conectado e organizado.</p>
                </div>

                <h3 class="serif text-2xl font-bold text-white pt-6">E o preço certo? A gente ajuda com isso também</h3>
                <p>Uma das maiores dúvidas de quem confecciona ou aluga é simples e dolorida: <strong class="text-white">quanto cobrar?</strong> Muita gente trabalha demais e lucra de menos porque chuta o preço, esquece custos ou não considera a margem.</p>
                <p>Por isso a {{ $marca }} tem uma <strong class="text-ouro">calculadora de precificação</strong> feita pra esse mercado. Você informa o custo do material, a mão de obra, as despesas e a margem que deseja — e o sistema sugere o <strong class="text-white">preço justo</strong>, tanto para <strong class="text-white">peças sob medida (confecção)</strong> quanto para <strong class="text-white">peças prontas (venda e locação)</strong>.</p>

                <div class="bg-gradient-to-r from-[#1c1a14] to-[#141418] border border-ouro/30 rounded-2xl p-6 my-4">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-2xl border border-ouro/40 flex items-center justify-center shrink-0"><i class="fas fa-calculator text-ouro text-xl"></i></div>
                        <div>
                            <p class="text-white font-bold">Pare de cobrar no "achismo".</p>
                            <p class="text-sm text-slate-400 mt-1">Material + mão de obra + despesas + a sua margem = o preço que faz você <strong class="text-ouro">lucrar de verdade</strong>, sem medo de perder dinheiro nem de assustar a cliente.</p>
                        </div>
                    </div>
                </div>

                <h3 class="serif text-2xl font-bold text-white pt-6">Nosso sonho</h3>
                <p>O sonho da {{ $marca }} é simples: <strong class="text-white">ajudar quem trabalha realizando o sonho de outras pessoas.</strong> Queremos que você termine o dia sabendo que seus clientes estão organizados, suas peças controladas, sua agenda em ordem e suas informações seguras.</p>
                <p>A {{ $marca }} não quer tirar espaço de ninguém. <strong class="text-ouro">Quer somar.</strong> Não quer substituir a experiência dos profissionais — <strong class="text-ouro">quer potencializá-la.</strong></p>
            </div>

            <!-- Fecho -->
            <div class="text-center mt-16 border-t border-slate-800 pt-12">
                <p class="serif text-3xl md:text-4xl font-bold text-white">{{ $marca }}</p>
                <p class="text-slate-400 mt-2">Mais de 20 anos vivendo o mercado. Agora, transformando experiência em tecnologia.</p>
                <p class="ouro-gradient serif text-2xl font-bold mt-4">Para agregar. Para facilitar. Para evoluir juntos.</p>
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

    <!-- SEÇÃO: VEJA O SISTEMA POR DENTRO (mockups) -->
    <section class="py-20 px-6">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-14">
                <p class="text-ouro text-xs font-bold uppercase tracking-[0.3em] mb-3">Por dentro</p>
                <h2 class="serif text-4xl md:text-5xl font-bold text-white">Veja o sistema por dentro</h2>
                <p class="text-slate-400 font-medium mt-4">Uma prévia de como você vai trabalhar no dia a dia.</p>
            </div>

            @php
                // "Janela de navegador" reutilizável para emoldurar cada tela.
            @endphp

            <div class="space-y-10">
                <!-- MOCKUP 1: DASHBOARD -->
                <div class="rounded-2xl overflow-hidden border border-slate-800 shadow-2xl bg-[#141418]">
                    <div class="flex items-center gap-2 px-4 py-3 bg-[#0f0f12] border-b border-slate-800">
                        <span class="w-3 h-3 rounded-full bg-rose-400"></span>
                        <span class="w-3 h-3 rounded-full bg-amber-400"></span>
                        <span class="w-3 h-3 rounded-full bg-emerald-400"></span>
                        <span class="ml-3 text-[11px] text-slate-500">Painel · {{ $marca }}</span>
                    </div>
                    <div class="p-6 bg-slate-100">
                        <p class="text-[11px] font-black uppercase text-slate-400 tracking-widest mb-4">Hoje no ateliê</p>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
                            @foreach([['Provas marcadas','3','fa-ruler-combined','text-blue-500'],['Locações em aberto','5','fa-handshake','text-emerald-500'],['Para retirar','2','fa-box-open','text-amber-500'],['Ajustes pendentes','4','fa-scissors','text-purple-500']] as [$t,$n,$ic,$c])
                                <div class="bg-white rounded-xl p-4 border border-slate-100">
                                    <div class="flex items-center justify-between">
                                        <span class="text-2xl font-black text-slate-800">{{ $n }}</span>
                                        <i class="fas {{ $ic }} {{ $c }}"></i>
                                    </div>
                                    <p class="text-[10px] font-bold uppercase text-slate-400 mt-1">{{ $t }}</p>
                                </div>
                            @endforeach
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            @foreach([['Recebido no mês','R$ 12.4k','text-emerald-600'],['A receber','R$ 3.8k','text-amber-600'],['Em atraso','R$ 900','text-rose-600'],['Projeção','R$ 16.2k','text-slate-800']] as [$t,$v,$c])
                                <div class="bg-white rounded-xl p-4 border border-slate-100">
                                    <p class="text-lg font-black {{ $c }}">{{ $v }}</p>
                                    <p class="text-[10px] font-bold uppercase text-slate-400">{{ $t }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-10">
                    <!-- MOCKUP 2: AGENDA -->
                    <div class="rounded-2xl overflow-hidden border border-slate-800 shadow-2xl bg-[#141418]">
                        <div class="flex items-center gap-2 px-4 py-3 bg-[#0f0f12] border-b border-slate-800">
                            <span class="w-3 h-3 rounded-full bg-rose-400"></span><span class="w-3 h-3 rounded-full bg-amber-400"></span><span class="w-3 h-3 rounded-full bg-emerald-400"></span>
                            <span class="ml-3 text-[11px] text-slate-500">Agenda</span>
                        </div>
                        <div class="p-5 bg-slate-100">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm font-black text-slate-700 capitalize">Outubro 2026</span>
                                <div class="flex gap-1">
                                    <span class="text-[9px] bg-slate-900 text-white px-2 py-1 rounded">Mês</span>
                                    <span class="text-[9px] bg-white border border-slate-200 text-slate-500 px-2 py-1 rounded">Semana</span>
                                </div>
                            </div>
                            <div class="grid grid-cols-7 gap-1 text-center">
                                @foreach(['D','S','T','Q','Q','S','S'] as $d)<div class="text-[9px] font-bold text-slate-400 py-1">{{ $d }}</div>@endforeach
                                @for($i=1;$i<=28;$i++)
                                    <div class="aspect-square rounded bg-white border border-slate-100 text-[9px] text-slate-400 p-1 relative">
                                        {{ $i }}
                                        @if($i==7)<span class="block mt-0.5 h-1 rounded bg-blue-400"></span>@endif
                                        @if($i==12)<span class="block mt-0.5 h-1 rounded bg-emerald-400"></span>@endif
                                        @if($i==18)<span class="block mt-0.5 h-1 rounded bg-pink-400"></span>@endif
                                        @if($i==21)<span class="block mt-0.5 h-1 rounded bg-purple-400"></span>@endif
                                    </div>
                                @endfor
                            </div>
                            <div class="flex flex-wrap gap-2 mt-3 text-[9px] font-bold text-slate-500">
                                <span><span class="inline-block w-2 h-2 rounded-full bg-blue-400"></span> Provas</span>
                                <span><span class="inline-block w-2 h-2 rounded-full bg-emerald-400"></span> Retiradas</span>
                                <span><span class="inline-block w-2 h-2 rounded-full bg-pink-400"></span> Aniversários</span>
                            </div>
                        </div>
                    </div>

                    <!-- MOCKUP 3: ACERVO (vitrine) -->
                    <div class="rounded-2xl overflow-hidden border border-slate-800 shadow-2xl bg-[#141418]">
                        <div class="flex items-center gap-2 px-4 py-3 bg-[#0f0f12] border-b border-slate-800">
                            <span class="w-3 h-3 rounded-full bg-rose-400"></span><span class="w-3 h-3 rounded-full bg-amber-400"></span><span class="w-3 h-3 rounded-full bg-emerald-400"></span>
                            <span class="ml-3 text-[11px] text-slate-500">Acervo</span>
                        </div>
                        <div class="p-5 bg-slate-100">
                            <div class="grid grid-cols-3 gap-3">
                                @foreach([['Noiva','VN-100','#fdf2f8'],['Festa','VF-210','#eef2ff'],['Plus Size','PS-052','#f0fdf4'],['Madrinha','MD-071','#fef3c7'],['Terno','TM-034','#f1f5f9'],['Noiva','VN-118','#fdf2f8']] as [$cat,$cod,$bg])
                                    <div class="bg-white rounded-xl overflow-hidden border border-slate-100">
                                        <div class="aspect-[3/4] flex items-center justify-center" style="background: {{ $bg }}">
                                            <i class="fas fa-tshirt text-2xl text-slate-300"></i>
                                        </div>
                                        <div class="p-2">
                                            <p class="text-[8px] font-bold uppercase text-amber-600">{{ $cat }}</p>
                                            <p class="text-[9px] font-bold text-slate-700">{{ $cod }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <p class="text-center text-slate-500 text-xs mt-8">Prévia ilustrativa das telas · a interface real é ainda mais completa.</p>
        </div>
    </section>

    <!-- SEÇÃO: O QUE OS LOJISTAS DIZEM (prova social) -->
    @if(isset($depoimentos) && $depoimentos->count())
    <section id="depoimentos" class="py-24 px-6 relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute bottom-0 right-1/4 w-[600px] h-[600px] rounded-full blur-[180px] opacity-10" style="background: radial-gradient(circle, #c9a24b, transparent 70%);"></div>
        </div>
        <div class="relative max-w-6xl mx-auto">
            <div class="text-center mb-14">
                <p class="text-ouro text-xs font-bold uppercase tracking-[0.3em] mb-3">Prova social</p>
                <h2 class="serif text-4xl md:text-5xl font-bold text-white">O que nossos clientes dizem</h2>
                <p class="text-slate-400 font-medium mt-4">Lojistas reais que já usam a {{ $marca }} no dia a dia.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($depoimentos as $dep)
                    <figure class="bg-[#141418] border border-slate-800 rounded-3xl p-7 flex flex-col hover:border-ouro/40 transition">
                        <div class="text-ouro text-2xl mb-3"><i class="fas fa-quote-left opacity-60"></i></div>
                        <blockquote class="text-slate-300 leading-relaxed text-[15px] flex-1">{{ $dep->mensagem }}</blockquote>
                        <figcaption class="mt-6 pt-5 border-t border-slate-800">
                            <p class="font-bold text-white">{{ $dep->autor_nome }}</p>
                            @if($dep->loja_nome)
                                <p class="text-xs text-slate-400">{{ $dep->loja_nome }}</p>
                            @endif
                            @if($dep->linkNormalizado())
                                <a href="{{ $dep->linkNormalizado() }}" target="_blank" rel="noopener"
                                   class="inline-flex items-center gap-1.5 mt-2 text-xs font-bold text-ouro hover:brightness-125 transition">
                                    <i class="fas fa-arrow-up-right-from-square"></i> {{ $dep->linkRotulo() }}
                                </a>
                            @endif
                        </figcaption>
                    </figure>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- SEÇÃO DE PLANOS (PREÇOS) -->
    <section id="planos" class="py-24 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <p class="text-ouro text-xs font-bold uppercase tracking-[0.3em] mb-3">Investimento</p>
                <h2 class="serif text-4xl md:text-5xl font-bold text-white mb-4">Escolha seu plano</h2>
                <p class="text-slate-400 font-medium">Cancele quando quiser. Sem taxas escondidas.</p>
            </div>

            @php
                // Ajusta o grid conforme a quantidade de planos ativos, para os
                // cards ficarem sempre centralizados (não encostados na borda).
                $qtdPlanos = $planos->count();
                $colunasGrid = match (true) {
                    $qtdPlanos <= 1 => 'md:grid-cols-1 max-w-sm',
                    $qtdPlanos === 2 => 'md:grid-cols-2 max-w-3xl',
                    $qtdPlanos === 3 => 'md:grid-cols-3 max-w-5xl',
                    default => 'md:grid-cols-2 lg:grid-cols-4 max-w-7xl',
                };
            @endphp
            <div class="grid grid-cols-1 {{ $colunasGrid }} gap-8 mx-auto justify-center items-stretch">
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
                        <a href="{{ route('checkout.plano', $plano->slug) }}" class="mt-auto w-full text-center font-bold py-3.5 rounded-xl transition uppercase tracking-widest text-xs
                                  {{ $destaque ? 'bg-ouro hover:brightness-110 text-black shadow-lg shadow-[#c9a24b]/20' : 'border border-slate-700 hover:border-ouro text-slate-200' }}">
                            Assinar {{ $plano->nome }}
                        </a>
                    </div>
                @empty
                    <p class="text-center text-slate-500">Planos em breve.</p>
                @endforelse
            </div>

            {{-- CTA WhatsApp: tirar dúvidas antes de assinar --}}
            @php
                $wa = preg_replace('/\D/', '', \App\Models\Setting::get('saas_whatsapp_suporte', '5511957866836'));
                $waMsg = urlencode('Olá! Tenho dúvidas sobre os planos do '.$marca.'. Pode me ajudar?');
            @endphp
            <div class="mt-14 max-w-3xl mx-auto">
                <div class="rounded-3xl bg-gradient-to-r from-[#141418] to-[#1c1a14] border border-ouro/30 p-8 text-center">
                    <h3 class="serif text-2xl md:text-3xl font-bold text-white mb-2">Ainda com dúvidas?</h3>
                    <p class="text-slate-400 font-medium mb-6">Fale comigo pelo WhatsApp antes de assinar — tiro todas as suas dúvidas e te ajudo a escolher o melhor plano. 💬</p>
                    <a href="https://wa.me/{{ $wa }}?text={{ $waMsg }}" target="_blank" rel="noopener"
                       class="inline-flex items-center gap-3 bg-[#25D366] hover:brightness-110 text-black px-8 py-4 rounded-full font-bold text-sm uppercase tracking-widest transition shadow-xl shadow-[#25D366]/20">
                        <i class="fab fa-whatsapp text-xl"></i>
                        Tire suas dúvidas no WhatsApp
                    </a>
                    <p class="text-xs text-slate-500 mt-4">Atendimento direto com quem desenvolve o sistema.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Botão flutuante de WhatsApp (sempre visível) --}}
    @php
        $waFloat = preg_replace('/\D/', '', \App\Models\Setting::get('saas_whatsapp_suporte', '5511957866836'));
        $waFloatMsg = urlencode('Olá! Vi o '.$marca.' e tenho interesse. Pode me explicar?');
    @endphp
    <a href="https://wa.me/{{ $waFloat }}?text={{ $waFloatMsg }}" target="_blank" rel="noopener"
       aria-label="Fale no WhatsApp"
       class="fixed bottom-5 right-5 z-50 flex items-center gap-2 bg-[#25D366] hover:brightness-110 text-black font-bold px-5 py-3.5 rounded-full shadow-2xl shadow-[#25D366]/30 transition">
        <i class="fab fa-whatsapp text-2xl"></i>
        <span class="hidden sm:inline text-sm">Fale comigo</span>
    </a>

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
