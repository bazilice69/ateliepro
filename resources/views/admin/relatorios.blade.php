@extends('admin.layout')

@section('titulo', 'Relatórios do SaaS')

@section('conteudo')
    <div class="flex justify-end gap-2 mb-6">
        <a href="{{ route('admin.relatorios.exportar') }}" class="bg-emerald-500 hover:bg-emerald-600 text-white px-5 py-3 rounded-xl font-black text-xs uppercase tracking-widest transition">
            <i class="fas fa-file-excel mr-1"></i> Excel (CSV)
        </a>
        <a href="{{ route('admin.relatorios.imprimir') }}" target="_blank" class="bg-slate-900 text-[#fbbf24] px-5 py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition">
            <i class="fas fa-print mr-1"></i> PDF / Imprimir
        </a>
    </div>

    <!-- Resumo -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-slate-900 rounded-2xl p-5 shadow-sm">
            <p class="text-[10px] font-black uppercase text-[#fbbf24] tracking-widest">MRR</p>
            <p class="text-2xl font-black text-white mt-1">R$ {{ number_format($mrr, 2, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
            <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest">🟢 Ativas</p>
            <p class="text-2xl font-black text-emerald-600 mt-1">{{ $porStatus['ativo'] }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
            <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest">🟡 Inadimplentes</p>
            <p class="text-2xl font-black text-amber-600 mt-1">{{ $porStatus['inadimplente'] }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
            <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest">✨ Novas no mês</p>
            <p class="text-2xl font-black text-slate-800 mt-1">{{ $novasNoMes }}</p>
        </div>
    </div>

    <!-- Receita por plano -->
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 mb-6">
        <h3 class="font-black text-slate-800 uppercase text-sm tracking-wider mb-4">Receita por Plano (lojas ativas)</h3>
        <table class="w-full text-left text-sm">
            <thead><tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                <th class="py-2">Plano</th><th class="py-2 text-center">Lojas</th><th class="py-2 text-right">Receita/mês</th>
            </tr></thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($porPlano as $nome => $v)
                    <tr>
                        <td class="py-2 font-semibold text-slate-700">{{ $nome ?: '—' }}</td>
                        <td class="py-2 text-center text-slate-600">{{ $v['lojas'] }}</td>
                        <td class="py-2 text-right font-black text-slate-800">R$ {{ number_format($v['receita'], 2, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="py-6 text-center text-slate-400">Nenhuma loja ativa.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Lista de lojas -->
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
        <h3 class="font-black text-slate-800 uppercase text-sm tracking-wider mb-4">Todas as Lojas</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead><tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                    <th class="py-2">Loja</th><th class="py-2">Plano</th><th class="py-2 text-right">Valor</th><th class="py-2 text-center">Status</th><th class="py-2">Vencimento</th>
                </tr></thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($lojas as $l)
                        <tr>
                            <td class="py-2 font-semibold text-slate-700">{{ $l->nome_fantasia }}</td>
                            <td class="py-2 text-slate-600">{{ $l->plano }}</td>
                            <td class="py-2 text-right font-bold text-slate-800">R$ {{ number_format($l->valorEfetivo(), 2, ',', '.') }}</td>
                            <td class="py-2 text-center text-xs font-bold uppercase">{{ $l->status }}</td>
                            <td class="py-2 text-slate-600">{{ optional($l->data_vencimento)->format('d/m/Y') ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
