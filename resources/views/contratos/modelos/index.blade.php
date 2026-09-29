<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-slate-800 uppercase tracking-tighter">Modelos de <span class="text-[#fbbf24]">Contrato</span></h2>
            <a href="{{ route('contratos.modelos.create') }}" class="bg-slate-900 text-[#fbbf24] px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition">+ Novo Modelo</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl font-medium text-sm">{{ session('success') }}</div>
            @endif

            <p class="text-sm text-slate-500">Crie aqui os textos padrão dos seus contratos (locação, confecção...). Depois, ao gerar um contrato, o sistema preenche os dados do cliente automaticamente.</p>

            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                            <th class="py-4 pl-6">Título</th>
                            <th class="py-4 text-center">Ativo</th>
                            <th class="py-4 pr-6 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($modelos as $m)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-4 pl-6 font-bold text-slate-800 text-sm">{{ $m->titulo }}</td>
                                <td class="py-4 text-center">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $m->ativo ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-500' }}">{{ $m->ativo ? 'Sim' : 'Não' }}</span>
                                </td>
                                <td class="py-4 pr-6 text-right space-x-3">
                                    <a href="{{ route('contratos.modelos.edit', $m) }}" class="text-xs font-bold text-slate-500 hover:text-slate-900 uppercase">Editar</a>
                                    <form method="POST" action="{{ route('contratos.modelos.destroy', $m) }}" class="inline" onsubmit="return confirm('Remover este modelo?')">
                                        @csrf @method('DELETE')
                                        <button class="text-xs font-bold text-rose-500 hover:text-rose-700 uppercase">Remover</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-12 text-center text-slate-400">Nenhum modelo cadastrado. Crie o primeiro!</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
