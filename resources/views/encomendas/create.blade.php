<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-slate-800 uppercase tracking-tighter">Nova <span class="text-[#fbbf24]">Encomenda</span></h2>
            <a href="{{ route('encomendas.index') }}" class="text-slate-400 hover:text-slate-600 font-bold text-sm uppercase tracking-widest transition">&larr; Voltar</a>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ cliente: '{{ old('cliente_id', $clienteId) }}', medidas: [] }">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-700 px-6 py-4 rounded-2xl font-medium text-sm mb-6">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $erro)<li>{{ $erro }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('encomendas.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- FICHA (cabeçalho igual ao papel da estilista) -->
                <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8">
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 pb-3 mb-6">Ficha do Pedido</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Cliente *</label>
                            <select name="cliente_id" x-model="cliente" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none">
                                <option value="">Selecione a cliente…</option>
                                @foreach($clientes as $c)
                                    <option value="{{ $c->id }}" @selected(old('cliente_id', $clienteId) == $c->id)>{{ $c->nome }}</option>
                                @endforeach
                            </select>
                            <p class="text-[11px] text-slate-400 mt-1">A ficha de medidas da cliente pode ser vinculada depois, na tela da encomenda.</p>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Peça / Modelo *</label>
                            <input type="text" name="titulo" value="{{ old('titulo') }}" required placeholder="Ex: Vestido de noiva sereia com renda" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Tipo *</label>
                            <select name="tipo" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none">
                                @foreach($tipos as $valor => $rotulo)
                                    <option value="{{ $valor }}" @selected(old('tipo') == $valor)>{{ $rotulo }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Data do Pedido</label>
                            <input type="date" name="data_pedido" value="{{ old('data_pedido', date('Y-m-d')) }}" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Data de Prova</label>
                            <input type="date" name="data_prova" value="{{ old('data_prova') }}" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Data de Entrega</label>
                            <input type="date" name="data_entrega" value="{{ old('data_entrega') }}" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none">
                        </div>
                    </div>
                </div>

                <!-- FINANCEIRO -->
                <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8">
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 pb-3 mb-6">Valores</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Valor Total (R$)</label>
                            <input type="number" step="0.01" min="0" name="valor" value="{{ old('valor') }}" placeholder="0,00" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Sinal / Entrada (R$)</label>
                            <input type="number" step="0.01" min="0" name="sinal" value="{{ old('sinal') }}" placeholder="0,00" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none">
                        </div>
                    </div>
                </div>

                <!-- TECIDOS, CROQUI E OBSERVAÇÕES -->
                <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8 space-y-6">
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 pb-3">Tecidos & Criação</h3>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Tecidos</label>
                        <textarea name="tecidos" rows="3" placeholder="Ex: Cetim de seda, renda francesa, forro em musseline…" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none">{{ old('tecidos') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Descrição do Modelo</label>
                        <textarea name="descricao" rows="3" placeholder="Detalhes do modelo, referências, decote, cauda…" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none">{{ old('descricao') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Croqui / Desenho <span class="text-slate-300 normal-case font-medium">(imagem, até 5MB)</span></label>
                        <input type="file" name="croqui" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:text-xs file:font-black file:uppercase file:tracking-widest file:bg-slate-900 file:text-[#fbbf24] hover:file:bg-slate-800 file:cursor-pointer">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Observações</label>
                        <textarea name="observacoes" rows="2" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none">{{ old('observacoes') }}</textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-4">
                    <a href="{{ route('encomendas.index') }}" class="px-6 py-3 font-bold text-slate-500 hover:bg-slate-100 rounded-xl transition">Cancelar</a>
                    <button type="submit" class="bg-slate-900 text-[#fbbf24] px-8 py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 shadow-xl transition">Criar Encomenda</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
