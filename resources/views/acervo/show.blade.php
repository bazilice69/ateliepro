<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-slate-800 uppercase tracking-tighter">
                {{ $peca->codigo }} <span class="text-[#fbbf24]">· {{ $peca->nome }}</span>
            </h2>
            <a href="{{ route('acervo.index') }}" class="text-xs font-bold text-slate-400 hover:text-slate-700 uppercase tracking-widest self-center">← Acervo</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Dados da peça -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 flex flex-col sm:flex-row gap-6">
                <div class="w-28 h-28 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-center overflow-hidden shrink-0">
                    @if($peca->foto)
                        <img src="{{ asset('storage/'.$peca->foto) }}" alt="{{ $peca->nome }}" class="w-full h-full object-cover">
                    @else
                        <i class="fas fa-tshirt text-3xl text-slate-300"></i>
                    @endif
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
</x-app-layout>
