<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-slate-800 uppercase tracking-tighter">
                {{ $peca->codigo }} <span class="text-[#fbbf24]">· {{ $peca->nome }}</span>
            </h2>
            <div class="flex items-center gap-4 self-center">
                <a href="{{ route('acervo.edit', $peca) }}" class="text-xs font-bold text-slate-400 hover:text-[#fbbf24] uppercase tracking-widest">✏️ Editar</a>
                <a href="{{ route('acervo.index') }}" class="text-xs font-bold text-slate-400 hover:text-slate-700 uppercase tracking-widest">← Acervo</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Dados da peça -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 flex flex-col sm:flex-row gap-6">
                @php $fotosGaleria = $peca->fotos->map(fn($f) => $f->url)->values(); @endphp
                <div class="shrink-0" x-data="galeriaPeca(@json($fotosGaleria))">
                    {{-- Foto principal (clica para ampliar) --}}
                    <button type="button" @click="if(fotos.length) abrir(indice)"
                            class="block w-40 h-52 rounded-2xl bg-slate-50 border border-slate-200 overflow-hidden {{ $peca->capa_url ? 'cursor-zoom-in' : '' }} flex items-center justify-center">
                        @if($peca->capa_url)
                            <img :src="fotos[indice]" alt="{{ $peca->nome }}" class="w-full h-full object-cover">
                        @else
                            <i class="fas fa-tshirt text-3xl text-slate-300"></i>
                        @endif
                    </button>
                    @if($peca->fotos->count() > 1)
                        <div class="flex gap-2 mt-2 flex-wrap w-40">
                            @foreach($peca->fotos as $i => $foto)
                                <button type="button" @click="indice = {{ $i }}"
                                        :class="indice === {{ $i }} ? 'ring-2 ring-[#fbbf24]' : ''"
                                        class="w-9 h-11 rounded-lg overflow-hidden border border-slate-200 hover:ring-2 hover:ring-[#fbbf24]">
                                    <img src="{{ $foto->url }}" class="w-full h-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    @endif

                    {{-- Lightbox (tela cheia com navegação) --}}
                    <div x-show="aberto" x-cloak class="fixed inset-0 z-[80] flex flex-col" style="background: rgba(0,0,0,.92)"
                         @keydown.escape.window="fechar()" @keydown.arrow-right.window="proxima()" @keydown.arrow-left.window="anterior()">
                        <div class="flex items-center justify-between px-4 py-3 text-white/90 text-sm shrink-0">
                            <span x-text="(indice + 1) + ' de ' + fotos.length"></span>
                            <button @click="fechar()" class="text-3xl leading-none">&times;</button>
                        </div>
                        <div class="flex-1 flex items-center justify-center relative overflow-hidden select-none"
                             @touchstart="toqueInicio($event)" @touchend="toqueFim($event)">
                            <button @click="anterior()" x-show="fotos.length > 1" class="hidden md:flex absolute left-3 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-white/15 hover:bg-white/25 text-white text-2xl items-center justify-center">‹</button>
                            <img :src="fotos[indice]" draggable="false" class="max-w-full max-h-[85vh] object-contain rounded-lg">
                            <button @click="proxima()" x-show="fotos.length > 1" class="hidden md:flex absolute right-3 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-white/15 hover:bg-white/25 text-white text-2xl items-center justify-center">›</button>
                        </div>
                        <div x-show="fotos.length > 1" class="flex justify-center gap-1.5 pb-5">
                            <template x-for="(f, i) in fotos" :key="i">
                                <button @click="indice = i" class="w-2 h-2 rounded-full transition" :class="i === indice ? 'bg-white' : 'bg-white/30'"></button>
                            </template>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 flex-1">
                    <div><p class="text-[10px] font-black uppercase text-slate-400">Categoria</p><p class="font-bold text-slate-800 text-sm">{{ $peca->categoria }}</p></div>
                    <div><p class="text-[10px] font-black uppercase text-slate-400">Tam / Cor</p><p class="font-bold text-slate-800 text-sm">{{ $peca->tamanho ?? '—' }} / {{ $peca->cor ?? '—' }}</p></div>
                    <div><p class="text-[10px] font-black uppercase text-slate-400">Locação</p><p class="font-bold text-slate-800 text-sm">R$ {{ number_format((float)$peca->valor_locacao, 2, ',', '.') }}</p></div>
                    <div><p class="text-[10px] font-black uppercase text-slate-400">Status</p><span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 uppercase">{{ str_replace('_',' ',$peca->status) }}</span></div>
                </div>
            </div>

            <h3 class="font-black text-slate-800 uppercase text-sm tracking-wider">📖 Histórico da Peça</h3>

            <!-- Locações -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-3">Locações</p>
                <div class="space-y-2">
                    @forelse($peca->locacoes as $l)
                        <a href="{{ route('locacao.show', $l) }}" class="flex justify-between items-center border border-slate-100 rounded-xl p-3 hover:bg-slate-50 transition">
                            <span class="text-sm font-semibold text-slate-700">{{ $l->cliente->nome ?? '—' }}</span>
                            <span class="text-xs text-slate-400">{{ optional($l->data_evento)->format('d/m/Y') }} · {{ str_replace('_',' ',$l->status) }}</span>
                        </a>
                    @empty
                        <p class="text-sm text-slate-400">Nenhuma locação.</p>
                    @endforelse
                </div>
            </div>

            <!-- Provas -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-3">Provas</p>
                <div class="space-y-2">
                    @forelse($peca->provas as $p)
                        <div class="flex justify-between items-center border border-slate-100 rounded-xl p-3">
                            <span class="text-sm font-semibold text-slate-700">{{ $p->cliente->nome ?? '—' }}</span>
                            <span class="text-xs text-slate-400">{{ optional($p->data_prova)->format('d/m/Y') }} · {{ $p->resultado ? str_replace('_',' ',$p->resultado) : $p->status }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400">Nenhuma prova.</p>
                    @endforelse
                </div>
            </div>

            <!-- Serviços -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-3">Ajustes / Lavanderia / Manutenção</p>
                <div class="space-y-2">
                    @forelse($peca->servicos as $s)
                        <div class="flex justify-between items-center border border-slate-100 rounded-xl p-3">
                            <span class="text-sm font-semibold text-slate-700">{{ $s->descricao }} <span class="text-[10px] text-slate-400 uppercase">({{ $s->tipo }})</span></span>
                            <span class="text-xs text-slate-400">{{ optional($s->created_at)->format('d/m/Y') }} · {{ str_replace('_',' ',$s->status) }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400">Nenhum serviço.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

    <script>
        function galeriaPeca(fotos) {
            return {
                fotos: fotos || [],
                indice: 0,
                aberto: false,
                touchX: 0,
                abrir(i) { this.indice = i; this.aberto = true; document.body.style.overflow = 'hidden'; },
                fechar() { this.aberto = false; document.body.style.overflow = ''; },
                proxima() { if (this.fotos.length > 1) this.indice = (this.indice + 1) % this.fotos.length; },
                anterior() { if (this.fotos.length > 1) this.indice = (this.indice - 1 + this.fotos.length) % this.fotos.length; },
                toqueInicio(e) { this.touchX = e.changedTouches[0].screenX; },
                toqueFim(e) {
                    const dx = e.changedTouches[0].screenX - this.touchX;
                    if (Math.abs(dx) < 40) return;
                    if (dx < 0) this.proxima(); else this.anterior();
                },
            }
        }
    </script>
</x-app-layout>
