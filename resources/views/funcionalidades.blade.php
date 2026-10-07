<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @php
        $marca = \App\Models\Setting::nomeSistema();
        $tituloSeo = 'Funcionalidades — '.$marca;
        $descSeo = 'Conheça as funcionalidades do '.$marca.': acervo com fotos, agenda de provas, locações, encomendas sob medida, contratos, financeiro, relatórios e assistente virtual. Tudo em um só lugar.';
        $ogImg = file_exists(public_path('og-image.jpg')) ? asset('og-image.jpg') : asset('og-image.svg');
        $wa = preg_replace('/\D/', '', \App\Models\Setting::get('saas_whatsapp_suporte', '5511957866836'));
    @endphp

    <title>{{ $tituloSeo }}</title>
    <meta name="description" content="{{ $descSeo }}">
    <link rel="canonical" href="{{ route('funcionalidades') }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $marca }}">
    <meta property="og:title" content="{{ $tituloSeo }}">
    <meta property="og:description" content="{{ $descSeo }}">
    <meta property="og:url" content="{{ route('funcionalidades') }}">
    <meta property="og:image" content="{{ $ogImg }}">
    <meta name="twitter:card" content="summary_large_image">

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
    </style>
</head>
<body class="bg-[#0b0b0d] font-sans antialiased text-slate-200 selection:bg-[#c9a24b] selection:text-black">

    <!-- CABEÇALHO -->
    <nav class="absolute top-0 w-full z-50">
        <div class="max-w-7xl mx-auto px-6 py-6 flex justify-between items-center">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-full border border-ouro flex items-center justify-center text-ouro serif text-xl font-bold">B</span>
                <span class="serif text-2xl font-bold tracking-wide text-white">{{ $marca }}</span>
            </a>
            <div class="flex items-center gap-5">
                <a href="{{ route('home') }}" class="hidden sm:inline font-semibold text-sm text-slate-300 hover:text-ouro transition">Início</a>
                <a href="{{ route('home') }}#planos" class="hidden sm:inline font-semibold text-sm text-slate-300 hover:text-ouro transition">Planos</a>
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="font-semibold text-sm text-slate-300 hover:text-ouro transition">Entrar</a>
                @endif
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="bg-ouro hover:brightness-110 text-black px-6 py-2.5 rounded-full font-bold text-xs uppercase tracking-widest transition shadow-lg shadow-[#c9a24b]/20">Testar agora</a>
                @endif
            </div>
        </div>
    </nav>

    <!-- HERO -->
    <section class="relative pt-40 pb-16 px-6 overflow-hidden text-center">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[700px] h-[500px] rounded-full blur-[160px] opacity-20" style="background: radial-gradient(circle, #c9a24b, transparent 70%);"></div>
        </div>
        <div class="relative max-w-3xl mx-auto">
            <p class="text-ouro text-xs font-bold uppercase tracking-[0.3em] mb-5">✦ Funcionalidades ✦</p>
            <h1 class="serif text-4xl md:text-6xl font-bold text-white leading-[1.05] mb-6">
                Sua loja de locação de vestidos <span class="ouro-gradient">pode ser muito maior.</span>
            </h1>
            <p class="text-lg text-slate-400 font-medium">
                Tudo que o seu ateliê precisa para profissionalizar a gestão — do acervo ao financeiro, sem planilhas e sem bagunça.
            </p>
        </div>
    </section>

    <!-- GRADE DE FUNCIONALIDADES -->
    <section class="py-12 px-6">
        <div class="max-w-6xl mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @php
                $features = [
                    ['fa-gem', 'Acervo Completo', 'Cada peça com fotos, medidas, status, caução e histórico próprio — a "vida" de cada vestido.'],
                    ['fa-calendar-check', 'Agenda de Provas', 'Provas, retiradas e devoluções organizadas por evento, com lembretes e visão do dia.'],
                    ['fa-ruler-combined', 'Sob Medida', 'Histórico de medidas por prova e acompanhamento das etapas de produção da peça.'],
                    ['fa-handshake', 'Locações', 'Registre locações em segundos, controle datas, sinal, saldo e devolução sem erro.'],
                    ['fa-scissors', 'Encomendas / Confecção', 'Peças sob medida com prazos, etapas e status — nada se perde no caminho.'],
                    ['fa-file-signature', 'Contratos Personalizados', 'Cadastre o seu modelo de contrato e o sistema preenche os dados do cliente automaticamente.'],
                    ['fa-sack-dollar', 'Financeiro Completo', 'Sinais, caução, parcelas, despesas por categoria e fluxo de caixa — adeus planilhas.'],
                    ['fa-calculator', 'Calculadora de Preços', 'Calcule o preço ideal de locação/venda com custo, mão de obra e margem.'],
                    ['fa-chart-pie', 'Relatórios', 'Saiba exatamente como vai o seu negócio com relatórios claros e imprimíveis.'],
                    ['fa-users-gear', 'Perfis de Acesso', 'Cadastre funcionárias e defina o que cada uma pode ver e fazer no sistema.'],
                    ['fa-image', 'Fotos das Peças e Clientes', 'Suba fotos do acervo, croquis e das clientes — tudo organizado e à mão.'],
                    ['fa-robot', 'Assistente Virtual (Valentina)', 'Uma assistente inteligente que ajuda no dia a dia da loja, direto na tela.'],
                ];
            @endphp
            @foreach($features as [$icone, $titulo, $desc])
                <div class="bg-[#141418] rounded-3xl p-7 border border-slate-800 hover:border-ouro/40 transition group text-center sm:text-left">
                    <div class="w-14 h-14 rounded-2xl border border-ouro/30 flex items-center justify-center mb-5 mx-auto sm:mx-0 group-hover:bg-ouro/10 transition">
                        <i class="fas {{ $icone }} text-ouro text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-white text-lg mb-2">{{ $titulo }}</h3>
                    <p class="text-sm text-slate-400 font-medium leading-relaxed">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <!-- DIFERENCIAIS -->
    <section class="py-16 px-6">
        <div class="max-w-5xl mx-auto">
            <div class="text-center mb-12">
                <p class="text-ouro text-xs font-bold uppercase tracking-[0.3em] mb-3">Por que escolher o {{ $marca }}</p>
                <h2 class="serif text-3xl md:text-4xl font-bold text-white">Feito para quem vive de vestidos</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @php
                    $difs = [
                        ['fa-mobile-screen', 'Funciona no celular', 'Acesse de qualquer lugar — veja a agenda e o financeiro da palma da mão.'],
                        ['fa-shield-halved', 'Seus dados seguros', 'Backup e segurança para você não perder o histórico da sua loja.'],
                        ['fa-headset', 'Suporte de verdade', 'Fale direto no WhatsApp com quem desenvolve o sistema.'],
                    ];
                @endphp
                @foreach($difs as [$icone, $titulo, $desc])
                    <div class="text-center p-6">
                        <div class="w-12 h-12 rounded-full bg-ouro/10 flex items-center justify-center mx-auto mb-4">
                            <i class="fas {{ $icone }} text-ouro text-xl"></i>
                        </div>
                        <h3 class="font-bold text-white mb-2">{{ $titulo }}</h3>
                        <p class="text-sm text-slate-400">{{ $desc }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- CTA FINAL -->
    <section class="py-20 px-6">
        <div class="max-w-3xl mx-auto text-center rounded-3xl bg-gradient-to-b from-[#1c1a14] to-[#141418] border border-ouro/40 p-10">
            <h2 class="serif text-3xl md:text-4xl font-bold text-white mb-3">Pronto para profissionalizar a sua loja?</h2>
            <p class="text-slate-400 font-medium mb-8">Comece agora com 7 dias grátis. Sem cartão, sem compromisso.</p>
            <div class="flex flex-wrap gap-4 justify-center">
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="bg-ouro hover:brightness-110 text-black px-9 py-4 rounded-full font-bold text-sm uppercase tracking-widest transition shadow-xl shadow-[#c9a24b]/25">Testar grátis</a>
                @endif
                <a href="https://wa.me/{{ $wa }}?text={{ urlencode('Olá! Vi as funcionalidades do '.$marca.' e tenho interesse.') }}" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 bg-[#25D366] hover:brightness-110 text-black px-8 py-4 rounded-full font-bold text-sm uppercase tracking-widest transition">
                    <i class="fab fa-whatsapp text-xl"></i> Falar no WhatsApp
                </a>
            </div>
        </div>
    </section>

    <!-- RODAPÉ -->
    <footer class="border-t border-slate-800 py-10 px-6">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-4">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-full border border-ouro flex items-center justify-center text-ouro serif font-bold">B</span>
                <span class="serif text-xl font-bold text-white">{{ $marca }}</span>
            </a>
            <p class="text-xs text-slate-500">&copy; {{ date('Y') }} {{ $marca }} — Gestão premium para ateliês e lojas de noivas.</p>
        </div>
    </footer>

    <!-- Botão flutuante de WhatsApp -->
    <a href="https://wa.me/{{ $wa }}?text={{ urlencode('Olá! Vi o '.$marca.' e tenho interesse. Pode me explicar?') }}" target="_blank" rel="noopener"
       aria-label="Fale no WhatsApp"
       class="fixed bottom-5 right-5 z-50 flex items-center gap-2 bg-[#25D366] hover:brightness-110 text-black font-bold px-5 py-3.5 rounded-full shadow-2xl shadow-[#25D366]/30 transition">
        <i class="fab fa-whatsapp text-2xl"></i>
        <span class="hidden sm:inline text-sm">Fale comigo</span>
    </a>

</body>
</html>
