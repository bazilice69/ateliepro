<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Escolha seu Plano - {{ \App\Models\Setting::nomeSistema() }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 font-sans min-h-screen">
    <div class="max-w-6xl mx-auto px-6 py-12">
        <div class="text-center mb-4">
            <div class="text-2xl font-black text-slate-900 tracking-tighter flex items-center justify-center mb-6">
                <i class="fas fa-cut mr-2 text-amber-500"></i> {{ \App\Models\Setting::nomeSistema() }}
            </div>
            @if(session('status'))
                <div class="inline-block bg-amber-100 text-amber-800 px-6 py-3 rounded-2xl font-semibold text-sm mb-4">
                    {{ session('status') }}
                </div>
            @endif
            <h1 class="text-3xl font-black text-slate-900 tracking-tighter uppercase">Escolha seu Plano</h1>
            @if($loja)
                <p class="text-slate-500 font-medium mt-1">
                    {{ $loja->nome_fantasia }}
                    @if($loja->emTrial())
                        — <span class="text-amber-600 font-bold">{{ $loja->diasDeTrial() }} dia(s) de teste restantes</span>
                    @endif
                </p>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-10">
            @forelse($planos as $plano)
                @php $destaque = $plano->destaque; @endphp
                <div class="rounded-3xl p-8 relative flex flex-col transition
                            {{ $destaque ? 'bg-slate-900 shadow-2xl md:-translate-y-4' : 'bg-white shadow-sm border border-slate-100 hover:shadow-xl' }}">
                    @if($destaque)
                        <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-amber-400 text-slate-900 font-black text-[10px] uppercase tracking-widest px-4 py-1 rounded-full">Mais Popular</div>
                    @endif
                    <h3 class="text-lg font-black uppercase tracking-tighter mb-2 {{ $destaque ? 'text-white' : 'text-slate-800' }}">{{ $plano->nome }}</h3>
                    <div class="text-4xl font-black mb-6 {{ $destaque ? 'text-white' : 'text-slate-900' }}">
                        R$ {{ number_format($plano->preco, 0, ',', '.') }}<span class="text-sm text-slate-400 font-medium">{{ $plano->periodo_label }}</span>
                    </div>
                    <ul class="space-y-3 mb-8 text-sm font-medium flex-1 {{ $destaque ? 'text-slate-300' : 'text-slate-600' }}">
                        @foreach(($plano->recursos ?? []) as $rec)
                            <li><i class="fas fa-check mr-2 {{ $destaque ? 'text-amber-400' : 'text-emerald-500' }}"></i>{{ $rec }}</li>
                        @endforeach
                    </ul>
                    <a href="{{ route('checkout.plano', $plano->slug) }}" class="w-full text-center font-black py-3 rounded-xl transition uppercase tracking-widest text-xs
                              {{ $destaque ? 'bg-amber-400 hover:bg-amber-300 text-slate-900' : 'bg-slate-100 hover:bg-slate-200 text-slate-800' }}">
                        Assinar
                    </a>
                </div>
            @empty
                <p class="col-span-4 text-center text-slate-400">Nenhum plano disponível no momento.</p>
            @endforelse
        </div>

        {{-- Selos de confiança --}}
        <div class="max-w-2xl mx-auto">
            @include('checkout._selos-confianca')
        </div>

        <div class="text-center mt-10">
            <a href="{{ route('dashboard') }}" class="text-sm text-slate-400 hover:text-slate-600 font-bold">Voltar ao painel</a>
            <form method="POST" action="{{ route('logout') }}" class="inline ml-4">
                @csrf
                <button class="text-sm text-slate-400 hover:text-slate-600 font-bold">Sair</button>
            </form>
        </div>
    </div>
</body>
</html>
