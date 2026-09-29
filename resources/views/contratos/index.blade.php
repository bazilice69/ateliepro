<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-slate-800 uppercase tracking-tighter">Contratos</h2>
            <div class="flex gap-2">
                @if(auth()->user()?->isAdminLoja() || auth()->user()?->isSuperAdmin())
                    <a href="{{ route('contratos.modelos.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-5 py-3 rounded-xl font-black text-xs uppercase tracking-widest transition">Modelos</a>
                @endif
                <a href="{{ route('contratos.create') }}" class="bg-slate-900 text-[#fbbf24] px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition">+ Gerar Contrato</a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl font-medium text-sm">{{ session('success') }}</div>
            @endif

            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                            <th class="py-4 pl-6">Contrato</th>
                            <th class="py-4">Cliente</th>
                            <th class="py-4">Data</th>
                            <th class="py-4 text-center">Status</th>
                            <th class="py-4 pr-6 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($contratos as $c)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-4 pl-6 font-bold text-slate-800 text-sm">{{ $c->titulo }}</td>
                                <td class="py-4 text-sm text-slate-600">{{ $c->cliente->nome ?? '—' }}</td>
                                <td class="py-4 text-sm text-slate-600">{{ optional($c->data_contrato)->format('d/m/Y') }}</td>
                                <td class="py-4 text-center"><span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 uppercase">{{ $c->status }}</span></td>
                                <td class="py-4 pr-6 text-right space-x-3">
                                    <a href="{{ route('contratos.show', $c) }}" class="text-xs font-bold text-slate-500 hover:text-slate-900 uppercase">Abrir</a>
                                    <a href="{{ route('contratos.imprimir', $c) }}" target="_blank" class="text-xs font-bold text-slate-500 hover:text-slate-900 uppercase">Imprimir</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-12 text-center text-slate-400">Nenhum contrato gerado ainda.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
