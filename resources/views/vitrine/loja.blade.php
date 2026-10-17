<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php $marca = \App\Models\Setting::nomeSistema(); @endphp
    <title>{{ $loja->nome_fantasia }} — Acervo · {{ $marca }}</title>
    <meta name="description" content="Conheça o acervo da {{ $loja->nome_fantasia }}{{ $loja->localizacaoLabel() ? ' em '.$loja->localizacaoLabel() : '' }}: vestidos e peças para noivas, festas e eventos. Fale direto com a loja.">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ route('vitrine.loja', $loja) }}">

    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $loja->nome_fantasia }} — Acervo">
    <meta property="og:description" content="Conheça o acervo da {{ $loja->nome_fantasia }}. Fale direto com a loja.">
    @if($pecas->first()?->capa_url)
        <meta property="og:image" content="{{ $pecas->first()->capa_url }}">
    @endif

    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
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
        [x-cloak] { display: none !important; }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-[#0b0b0d] text-slate-200 antialiased">

    <!-- Barra superior -->
    <nav class="border-b border-slate-800/60">
        <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ route('home') }}" class="text-slate-400 hover:text-ouro text-sm font-semibold transition">
                <i class="fas fa-arrow-left mr-1"></i> Voltar
            </a>
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-full border border-ouro flex items-center justify-center text-ouro serif font-bold">B</span>
                <span class="serif text-lg font-bold text-white hidden sm:inline">{{ $marca }}</span>
            </a>
        </div>
    </nav>

    <!-- CABEÇALHO DA LOJA -->
    <header class="relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -top-20 right-1/4 w-[600px] h-[600px] rounded-full blur-[180px] opacity-20" style="background: radial-gradient(circle, #c9a24b, transparent 70%);"></div>
        </div>
        <div class="relative max-w-6xl mx-auto px-6 py-14 text-center">
            <div class="w-24 h-24 mx-auto rounded-3xl bg-white/5 border border-ouro/30 flex items-center justify-center overflow-hidden mb-5">
                @if($loja->logo)
                    <img src="{{ asset('storage/'.$loja->logo) }}" alt="{{ $loja->nome_fantasia }}" class="w-full h-full object-contain p-2">
                @else
                    <span class="serif text-4xl font-bold text-ouro">{{ \Illuminate\Support\Str::of($loja->nome_fantasia)->substr(0,1)->upper() }}</span>
                @endif
            </div>
            <h1 class="serif text-4xl md:text-5xl font-bold text-white">{{ $loja->nome_fantasia }}</h1>
            @if($loja->localizacaoLabel())
                <p class="text-slate-400 font-medium mt-2"><i class="fas fa-location-dot text-ouro/70 mr-1"></i>{{ $loja->localizacaoLabel() }}</p>
            @endif

            <!-- Botões de contato -->
            <div class="flex flex-wrap justify-center gap-3 mt-7">
                @if($loja->whatsappLink())
                    <a href="{{ $loja->whatsappLink() }}" target="_blank" rel="noopener"
                       class="inline-flex items-center gap-2 bg-emerald-500 hover:bg-emerald-400 text-white px-6 py-3 rounded-full font-black text-xs uppercase tracking-widest transition shadow-lg">
                        <i class="fab fa-whatsapp text-base"></i> WhatsApp
                    </a>
                @endif
                @if($loja->instagramLink())
                    <a href="{{ $loja->instagramLink() }}" target="_blank" rel="noopener"
                       class="inline-flex items-center gap-2 bg-gradient-to-r from-[#833ab4] via-[#fd1d1d] to-[#fcb045] text-white px-6 py-3 rounded-full font-black text-xs uppercase tracking-widest transition hover:brightness-110 shadow-lg">
                        <i class="fab fa-instagram text-base"></i> Instagram
                    </a>
                @endif
                @if($loja->siteLink())
                    <a href="{{ $loja->siteLink() }}" target="_blank" rel="noopener"
                       class="inline-flex items-center gap-2 bg-white/5 border border-slate-700 hover:border-ouro text-slate-200 px-6 py-3 rounded-full font-black text-xs uppercase tracking-widest transition">
                        <i class="fas fa-globe text-base"></i> Site
                    </a>
                @endif
            </div>

            <p class="text-[11px] text-slate-500 uppercase tracking-widest mt-6">
                Vitrine de divulgação · o atendimento e a reserva são feitos direto com a loja
            </p>
        </div>
    </header>

    <!-- ACERVO -->
    <main class="max-w-6xl mx-auto px-6 pb-20" x-data="{ aberta: null }">
        <div class="flex items-center gap-3 mb-6">
            <h2 class="serif text-2xl font-bold text-white">Nosso acervo</h2>
            <span class="text-xs text-slate-500 font-bold">{{ $pecas->count() }} {{ $pecas->count() === 1 ? 'peça' : 'peças' }}</span>
        </div>

        @if($pecas->isEmpty())
            <div class="text-center py-20 text-slate-500">
                <i class="fas fa-camera text-4xl mb-3 opacity-50"></i>
                <p class="font-bold">Esta loja ainda não publicou peças com foto.</p>
            </div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($pecas as $peca)
                    <button type="button" @click="aberta = {{ $peca->id }}"
                            class="group text-left bg-[#141418] border border-slate-800 rounded-2xl overflow-hidden hover:border-ouro/50 transition">
                        <div class="relative aspect-[3/4] overflow-hidden bg-[#0f0f12]">
                            <img src="{{ $peca->capa_url }}" alt="{{ $peca->nome }}" loading="lazy"
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            @if($peca->status !== \App\Models\Acervo::STATUS_DISPONIVEL)
                                <span class="absolute top-2 left-2 bg-black/60 text-white/90 text-[9px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wide">Reservada</span>
                            @endif
                        </div>
                        <div class="p-3">
                            <p class="font-bold text-white text-sm truncate">{{ $peca->nome }}</p>
                            <p class="text-[11px] text-slate-400 truncate">
                                {{ \App\Models\Acervo::CATEGORIAS[$peca->categoria] ?? $peca->categoria }}
                                @if($peca->tamanho) · Tam. {{ $peca->tamanho }} @endif
                            </p>
                        </div>
                    </button>

                    <!-- Lightbox da peça (galeria) -->
                    <div x-show="aberta === {{ $peca->id }}" x-cloak
                         class="fixed inset-0 z-50 bg-black/80 backdrop-blur flex items-center justify-center p-4"
                         @click.self="aberta = null" @keydown.escape.window="aberta = null">
                        <div class="bg-[#141418] border border-slate-800 rounded-3xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
                            <div class="flex items-center justify-between p-5 border-b border-slate-800">
                                <div class="min-w-0">
                                    <h3 class="serif text-2xl font-bold text-white truncate">{{ $peca->nome }}</h3>
                                    <p class="text-xs text-slate-400">{{ \App\Models\Acervo::CATEGORIAS[$peca->categoria] ?? $peca->categoria }}@if($peca->tamanho) · Tamanho {{ $peca->tamanho }}@endif @if($peca->cor) · {{ $peca->cor }}@endif</p>
                                </div>
                                <button @click="aberta = null" class="text-slate-400 hover:text-white text-2xl shrink-0 ml-3">&times;</button>
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 p-4">
                                @foreach($peca->fotos as $foto)
                                    <div class="aspect-[3/4] overflow-hidden rounded-xl bg-[#0f0f12]">
                                        <img src="{{ $foto->url }}" alt="{{ $peca->nome }}" loading="lazy" class="w-full h-full object-cover">
                                    </div>
                                @endforeach
                            </div>
                            <div class="p-5 pt-0">
                                @if($loja->whatsappLink())
                                    <a href="{{ $loja->whatsappLink() }}" target="_blank" rel="noopener"
                                       class="w-full inline-flex items-center justify-center gap-2 bg-emerald-500 hover:bg-emerald-400 text-white px-6 py-3.5 rounded-2xl font-black text-sm uppercase tracking-widest transition">
                                        <i class="fab fa-whatsapp text-lg"></i> Gostei! Falar com a loja
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Rodapé -->
        <div class="text-center mt-16 pt-10 border-t border-slate-800">
            <p class="text-slate-500 text-sm">Vitrine powered by</p>
            <a href="{{ route('home') }}" class="serif text-xl font-bold text-ouro">{{ $marca }}</a>
        </div>
    </main>
</body>
</html>
