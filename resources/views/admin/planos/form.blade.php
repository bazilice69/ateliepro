@extends('admin.layout')

@section('titulo', $plano->exists ? 'Editar Plano' : 'Novo Plano')

@section('conteudo')
    <a href="{{ route('admin.planos.index') }}" class="text-xs font-bold text-slate-400 hover:text-slate-700 uppercase tracking-widest">← Voltar</a>

    <div class="max-w-2xl mt-4 bg-white rounded-3xl p-8 shadow-sm border border-slate-100">
        <form method="POST" action="{{ $plano->exists ? route('admin.planos.update', $plano) : route('admin.planos.store') }}" class="space-y-5">
            @csrf
            @if($plano->exists) @method('PUT') @endif

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-black uppercase text-slate-400">Nome</label>
                    <input type="text" name="nome" value="{{ old('nome', $plano->nome) }}" required class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                </div>
                <div>
                    <label class="text-[10px] font-black uppercase text-slate-400">Slug (opcional)</label>
                    <input type="text" name="slug" value="{{ old('slug', $plano->slug) }}" placeholder="gerado do nome" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="text-[10px] font-black uppercase text-slate-400">Preço (R$)</label>
                    <input type="number" step="0.01" name="preco" value="{{ old('preco', $plano->preco) }}" required class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                </div>
                <div>
                    <label class="text-[10px] font-black uppercase text-slate-400">Duração (meses)</label>
                    <input type="number" name="meses" value="{{ old('meses', $plano->meses) }}" required class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                </div>
                <div>
                    <label class="text-[10px] font-black uppercase text-slate-400">Rótulo período</label>
                    <input type="text" name="periodo_label" value="{{ old('periodo_label', $plano->periodo_label) }}" placeholder="/mês" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                </div>
            </div>

            <div>
                <label class="text-[10px] font-black uppercase text-slate-400">Recursos (um por linha)</label>
                <textarea name="recursos" rows="4" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl text-sm text-slate-700">{{ old('recursos', implode("\n", $plano->recursos ?? [])) }}</textarea>
            </div>

            <div class="flex items-center gap-6">
                <label class="flex items-center gap-2 text-sm font-semibold text-slate-600">
                    <input type="checkbox" name="destaque" value="1" @checked(old('destaque', $plano->destaque)) class="rounded"> Mais Popular
                </label>
                <label class="flex items-center gap-2 text-sm font-semibold text-slate-600">
                    <input type="checkbox" name="ativo" value="1" @checked(old('ativo', $plano->ativo ?? true)) class="rounded"> Ativo
                </label>
                <div class="flex items-center gap-2">
                    <label class="text-[10px] font-black uppercase text-slate-400">Ordem</label>
                    <input type="number" name="ordem" value="{{ old('ordem', $plano->ordem ?? 0) }}" class="w-20 p-2 bg-slate-50 border-none rounded-lg font-semibold text-slate-700">
                </div>
            </div>

            <button class="w-full bg-slate-900 text-[#fbbf24] py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition">Salvar Plano</button>
        </form>
    </div>
@endsection
