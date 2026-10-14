<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-slate-800 uppercase tracking-tighter">Orçamentos</h2>
            <a href="{{ route('orcamentos.create') }}" class="bg-slate-900 text-[#fbbf24] px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition shadow-lg">+ Novo Orçamento</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl text-sm">{{ session('success') }}</div>
            @endif

            @forelse($orcamentos as $o)
                @php
                    $corStatus = match($o->status) {
                        'aceito' => 'bg-emerald-100 text-emerald-700',
                        'enviado' => 'bg-blue-100 text-blue-700',
                        'recusado' => 'bg-rose-100 text-rose-700',
                        default => 'bg-slate-100 text-slate-600',
                    };
                @endphp
                <a href="{{ route('orcamentos.show', $o) }}" class="flex items-center justify-between bg-white rounded-2xl p-5 shadow-sm border border-slate-100 hover:border-[#fbbf24] transition">
                    <div class="min-w-0">
                        <p class="font-bold text-slate-800">{{ $o->cliente?->nome ?? 'Sem cliente' }}</p>
                        <p class="text-xs text-slate-500 truncate">{{ $o->descricao ?: $o->modoLabel() }} @if($o->acervo)· {{ $o->acervo->codigo }}@endif</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">{{ $o->created_at->format('d/m/Y') }}</p>
                    </div>
                    <div class="text-right pl-3">
                        <p class="font-black text-slate-900">R$ {{ number_format((float)$o->valor_final, 2, ',', '.') }}</p>
                        <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $corStatus }}">{{ $o->status }}</span>
                    </div>
                </a>
            @empty
                <div class="bg-white rounded-[2rem] border border-slate-100 py-16 text-center">
                    <div class="text-slate-200 text-5xl mb-3">🧮</div>
                    <h3 class="text-lg font-black text-slate-700 mb-1">Nenhum orçamento ainda</h3>
                    <p class="text-slate-500 text-sm mb-5">Monte o preço certo e envie para o cliente.</p>
                    <a href="{{ route('orcamentos.create') }}" class="inline-block bg-slate-900 text-[#fbbf24] px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest">+ Novo Orçamento</a>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
