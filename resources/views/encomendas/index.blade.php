<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-black text-2xl text-slate-800 uppercase tracking-tighter">Encomendas <span class="text-[#fbbf24]">Sob Medida</span></h2>
                <p class="text-xs font-medium text-slate-400 mt-1">Peças criadas do zero — modelagem, produção e entrega.</p>
            </div>
            <a href="{{ route('encomendas.create') }}" class="bg-slate-900 text-[#fbbf24] px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition">+ Nova Encomenda</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl font-medium text-sm">{{ session('success') }}</div>
            @endif

            @forelse($encomendas as $e)
                <a href="{{ route('encomendas.show', $e) }}" class="block bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6 hover:shadow-md hover:border-slate-200 transition">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div class="flex items-center gap-4 min-w-0">
                            <div class="w-14 h-14 rounded-2xl bg-slate-900 text-[#fbbf24] flex items-center justify-center text-xl shrink-0">
                                <i class="fas fa-scissors"></i>
                            </div>
                            <div class="min-w-0">
                                <h3 class="font-black text-slate-800 text-lg truncate">{{ $e->titulo }}</h3>
                                <p class="text-sm text-slate-500 font-medium truncate">
                                    <i class="fas fa-user text-slate-300 mr-1"></i> {{ $e->cliente->nome ?? '—' }}
                                    <span class="mx-2 text-slate-200">•</span>
                                    <span class="{{ $e->tipo === 'aluguel' ? 'text-indigo-600' : 'text-emerald-600' }} font-bold uppercase text-[11px] tracking-wide">{{ ucfirst($e->tipo) }}</span>
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-6">
                            <div class="text-right">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Entrega</p>
                                <p class="font-bold text-sm {{ $e->estaAtrasada() ? 'text-rose-600' : 'text-slate-700' }}">
                                    {{ $e->data_entrega ? $e->data_entrega->format('d/m/Y') : '—' }}
                                    @if($e->estaAtrasada())<i class="fas fa-triangle-exclamation ml-1" title="Atrasada"></i>@endif
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Saldo</p>
                                <p class="font-black text-sm text-slate-800">R$ {{ number_format($e->saldo(), 2, ',', '.') }}</p>
                            </div>
                            <span class="px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-wide whitespace-nowrap
                                @if($e->etapa === 'entregue') bg-emerald-100 text-emerald-700
                                @elseif($e->etapa === 'cancelada') bg-rose-100 text-rose-700
                                @elseif($e->etapa === 'pronta') bg-sky-100 text-sky-700
                                @else bg-amber-100 text-amber-800 @endif">
                                {{ $e->etapaLabel() }}
                            </span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 py-16 text-center">
                    <i class="fas fa-scissors text-4xl text-slate-200 mb-4"></i>
                    <h3 class="text-lg font-black text-slate-800 mb-1">Nenhuma encomenda ainda</h3>
                    <p class="text-slate-500 text-sm mb-6">Registre a primeira peça sob medida do ateliê.</p>
                    <a href="{{ route('encomendas.create') }}" class="inline-block bg-slate-900 text-[#fbbf24] px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition">+ Nova Encomenda</a>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
