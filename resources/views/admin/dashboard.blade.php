@extends('admin.layout')

@section('titulo', 'Lojas Cadastradas')

@php
    function badgeStatus($status) {
        return match($status) {
            'ativo' => ['🟢', 'Ativa', 'bg-emerald-100 text-emerald-800'],
            'inadimplente' => ['🟡', 'Inadimplente', 'bg-amber-100 text-amber-800'],
            'bloqueado' => ['🔴', 'Bloqueada', 'bg-rose-100 text-rose-800'],
            default => ['⚫', ucfirst($status), 'bg-slate-200 text-slate-700'],
        };
    }
@endphp

@section('conteudo')
    <!-- Resumo -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
            <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Total de Lojas</p>
            <p class="text-3xl font-black text-slate-800 mt-1">{{ $resumo['total_lojas'] }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
            <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest">🟢 Ativas</p>
            <p class="text-3xl font-black text-emerald-600 mt-1">{{ $resumo['ativas'] }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
            <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest">🟡 Inadimplentes</p>
            <p class="text-3xl font-black text-amber-600 mt-1">{{ $resumo['inadimplentes'] }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
            <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest">🔴 Bloqueadas</p>
            <p class="text-3xl font-black text-rose-600 mt-1">{{ $resumo['bloqueadas'] }}</p>
        </div>
        <div class="bg-slate-900 rounded-2xl p-5 shadow-sm">
            <p class="text-[10px] font-black uppercase text-[#fbbf24] tracking-widest">Faturamento/mês</p>
            <p class="text-2xl font-black text-white mt-1">R$ {{ number_format($resumo['faturamento_mensal'], 2, ',', '.') }}</p>
        </div>
    </div>

    <!-- Lista de lojas -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                    <th class="py-4 pl-6">Loja</th>
                    <th class="py-4">Responsável</th>
                    <th class="py-4">Plano</th>
                    <th class="py-4 text-right">Valor</th>
                    <th class="py-4 text-center">Usuários</th>
                    <th class="py-4 text-center">Clientes</th>
                    <th class="py-4 text-center">Peças</th>
                    <th class="py-4">Vencimento</th>
                    <th class="py-4 text-center">Status</th>
                    <th class="py-4 pr-6"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($lojas as $loja)
                    @php [$emoji, $rotulo, $cor] = badgeStatus($loja->status); @endphp
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="py-4 pl-6">
                            <div class="font-bold text-slate-800 text-sm">{{ $loja->nome_fantasia }}</div>
                            <div class="text-[10px] text-slate-400">{{ $loja->cnpj_cpf }}</div>
                        </td>
                        <td class="py-4 text-sm text-slate-600">{{ $loja->email_responsavel }}</td>
                        <td class="py-4 text-sm font-semibold text-slate-700">{{ $loja->plano }}</td>
                        <td class="py-4 text-right text-sm font-bold text-slate-800">R$ {{ number_format($loja->valorEfetivo(), 2, ',', '.') }}</td>
                        <td class="py-4 text-center text-sm text-slate-600">{{ $loja->users_count }}</td>
                        <td class="py-4 text-center text-sm text-slate-600">{{ $clientesPorLoja[$loja->id] ?? 0 }}</td>
                        <td class="py-4 text-center text-sm text-slate-600">{{ $pecasPorLoja[$loja->id] ?? 0 }}</td>
                        <td class="py-4 text-sm text-slate-600">{{ $loja->data_vencimento?->format('d/m/Y') ?? '—' }}</td>
                        <td class="py-4 text-center">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold {{ $cor }}">
                                {{ $emoji }} {{ $rotulo }}
                            </span>
                        </td>
                        <td class="py-4 pr-6 text-right">
                            <a href="{{ route('admin.lojas.show', $loja) }}" class="text-xs font-bold text-slate-500 hover:text-slate-900 uppercase">Gerenciar →</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="10" class="py-16 text-center text-slate-400 font-medium">Nenhuma loja cadastrada ainda.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
