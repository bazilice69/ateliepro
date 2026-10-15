@extends('admin.layout')

@section('titulo', 'Depoimentos dos Lojistas')

@section('conteudo')
    <div class="max-w-4xl space-y-10">

        {{-- PENDENTES --}}
        <section>
            <div class="flex items-center gap-3 mb-4">
                <h3 class="text-sm font-black uppercase tracking-widest text-slate-500">Aguardando aprovação</h3>
                <span class="bg-amber-100 text-amber-700 text-xs font-black px-2.5 py-0.5 rounded-full">{{ $pendentes->count() }}</span>
            </div>

            @forelse($pendentes as $dep)
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 mb-4">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="font-black text-slate-800">{{ $dep->autor_nome }}
                                @if($dep->loja_nome)<span class="text-slate-400 font-medium">· {{ $dep->loja_nome }}</span>@endif
                            </p>
                            <p class="text-[11px] text-slate-400 uppercase tracking-wider">{{ $dep->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    <p class="text-slate-600 mt-3 leading-relaxed">“{{ $dep->mensagem }}”</p>

                    @if($dep->linkNormalizado())
                        <a href="{{ $dep->linkNormalizado() }}" target="_blank" rel="noopener"
                           class="inline-flex items-center gap-1.5 mt-3 text-xs font-bold text-indigo-600 hover:underline">
                            <i class="fas fa-link"></i> {{ $dep->linkRotulo() }}
                        </a>
                    @endif

                    <div class="flex flex-wrap gap-2 mt-5">
                        <form method="POST" action="{{ route('admin.depoimentos.aprovar', $dep) }}">
                            @csrf @method('PUT')
                            <button class="bg-emerald-500 hover:bg-emerald-400 text-white px-5 py-2.5 rounded-xl font-black text-xs uppercase tracking-widest transition">
                                <i class="fas fa-check mr-1"></i> Aprovar
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.depoimentos.destroy', $dep) }}" onsubmit="return confirm('Remover este depoimento?')">
                            @csrf @method('DELETE')
                            <button class="bg-slate-100 hover:bg-rose-100 text-slate-500 hover:text-rose-600 px-5 py-2.5 rounded-xl font-black text-xs uppercase tracking-widest transition">
                                <i class="fas fa-trash mr-1"></i> Descartar
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl p-8 text-center text-slate-400 border border-slate-100">
                    <i class="fas fa-inbox text-3xl mb-2 opacity-50"></i>
                    <p class="font-bold">Nenhum elogio aguardando.</p>
                </div>
            @endforelse
        </section>

        {{-- APROVADOS (no ar) --}}
        <section>
            <div class="flex items-center gap-3 mb-4">
                <h3 class="text-sm font-black uppercase tracking-widest text-slate-500">Publicados na página inicial</h3>
                <span class="bg-emerald-100 text-emerald-700 text-xs font-black px-2.5 py-0.5 rounded-full">{{ $aprovados->count() }}</span>
            </div>

            @forelse($aprovados as $dep)
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 mb-3 flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <p class="font-black text-slate-800 text-sm">{{ $dep->autor_nome }}
                            @if($dep->loja_nome)<span class="text-slate-400 font-medium">· {{ $dep->loja_nome }}</span>@endif
                            @if($dep->linkNormalizado())
                                <a href="{{ $dep->linkNormalizado() }}" target="_blank" rel="noopener" class="text-indigo-500 ml-1" title="{{ $dep->linkRotulo() }}"><i class="fas fa-link text-xs"></i></a>
                            @endif
                        </p>
                        <p class="text-slate-500 text-sm mt-1 truncate">“{{ $dep->mensagem }}”</p>
                    </div>
                    <div class="flex gap-1.5 shrink-0">
                        <form method="POST" action="{{ route('admin.depoimentos.reprovar', $dep) }}" title="Ocultar da landing">
                            @csrf @method('PUT')
                            <button class="bg-slate-100 hover:bg-amber-100 text-slate-500 hover:text-amber-600 w-9 h-9 rounded-lg font-black text-xs transition"><i class="fas fa-eye-slash"></i></button>
                        </form>
                        <form method="POST" action="{{ route('admin.depoimentos.destroy', $dep) }}" onsubmit="return confirm('Remover este depoimento?')">
                            @csrf @method('DELETE')
                            <button class="bg-slate-100 hover:bg-rose-100 text-slate-500 hover:text-rose-600 w-9 h-9 rounded-lg font-black text-xs transition"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl p-8 text-center text-slate-400 border border-slate-100">
                    <i class="fas fa-comment-slash text-3xl mb-2 opacity-50"></i>
                    <p class="font-bold">Nenhum depoimento publicado ainda.</p>
                </div>
            @endforelse
        </section>
    </div>
@endsection
