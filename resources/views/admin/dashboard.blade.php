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

    <!-- ===== CARDS DAS LOJAS (clique para gerenciar) ===== -->
    <p class="text-[11px] font-black uppercase tracking-widest text-slate-400 mb-3">Lojas · toque para gerenciar</p>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
        @forelse($lojas as $loja)
            @php [$rotulo, $cor, $dot] = badgeStatus($loja->status); @endphp
            <div class="relative group">
                {{-- Botão de excluir (canto do card) --}}
                <button type="button" @click.stop.prevent="excluir = {{ $loja->id }}"
                        class="absolute top-2.5 right-2.5 z-10 w-8 h-8 rounded-full bg-white/90 text-slate-300 hover:text-rose-600 hover:bg-rose-50 shadow-sm flex items-center justify-center transition"
                        title="Excluir loja">
                    <i class="fas fa-trash text-xs"></i>
                </button>

                {{-- Card inteiro clicável -> gerenciar --}}
                <a href="{{ route('admin.lojas.show', $loja) }}"
                   class="block bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-lg hover:border-[#fbbf24]/60 hover:-translate-y-0.5 transition duration-300 p-5 text-center h-full">
                    {{-- Logo (quadradinho arredondado) ou inicial do nome --}}
                    <div class="w-20 h-20 mx-auto rounded-3xl border-2 border-slate-100 bg-slate-50 flex items-center justify-center overflow-hidden mb-3">
                        @if($loja->logo)
                            <img src="{{ asset('storage/'.$loja->logo) }}" alt="{{ $loja->nome_fantasia }}" class="w-full h-full object-contain p-1.5">
                        @else
                            <span class="text-3xl font-black text-slate-300">{{ \Illuminate\Support\Str::of($loja->nome_fantasia)->substr(0,1)->upper() }}</span>
                        @endif
                    </div>

                    <p class="font-black text-slate-800 text-sm leading-tight truncate">{{ $loja->nome_fantasia }}</p>
                    <p class="text-[10px] text-slate-400 truncate mt-0.5">{{ $loja->plano ?: 'Sem plano' }}</p>

                    {{-- Status --}}
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold {{ $cor }} mt-3">
                        <span class="w-1.5 h-1.5 rounded-full {{ $dot }}"></span> {{ $rotulo }}
                    </span>

                    {{-- Mini stats: usuários · clientes · peças --}}
                    <div class="flex items-center justify-center gap-3 mt-3 text-[11px] text-slate-400 font-semibold">
                        <span title="Usuários"><i class="fas fa-user-tie"></i> {{ $loja->users_count }}</span>
                        <span title="Clientes"><i class="fas fa-users"></i> {{ $clientesPorLoja[$loja->id] ?? 0 }}</span>
                        <span title="Peças"><i class="fas fa-tshirt"></i> {{ $pecasPorLoja[$loja->id] ?? 0 }}</span>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl p-12 text-center text-slate-400 font-medium border border-slate-100">Nenhuma loja cadastrada ainda.</div>
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
