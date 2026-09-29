<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-slate-800 uppercase tracking-tighter">Novo <span class="text-[#fbbf24]">Serviço</span></h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            @if($errors->any())
                <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-700 px-6 py-4 rounded-2xl text-sm">{{ $errors->first() }}</div>
            @endif
            <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100">
                <form method="POST" action="{{ route('servicos.store') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="locacao_id" value="{{ $locacaoId }}">
                    <input type="hidden" name="cliente_id" value="{{ $clienteId }}">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-black uppercase text-slate-400">Tipo</label>
                            <select name="tipo" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                                <option value="ajuste" @selected($tipo === 'ajuste')>Ajuste / Costura</option>
                                <option value="lavanderia" @selected($tipo === 'lavanderia')>Lavanderia</option>
                                <option value="manutencao" @selected($tipo === 'manutencao')>Manutenção</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] font-black uppercase text-slate-400">Peça</label>
                            <select name="acervo_id" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                                <option value="">— nenhuma —</option>
                                @foreach($acervos as $a)
                                    <option value="{{ $a->id }}" @selected($acervoId == $a->id)>{{ $a->codigo }} · {{ $a->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400">Descrição</label>
                        <input type="text" name="descricao" required placeholder="Ex: Ajuste de barra e cintura" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400">Itens (um por linha)</label>
                        <textarea name="itens" rows="3" placeholder="Barra&#10;Cintura&#10;Alça" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl text-sm"></textarea>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="text-[10px] font-black uppercase text-slate-400">Responsável</label>
                            <input type="text" name="responsavel" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                        </div>
                        <div>
                            <label class="text-[10px] font-black uppercase text-slate-400">Prazo</label>
                            <input type="date" name="prazo" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                        </div>
                        <div>
                            <label class="text-[10px] font-black uppercase text-slate-400">Custo (R$)</label>
                            <input type="number" step="0.01" name="custo" value="0" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('servicos.index') }}" class="px-6 py-3 text-slate-400 font-bold text-xs uppercase">Cancelar</a>
                        <button class="flex-1 bg-slate-900 text-[#fbbf24] py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition">Abrir Serviço</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
