@extends('admin.layout')

@section('titulo', 'Planos & Preços')

@section('conteudo')
    <div class="flex justify-between items-center mb-6">
        <p class="text-slate-500 text-sm">Estes planos aparecem na página inicial e no checkout.</p>
        <a href="{{ route('admin.planos.create') }}" class="bg-slate-900 text-[#fbbf24] px-5 py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition">+ Novo Plano</a>
    </div>

    <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
        @forelse($planos as $plano)
            <div class="bg-white rounded-3xl p-6 shadow-sm border {{ $plano->destaque ? 'border-[#fbbf24] ring-2 ring-[#fbbf24]/30' : 'border-slate-100' }} flex flex-col relative">
                @if($plano->destaque)
                    <span class="absolute -top-3 left-1/2 -translate-x-1/2 bg-[#fbbf24] text-slate-900 text-[10px] font-black uppercase px-3 py-1 rounded-full tracking-wider">Mais Popular</span>
                @endif
                @unless($plano->ativo)
                    <span class="absolute top-3 right-3 bg-slate-200 text-slate-500 text-[10px] font-bold uppercase px-2 py-0.5 rounded">Inativo</span>
                @endunless

                <h3 class="text-lg font-black text-slate-800 uppercase tracking-tighter">{{ $plano->nome }}</h3>
                <div class="text-3xl font-black text-slate-900 my-3">
                    R$ {{ number_format($plano->preco, 0, ',', '.') }}<span class="text-xs text-slate-400 font-medium">{{ $plano->periodo_label }}</span>
                </div>
                <ul class="space-y-2 text-sm text-slate-600 flex-1 mb-4">
                    @foreach(($plano->recursos ?? []) as $rec)
                        <li><i class="fas fa-check text-emerald-500 mr-2"></i>{{ $rec }}</li>
                    @endforeach
                </ul>
                <div class="flex gap-2">
                    <a href="{{ route('admin.planos.edit', $plano) }}" class="flex-1 text-center bg-slate-100 hover:bg-slate-200 text-slate-800 py-2 rounded-xl font-bold text-xs uppercase">Editar</a>
                    <form method="POST" action="{{ route('admin.planos.destroy', $plano) }}" onsubmit="return confirm('Remover este plano?')">
                        @csrf @method('DELETE')
                        <button class="bg-rose-50 hover:bg-rose-100 text-rose-600 py-2 px-3 rounded-xl font-bold text-xs"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-slate-400 col-span-4 text-center py-12">Nenhum plano cadastrado.</p>
        @endforelse
    </div>
@endsection
