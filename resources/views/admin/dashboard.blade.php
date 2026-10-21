@extends('admin.layout')

@section('titulo', 'Lojas Cadastradas')

@php
    function badgeStatus($status) {
        return match($status) {
            'ativo' => ['Ativa', 'bg-emerald-100 text-emerald-700', 'bg-emerald-500'],
            'inadimplente' => ['Inadimplente', 'bg-amber-100 text-amber-700', 'bg-amber-500'],
            'bloqueado' => ['Bloqueada', 'bg-rose-100 text-rose-700', 'bg-rose-500'],
            default => [ucfirst($status), 'bg-slate-200 text-slate-700', 'bg-slate-400'],
        };
    }
@endphp

@section('conteudo')
<div x-data="{ excluir: null }">

    <!-- ===== CARDS DE RESUMO ===== -->
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3 sm:gap-4 mb-8">
        @foreach([
            ['Total de Lojas', $resumo['total_lojas'], 'fa-store', 'text-slate-800', 'text-slate-300'],
            ['Ativas', $resumo['ativas'], 'fa-circle-check', 'text-emerald-600', 'text-emerald-200'],
            ['Inadimplentes', $resumo['inadimplentes'], 'fa-triangle-exclamation', 'text-amber-600', 'text-amber-200'],
            ['Vencendo (7d)', $resumo['vencendo'], 'fa-clock', 'text-orange-500', 'text-orange-200'],
            ['Novas no mês', $resumo['novas_no_mes'], 'fa-sparkles', 'text-slate-800', 'text-slate-300'],
        ] as [$rotulo, $valor, $icone, $corValor, $corIcone])
            <div class="relative bg-white rounded-2xl p-5 shadow-sm border border-slate-100 overflow-hidden">
                <i class="fas {{ $icone }} absolute -right-2 -bottom-2 text-5xl {{ $corIcone }} opacity-40"></i>
                <p class="relative text-[10px] font-black uppercase text-slate-400 tracking-widest">{{ $rotulo }}</p>
                <p class="relative text-3xl font-black {{ $corValor }} mt-1">{{ $valor }}</p>
            </div>
        @endforeach
        <div class="relative bg-slate-900 rounded-2xl p-5 shadow-lg overflow-hidden">
            <i class="fas fa-chart-line absolute -right-2 -bottom-2 text-5xl text-slate-800 opacity-60"></i>
            <p class="relative text-[10px] font-black uppercase text-[#fbbf24] tracking-widest">MRR (recorrente/mês)</p>
            <p class="relative text-2xl font-black text-white mt-1">R$ {{ number_format($resumo['faturamento_mensal'], 2, ',', '.') }}</p>
        </div>
    </div>

    <!-- ===== LISTA DE LOJAS ===== -->

    {{-- TABELA (desktop) --}}
    <div class="hidden lg:block bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                    <th class="py-4 pl-6">Loja</th>
                    <th class="py-4">Responsável</th>
                    <th class="py-4">Plano</th>
                    <th class="py-4 text-right">Valor</th>
                    <th class="py-4 text-center" title="Usuários · Clientes · Peças">Uso</th>
                    <th class="py-4">Vencimento</th>
                    <th class="py-4 text-center">Status</th>
                    <th class="py-4 pr-6 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($lojas as $loja)
                    @php [$rotulo, $cor, $dot] = badgeStatus($loja->status); @endphp
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="py-4 pl-6">
                            <div class="font-bold text-slate-800 text-sm">{{ $loja->nome_fantasia }}</div>
                            <div class="text-[10px] text-slate-400">{{ $loja->cnpj_cpf ?: '—' }}</div>
                        </td>
                        <td class="py-4 text-sm text-slate-600">{{ $loja->email_responsavel }}</td>
                        <td class="py-4 text-sm font-semibold text-slate-700">{{ $loja->plano ?: '—' }}</td>
                        <td class="py-4 text-right text-sm font-bold text-slate-800">R$ {{ number_format($loja->valorEfetivo(), 2, ',', '.') }}</td>
                        <td class="py-4 text-center text-xs text-slate-500 font-semibold whitespace-nowrap">
                            <span title="Usuários"><i class="fas fa-user-tie text-slate-300"></i> {{ $loja->users_count }}</span>
                            <span class="mx-1 text-slate-200">·</span>
                            <span title="Clientes"><i class="fas fa-users text-slate-300"></i> {{ $clientesPorLoja[$loja->id] ?? 0 }}</span>
                            <span class="mx-1 text-slate-200">·</span>
                            <span title="Peças"><i class="fas fa-tshirt text-slate-300"></i> {{ $pecasPorLoja[$loja->id] ?? 0 }}</span>
                        </td>
                        <td class="py-4 text-sm text-slate-600">{{ $loja->data_vencimento?->format('d/m/Y') ?? '—' }}</td>
                        <td class="py-4 text-center">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold {{ $cor }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $dot }}"></span> {{ $rotulo }}
                            </span>
                        </td>
                        <td class="py-4 pr-6 text-right whitespace-nowrap">
                            <a href="{{ route('admin.lojas.show', $loja) }}" class="text-xs font-bold text-slate-600 hover:text-slate-900 uppercase">Gerenciar</a>
                            <button type="button" @click="excluir = {{ $loja->id }}" class="ml-3 text-xs font-bold text-rose-400 hover:text-rose-600 uppercase" title="Excluir loja">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="py-16 text-center text-slate-400 font-medium">Nenhuma loja cadastrada ainda.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- CARDS (mobile/tablet) --}}
    <div class="lg:hidden space-y-3">
        @forelse($lojas as $loja)
            @php [$rotulo, $cor, $dot] = badgeStatus($loja->status); @endphp
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="font-black text-slate-800 truncate">{{ $loja->nome_fantasia }}</p>
                        <p class="text-xs text-slate-400 truncate">{{ $loja->email_responsavel }}</p>
                    </div>
                    <span class="shrink-0 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold {{ $cor }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $dot }}"></span> {{ $rotulo }}
                    </span>
                </div>

                <div class="grid grid-cols-3 gap-2 mt-3 text-center">
                    <div class="bg-slate-50 rounded-xl py-2">
                        <p class="text-sm font-black text-slate-700">{{ $loja->users_count }}</p>
                        <p class="text-[9px] font-bold uppercase text-slate-400">Usuários</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl py-2">
                        <p class="text-sm font-black text-slate-700">{{ $clientesPorLoja[$loja->id] ?? 0 }}</p>
                        <p class="text-[9px] font-bold uppercase text-slate-400">Clientes</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl py-2">
                        <p class="text-sm font-black text-slate-700">{{ $pecasPorLoja[$loja->id] ?? 0 }}</p>
                        <p class="text-[9px] font-bold uppercase text-slate-400">Peças</p>
                    </div>
                </div>

                <div class="flex items-center justify-between mt-3 text-xs">
                    <span class="text-slate-500 font-semibold">{{ $loja->plano ?: '—' }} · R$ {{ number_format($loja->valorEfetivo(), 2, ',', '.') }}</span>
                    <span class="text-slate-400">venc. {{ $loja->data_vencimento?->format('d/m/Y') ?? '—' }}</span>
                </div>

                <div class="flex gap-2 mt-4">
                    <a href="{{ route('admin.lojas.show', $loja) }}" class="flex-1 text-center bg-slate-900 text-[#fbbf24] px-4 py-2.5 rounded-xl font-black text-[11px] uppercase tracking-widest">Gerenciar</a>
                    <button type="button" @click="excluir = {{ $loja->id }}" class="px-4 py-2.5 rounded-xl font-black text-[11px] uppercase tracking-widest bg-rose-50 text-rose-600 hover:bg-rose-100 transition">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl p-12 text-center text-slate-400 font-medium border border-slate-100">Nenhuma loja cadastrada ainda.</div>
        @endforelse
    </div>

    {{-- ===== MODAIS DE EXCLUSÃO (um por loja, controlados pelo Alpine) ===== --}}
    @foreach($lojas as $loja)
        <div x-show="excluir === {{ $loja->id }}" x-cloak
             class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4"
             @click.self="excluir = null" @keydown.escape.window="excluir = null">
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-7">
                <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center mb-4">
                    <i class="fas fa-triangle-exclamation text-xl"></i>
                </div>
                <h3 class="text-lg font-black text-slate-800">Excluir "{{ $loja->nome_fantasia }}"?</h3>
                <p class="text-sm text-slate-500 mt-2">
                    Esta ação é <strong class="text-rose-600">irreversível</strong>. Serão apagados em definitivo:
                    usuários, clientes, acervo e fotos, locações, provas, contratos, financeiro e todos os demais dados desta loja.
                </p>
                <form method="POST" action="{{ route('admin.lojas.destroy', $loja) }}" class="mt-5">
                    @csrf @method('DELETE')
                    <label class="block text-[10px] font-black uppercase text-slate-400 tracking-widest mb-1">
                        Para confirmar, digite o nome da loja:
                    </label>
                    <input type="text" name="confirmacao" autocomplete="off"
                           placeholder="{{ $loja->nome_fantasia }}"
                           class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-700 focus:ring-2 focus:ring-rose-400 focus:outline-none">
                    <div class="flex gap-2 mt-5">
                        <button type="button" @click="excluir = null" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-black py-3 rounded-xl text-xs uppercase tracking-widest transition">Cancelar</button>
                        <button type="submit" class="flex-1 bg-rose-600 hover:bg-rose-500 text-white font-black py-3 rounded-xl text-xs uppercase tracking-widest transition shadow-lg">
                            <i class="fas fa-trash mr-1"></i> Excluir em definitivo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
</div>
@endsection
