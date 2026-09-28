<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-slate-800 leading-tight uppercase tracking-tighter">
                Gestão de <span class="text-[#fbbf24]">Acervo</span>
            </h2>
            <a href="{{ route('produto.create') }}" class="bg-slate-900 text-[#fbbf24] px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition shadow-lg">
                + Nova Peça
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl font-medium text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <!-- TABELA DE PEÇAS -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-[2rem] border border-slate-100">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                            <th class="py-5 pl-8">Código</th>
                            <th class="py-5">Peça</th>
                            <th class="py-5">Categoria</th>
                            <th class="py-5">Tam / Cor</th>
                            <th class="py-5 text-right">Locação</th>
                            <th class="py-5 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($pecas as $peca)
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <td class="py-5 pl-8">
                                    <span class="text-xs font-bold text-slate-400">{{ $peca->codigo }}</span>
                                </td>
                                <td class="py-5">
                                    <span class="font-bold text-slate-800 text-sm">{{ $peca->nome }}</span>
                                </td>
                                <td class="py-5">
                                    <span class="bg-amber-50 text-amber-600 px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider">
                                        {{ $peca->categoria }}
                                    </span>
                                </td>
                                <td class="py-5 text-sm font-medium text-slate-600">
                                    {{ $peca->tamanho ?? '—' }} / {{ $peca->cor ?? '—' }}
                                </td>
                                <td class="py-5 text-right font-bold text-slate-800 text-sm">
                                    R$ {{ number_format((float) $peca->valor_locacao, 2, ',', '.') }}
                                </td>
                                <td class="py-5 text-center">
                                    @php
                                        $status = $peca->status ?? 'disponivel';
                                        $cor = match($status) {
                                            'disponivel' => 'bg-emerald-100 text-emerald-800',
                                            'reservada', 'alugada' => 'bg-amber-100 text-amber-800',
                                            'lavanderia', 'manutencao' => 'bg-blue-100 text-blue-800',
                                            'indisponivel', 'danificada' => 'bg-rose-100 text-rose-800',
                                            default => 'bg-slate-100 text-slate-700',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $cor }}">
                                        {{ ucfirst($status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-16 text-center">
                                    <div class="text-slate-200 mb-4"><i class="fas fa-tshirt text-5xl"></i></div>
                                    <h3 class="text-lg font-black text-slate-700 mb-1">Acervo vazio</h3>
                                    <p class="text-slate-500 font-medium text-sm">Cadastre a primeira peça do seu acervo.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>
