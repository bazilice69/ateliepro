<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-slate-800 uppercase tracking-tighter">Oficina</h2>
            <a href="{{ route('servicos.create') }}" class="bg-slate-900 text-[#fbbf24] px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition">+ Novo Serviço</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl font-medium text-sm">{{ session('success') }}</div>
            @endif

            <!-- Filtro por tipo -->
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('servicos.index') }}" class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest {{ !$tipo ? 'bg-slate-900 text-[#fbbf24]' : 'bg-white text-slate-500 border border-slate-200' }}">Todos</a>
                @foreach(['ajuste' => 'Ajustes', 'lavanderia' => 'Lavanderia', 'manutencao' => 'Manutenção'] as $t => $rot)
                    <a href="{{ route('servicos.index', ['tipo' => $t]) }}" class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest {{ $tipo === $t ? 'bg-slate-900 text-[#fbbf24]' : 'bg-white text-slate-500 border border-slate-200' }}">{{ $rot }}</a>
                @endforeach
            </div>

            {{-- Peças marcadas com status de oficina no Acervo mas SEM ordem de serviço aberta --}}
            @if($pecasSemOS->isNotEmpty())
                @php
                    $rotuloStatus = ['em_ajuste' => 'em ajuste', 'lavanderia' => 'na lavanderia', 'manutencao' => 'em manutenção'];
                @endphp
                <div class="bg-amber-50 border border-amber-200 rounded-3xl p-5">
                    <p class="text-xs font-black uppercase tracking-widest text-amber-700 mb-3">⚠️ Peças marcadas no acervo (sem ordem de serviço)</p>
                    <div class="space-y-2">
                        @foreach($pecasSemOS as $p)
                            <div class="flex items-center justify-between bg-white rounded-xl px-4 py-3 border border-amber-100">
                                <div class="min-w-0">
                                    <p class="font-bold text-slate-800 text-sm truncate">{{ $p->codigo }} · {{ $p->nome }}</p>
                                    <p class="text-[11px] text-amber-600 font-bold uppercase">{{ $rotuloStatus[$p->status] ?? $p->status }}</p>
                                </div>
                                <a href="{{ route('servicos.create', ['acervo_id' => $p->id, 'tipo' => $p->status === 'em_ajuste' ? 'ajuste' : $p->status]) }}" class="shrink-0 bg-slate-900 text-[#fbbf24] px-4 py-2 rounded-lg text-[11px] font-black uppercase tracking-widest hover:bg-slate-800 transition">+ Abrir ordem</a>
                            </div>
                        @endforeach
                    </div>
                    <p class="text-[11px] text-amber-600 mt-3">Essas peças aparecem no painel mas ainda não têm uma ordem de serviço formal. Clique em "Abrir ordem" para registrar o trabalho.</p>
                </div>
            @endif

            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-x-auto">
                <table class="w-full min-w-[700px] text-left">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                            <th class="py-4 pl-6">Serviço</th>
                            <th class="py-4">Peça</th>
                            <th class="py-4">Responsável</th>
                            <th class="py-4">Prazo</th>
                            <th class="py-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($servicos as $s)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-4 pl-6">
                                    <p class="font-bold text-slate-800 text-sm">{{ $s->descricao }}</p>
                                    <p class="text-[10px] text-slate-400 uppercase">{{ $s->tipo }}</p>
                                </td>
                                <td class="py-4 text-sm text-slate-600">{{ $s->acervo->nome ?? '—' }}</td>
                                <td class="py-4 text-sm text-slate-600">{{ $s->responsavel ?: '—' }}</td>
                                <td class="py-4 text-sm text-slate-600">{{ optional($s->prazo)->format('d/m/Y') ?? '—' }}</td>
                                <td class="py-4 text-center">
                                    <form method="POST" action="{{ route('servicos.status', $s) }}">
                                        @csrf @method('PUT')
                                        <select name="status" onchange="this.form.submit()" class="p-2 bg-slate-50 border-none rounded-lg text-xs font-bold uppercase">
                                            @foreach(['solicitado','em_andamento','pronto','aprovado','cancelado'] as $st)
                                                <option value="{{ $st }}" @selected($s->status === $st)>{{ str_replace('_',' ',$st) }}</option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-16 text-center text-slate-400 font-medium">Nenhum serviço na fila.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
