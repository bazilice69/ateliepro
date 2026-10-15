<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            <h2 class="font-black text-2xl text-slate-800 leading-tight uppercase tracking-tighter">
                Gestão de <span class="text-[#fbbf24]">Acervo</span>
            </h2>
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <a href="{{ route('acervo.catalogo') }}" target="_blank" class="flex-1 sm:flex-none text-center bg-white text-slate-800 border border-slate-200 px-5 py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:border-[#fbbf24] transition shadow-sm">
                    📖 Catálogo PDF
                </a>
                <a href="{{ route('produto.create') }}" class="flex-1 sm:flex-none text-center bg-slate-900 text-[#fbbf24] px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition shadow-lg">
                    + Nova Peça
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10" x-data="acervoVitrine()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl font-medium text-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Busca + filtro por categoria --}}
            @if($pecas->isNotEmpty())
                <div class="flex flex-col md:flex-row gap-3 md:items-center md:justify-between">
                    <div class="relative flex-1 max-w-md">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 text-sm"></i>
                        <input type="text" x-model="busca" placeholder="Buscar por código, nome ou cor..."
                               class="w-full pl-11 pr-4 py-3 bg-white border border-slate-200 rounded-2xl text-sm focus:ring-2 focus:ring-[#fbbf24] focus:outline-none">
                    </div>
                    <div class="flex gap-2 overflow-x-auto pb-1">
                        @php $categorias = $pecas->pluck('categoria')->filter()->unique()->sort()->values(); @endphp
                        <button @click="categoria=''" :class="categoria==='' ? 'bg-slate-900 text-white' : 'bg-white text-slate-500 border border-slate-200'" class="text-[11px] font-black uppercase px-3 py-2 rounded-xl whitespace-nowrap transition">Todas</button>
                        @foreach($categorias as $cat)
                            <button @click="categoria='{{ $cat }}'" :class="categoria==='{{ $cat }}' ? 'bg-slate-900 text-white' : 'bg-white text-slate-500 border border-slate-200'" class="text-[11px] font-black uppercase px-3 py-2 rounded-xl whitespace-nowrap transition">{{ $cat }}</button>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- VITRINE DE CARDS --}}
            @if($pecas->isNotEmpty())
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                    @foreach($pecas as $peca)
                        @php
                            $status = $peca->status ?? 'disponivel';
                            $corStatus = match($status) {
                                'disponivel' => 'bg-emerald-500',
                                'reservada', 'alugada' => 'bg-amber-500',
                                'em_prova', 'em_ajuste' => 'bg-orange-500',
                                'lavanderia', 'manutencao' => 'bg-blue-500',
                                default => 'bg-rose-500',
                            };
                            $buscaStr = mb_strtolower(trim($peca->codigo.' '.$peca->nome.' '.$peca->cor));
                        @endphp
                        <div class="group"
                             x-show="(busca==='' || '{{ $buscaStr }}'.includes(busca.toLowerCase())) && (categoria==='' || categoria==='{{ $peca->categoria }}')"
                             x-transition>
                            <a href="{{ route('acervo.show', $peca) }}" class="block bg-white rounded-3xl overflow-hidden border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all">
                                {{-- Foto --}}
                                <div class="relative aspect-[3/4] bg-slate-50 overflow-hidden">
                                    @if($peca->capa_url)
                                        <img src="{{ $peca->capa_url }}" alt="{{ $peca->nome }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center"><i class="fas fa-tshirt text-5xl text-slate-200"></i></div>
                                    @endif
                                    {{-- Status (bolinha + texto) --}}
                                    <span class="absolute top-3 left-3 inline-flex items-center gap-1.5 bg-white/90 backdrop-blur px-2.5 py-1 rounded-full text-[10px] font-black uppercase text-slate-700 shadow-sm">
                                        <span class="w-2 h-2 rounded-full {{ $corStatus }}"></span>{{ str_replace('_',' ',$status) }}
                                    </span>
                                    {{-- Categoria --}}
                                    <span class="absolute top-3 right-3 bg-[#fbbf24] text-black px-2.5 py-1 rounded-full text-[9px] font-black uppercase shadow-sm">{{ $peca->categoria }}</span>
                                </div>
                                {{-- Infos --}}
                                <div class="p-4">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase">{{ $peca->codigo }}</p>
                                    <p class="font-bold text-slate-800 text-sm leading-tight mt-0.5 line-clamp-2" style="min-height:2.4em">{{ $peca->nome }}</p>
                                    <div class="flex items-center justify-between mt-3">
                                        <span class="text-xs font-medium text-slate-500">{{ $peca->tamanho ?? '—' }} · {{ $peca->cor ?? '—' }}</span>
                                        <span class="font-black text-slate-900 text-sm">R$ {{ number_format((float) $peca->valor_locacao, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </a>
                            {{-- Ações rápidas --}}
                            <div class="flex gap-2 mt-2 px-1">
                                <a href="{{ route('acervo.edit', $peca) }}" class="flex-1 text-center text-[11px] font-bold text-slate-500 hover:text-[#fbbf24] uppercase py-1">✏️ Editar</a>
                                <a href="{{ route('acervo.show', $peca) }}" class="flex-1 text-center text-[11px] font-bold text-slate-500 hover:text-slate-900 uppercase py-1">Ver ficha →</a>
                            </div>
                        </div>
                    @endforeach
                </div>

            @else
                <div class="bg-white rounded-[2rem] border border-slate-100 py-20 text-center">
                    <div class="text-slate-200 mb-4"><i class="fas fa-tshirt text-6xl"></i></div>
                    <h3 class="text-lg font-black text-slate-700 mb-1">Acervo vazio</h3>
                    <p class="text-slate-500 font-medium text-sm mb-5">Cadastre a primeira peça do seu acervo.</p>
                    <a href="{{ route('produto.create') }}" class="inline-block bg-slate-900 text-[#fbbf24] px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest">+ Nova Peça</a>
                </div>
            @endif

        </div>
    </div>

    <script>
        function acervoVitrine() {
            return { busca: '', categoria: '' };
        }
    </script>
</x-app-layout>
