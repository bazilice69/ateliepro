<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-slate-800 uppercase tracking-tighter">Locações</h2>
            <a href="{{ route('locacao.create') }}" class="bg-slate-900 text-[#fbbf24] px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition">+ Nova Locação</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl font-medium text-sm">{{ session('success') }}</div>
            @endif

            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-x-auto">
                <table class="w-full min-w-[700px] text-left">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                            <th class="py-4 pl-6">Cliente</th>
                            <th class="py-4">Peça</th>
                            <th class="py-4">Evento</th>
                            <th class="py-4 text-right">Saldo</th>
                            <th class="py-4 text-center">Status</th>
                            <th class="py-4 pr-6 text-right"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($locacoes as $l)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-4 pl-6 font-bold text-slate-800 text-sm">{{ $l->cliente->nome ?? '—' }}</td>
                                <td class="py-4 text-sm text-slate-600">{{ $l->acervo->nome ?? '—' }}</td>
                                <td class="py-4 text-sm text-slate-600">{{ optional($l->data_evento)->format('d/m/Y') }}</td>
                                <td class="py-4 text-right font-bold text-slate-800 text-sm">R$ {{ number_format($l->saldo(), 2, ',', '.') }}</td>
                                <td class="py-4 text-center">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 uppercase">{{ str_replace('_', ' ', $l->status) }}</span>
                                </td>
                                <td class="py-4 pr-6 text-right">
                                    <a href="{{ route('locacao.show', $l) }}" class="text-xs font-bold text-slate-500 hover:text-slate-900 uppercase">Abrir →</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-16 text-center text-slate-400 font-medium">Nenhuma locação ainda. Crie a primeira!</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
