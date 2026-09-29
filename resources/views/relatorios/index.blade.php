<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-slate-800 uppercase tracking-tighter">
                Relatórios <span class="text-[#fbbf24]">da Loja</span>
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('relatorios.exportar', request()->only('inicio','fim')) }}" class="bg-emerald-500 hover:bg-emerald-600 text-white px-5 py-3 rounded-xl font-black text-xs uppercase tracking-widest transition">
                    <i class="fas fa-file-excel mr-1"></i> Excel (CSV)
                </a>
                <a href="{{ route('relatorios.imprimir', request()->only('inicio','fim')) }}" target="_blank" class="bg-slate-900 text-[#fbbf24] px-5 py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition">
                    <i class="fas fa-print mr-1"></i> PDF / Imprimir
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ modo: 'simplificado' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Filtros de período + alternância simplificado/detalhado -->
            <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100 flex flex-col md:flex-row gap-4 md:items-end justify-between">
                <form method="GET" action="{{ route('relatorios.index') }}" class="flex flex-wrap gap-4 items-end">
                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400">De</label>
                        <input type="date" name="inicio" value="{{ $inicio->format('Y-m-d') }}" class="block mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400">Até</label>
                        <input type="date" name="fim" value="{{ $fim->format('Y-m-d') }}" class="block mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                    </div>
                    <button class="bg-slate-900 text-[#fbbf24] px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition">Filtrar</button>
                </form>
                <div class="flex gap-2 bg-slate-100 p-1 rounded-xl">
                    <button @click="modo='simplificado'" :class="modo==='simplificado' ? 'bg-white shadow text-slate-800' : 'text-slate-500'" class="px-4 py-2 rounded-lg text-xs font-black uppercase tracking-widest transition">Simplificado</button>
                    <button @click="modo='detalhado'" :class="modo==='detalhado' ? 'bg-white shadow text-slate-800' : 'text-slate-500'" class="px-4 py-2 rounded-lg text-xs font-black uppercase tracking-widest transition">Detalhado</button>
                </div>
            </div>

            <p class="text-sm text-slate-500">Período: <strong>{{ $inicio->format('d/m/Y') }}</strong> até <strong>{{ $fim->format('d/m/Y') }}</strong></p>

            <!-- ===================== SIMPLIFICADO ===================== -->
            <div x-show="modo==='simplificado'" class="space-y-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                        <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Receitas</p>
                        <p class="text-2xl font-black text-emerald-600 mt-1">R$ {{ number_format($financeiro['receitas'], 2, ',', '.') }}</p>
                    </div>
                    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                        <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Despesas</p>
                        <p class="text-2xl font-black text-rose-500 mt-1">R$ {{ number_format($financeiro['despesas'], 2, ',', '.') }}</p>
                    </div>
                    <div class="bg-slate-900 rounded-2xl p-5 shadow-sm">
                        <p class="text-[10px] font-black uppercase text-[#fbbf24] tracking-widest">Saldo</p>
                        <p class="text-2xl font-black text-white mt-1">R$ {{ number_format($financeiro['saldo'], 2, ',', '.') }}</p>
                    </div>
                    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                        <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Em atraso</p>
                        <p class="text-2xl font-black text-amber-600 mt-1">R$ {{ number_format($financeiro['emAtraso'], 2, ',', '.') }}</p>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                        <h3 class="font-black text-slate-800 uppercase text-sm tracking-wider mb-4">Operacional</h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between"><span class="text-slate-500">Locações no período</span><span class="font-black text-slate-800">{{ $operacional['locacoes'] }}</span></div>
                            <div class="flex justify-between"><span class="text-slate-500">Novos clientes</span><span class="font-black text-slate-800">{{ $operacional['novos_clientes'] }}</span></div>
                        </div>
                    </div>
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                        <h3 class="font-black text-slate-800 uppercase text-sm tracking-wider mb-4">Peças mais alugadas</h3>
                        <ul class="space-y-2 text-sm">
                            @forelse($pecasMaisAlugadas as $p)
                                <li class="flex justify-between"><span class="text-slate-600">{{ $p['nome'] }} <span class="text-slate-300">({{ $p['codigo'] }})</span></span><span class="font-black text-slate-800">{{ $p['total'] }}x</span></li>
                            @empty
                                <li class="text-slate-400">Nenhuma locação no período.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <!-- ===================== DETALHADO ===================== -->
            <div x-show="modo==='detalhado'" x-cloak class="space-y-6">
                <!-- Por categoria -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                    <h3 class="font-black text-slate-800 uppercase text-sm tracking-wider mb-4">Por Categoria</h3>
                    <table class="w-full text-left text-sm">
                        <thead><tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                            <th class="py-2">Categoria</th><th class="py-2 text-right">Receita</th><th class="py-2 text-right">Despesa</th>
                        </tr></thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($porCategoria as $nome => $v)
                                <tr>
                                    <td class="py-2 text-slate-700 font-semibold">{{ $nome }}</td>
                                    <td class="py-2 text-right text-emerald-600 font-bold">R$ {{ number_format($v['receita'], 2, ',', '.') }}</td>
                                    <td class="py-2 text-right text-rose-500 font-bold">R$ {{ number_format($v['despesa'], 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Lançamentos detalhados -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                    <h3 class="font-black text-slate-800 uppercase text-sm tracking-wider mb-4">Lançamentos ({{ $lancamentos->count() }})</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead><tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                                <th class="py-2">Data</th><th class="py-2">Descrição</th><th class="py-2">Categoria</th><th class="py-2 text-center">Status</th><th class="py-2 text-right">Valor</th>
                            </tr></thead>
                            <tbody class="divide-y divide-slate-50">
                                @forelse($lancamentos as $l)
                                    <tr>
                                        <td class="py-2 text-slate-600">{{ optional($l->data_vencimento)->format('d/m/Y') }}</td>
                                        <td class="py-2 text-slate-800 font-semibold">{{ $l->descricao }}</td>
                                        <td class="py-2 text-slate-500">{{ $l->categoria->nome ?? '—' }}</td>
                                        <td class="py-2 text-center"><span class="text-[10px] font-bold uppercase">{{ $l->status }}</span></td>
                                        <td class="py-2 text-right font-black {{ $l->tipo === 'Receita' ? 'text-emerald-600' : 'text-rose-500' }}">{{ $l->tipo === 'Receita' ? '+' : '−' }} R$ {{ number_format((float)$l->valor_final, 2, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="py-8 text-center text-slate-400">Nenhum lançamento no período.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <style>[x-cloak]{display:none!important}</style>
</x-app-layout>
