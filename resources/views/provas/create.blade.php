<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-slate-800 uppercase tracking-tighter">Nova <span class="text-[#fbbf24]">Prova</span></h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            @if($errors->any())
                <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-700 px-6 py-4 rounded-2xl text-sm">{{ $errors->first() }}</div>
            @endif
            <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100">
                <form method="POST" action="{{ route('provas.store') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="locacao_id" value="{{ $locacaoId }}">
                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400">Cliente</label>
                        <select name="cliente_id" required class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                            <option value="">Selecione...</option>
                            @foreach($clientes as $c)
                                <option value="{{ $c->id }}" @selected($clienteId == $c->id)>{{ $c->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400">Peça (opcional)</label>
                        <select name="acervo_id" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                            <option value="">— nenhuma —</option>
                            @foreach($acervos as $a)
                                <option value="{{ $a->id }}">{{ $a->codigo }} · {{ $a->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-black uppercase text-slate-400">Data e hora</label>
                            <input type="datetime-local" name="data_prova" required class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                        </div>
                        <div>
                            <label class="text-[10px] font-black uppercase text-slate-400">Responsável</label>
                            <input type="text" name="responsavel" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                        </div>
                    </div>
                    <textarea name="observacoes" rows="2" placeholder="Observações..." class="w-full p-3 bg-slate-50 border-none rounded-xl text-sm"></textarea>
                    <div class="flex gap-3">
                        <a href="{{ route('provas.index') }}" class="px-6 py-3 text-slate-400 font-bold text-xs uppercase">Cancelar</a>
                        <button class="flex-1 bg-slate-900 text-[#fbbf24] py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition">Agendar Prova</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
