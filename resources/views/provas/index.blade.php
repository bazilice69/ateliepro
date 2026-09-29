<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-slate-800 uppercase tracking-tighter">Provas</h2>
            <a href="{{ route('provas.create') }}" class="bg-slate-900 text-[#fbbf24] px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition">+ Nova Prova</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl font-medium text-sm">{{ session('success') }}</div>
            @endif

            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-x-auto">
                <table class="w-full min-w-[600px] text-left">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                            <th class="py-4 pl-6">Data</th>
                            <th class="py-4">Cliente</th>
                            <th class="py-4">Peça</th>
                            <th class="py-4 text-center">Status</th>
                            <th class="py-4">Resultado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($provas as $p)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-4 pl-6 text-sm text-slate-600">{{ optional($p->data_prova)->format('d/m/Y H:i') }}</td>
                                <td class="py-4 font-bold text-slate-800 text-sm">{{ $p->cliente->nome ?? '—' }}</td>
                                <td class="py-4 text-sm text-slate-600">{{ $p->acervo->nome ?? '—' }}</td>
                                <td class="py-4 text-center"><span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $p->status === 'agendada' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600' }} uppercase">{{ $p->status }}</span></td>
                                <td class="py-4 text-sm {{ $p->resultado === 'aprovada' ? 'text-emerald-600 font-bold' : 'text-slate-500' }}">{{ $p->resultado ? str_replace('_',' ',$p->resultado) : '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-16 text-center text-slate-400 font-medium">Nenhuma prova registrada.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
