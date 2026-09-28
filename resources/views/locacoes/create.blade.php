<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-slate-800 leading-tight uppercase tracking-tighter">
            Nova <span class="text-[#fbbf24]">Locação</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-2xl sm:rounded-[2rem] border border-slate-100">
                <div class="p-10 text-slate-900">

                    <!-- ALERTA DE CONFLITO DA REGRA DE OURO -->
                    @if($errors->has('conflito'))
                        <div class="mb-8 p-6 bg-rose-50 border-l-4 border-rose-500 rounded-r-2xl shadow-sm animate-pulse">
                            <h3 class="text-rose-800 font-black text-lg uppercase tracking-wider mb-1">
                                <i class="fas fa-exclamation-triangle mr-2"></i> Conflito de Datas!
                            </h3>
                            <p class="text-rose-600 font-medium">{{ $errors->first('conflito') }}</p>
                        </div>
                    @endif

                    <form action="{{ route('locacao.store') }}" method="POST" class="space-y-8">
                        @csrf

                        <!-- BLOCO 1: CLIENTE E PEÇA -->
                        <div class="p-6 bg-slate-50 rounded-2xl border border-slate-200">
                            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">1. Identificação</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Cliente</label>
                                    <select name="cliente_id" required class="w-full p-3 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none">
                                        <option value="">Selecione a cliente...</option>
                                        @foreach($clientes as $cliente)
                                            <option value="{{ $cliente->id }}" {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                                {{ $cliente->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Peça do Acervo</label>
                                    <select name="acervo_id" required class="w-full p-3 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none">
                                        <option value="">Selecione a peça...</option>
                                        @foreach($acervos as $peca)
                                            <option value="{{ $peca->id }}" {{ old('acervo_id') == $peca->id ? 'selected' : '' }}>
                                                [{{ $peca->codigo }}] {{ $peca->nome }} (R$ {{ number_format($peca->valor_locacao, 2, ',', '.') }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- BLOCO 2: DATAS -->
                        <div class="p-6 bg-amber-50/50 rounded-2xl border border-amber-100">
                            <h3 class="text-xs font-bold text-amber-500 uppercase tracking-widest mb-4">2. Período de Indisponibilidade</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Retirada</label>
                                    <input type="date" name="data_retirada" value="{{ old('data_retirada') }}" required class="w-full p-3 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Data do Evento</label>
                                    <input type="date" name="data_evento" value="{{ old('data_evento') }}" required class="w-full p-3 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Devolução</label>
                                    <input type="date" name="data_devolucao" value="{{ old('data_devolucao') }}" required class="w-full p-3 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none">
                                    <span class="text-[10px] text-slate-400 mt-1 block">O sistema adicionará 3 dias automáticos para lavanderia.</span>
                                </div>
                            </div>
                        </div>

                        <!-- BLOCO 3: FINANCEIRO E OBS -->
                        <div class="p-6 bg-slate-50 rounded-2xl border border-slate-200">
                            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">3. Financeiro e Detalhes</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Valor Total (R$)</label>
                                    <input type="number" step="0.01" name="valor_total" value="{{ old('valor_total') }}" required class="w-full p-3 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Sinal / Entrada Paga (R$)</label>
                                    <input type="number" step="0.01" name="sinal_pago" value="{{ old('sinal_pago') ?? '0' }}" class="w-full p-3 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Observações Adicionais</label>
                                <textarea name="observacoes" rows="2" class="w-full p-3 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none" placeholder="Ex: Ajustar a barra, Cliente quer buscar às 14h...">{{ old('observacoes') }}</textarea>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4">
                            <button type="submit" class="bg-slate-900 text-[#fbbf24] px-8 py-4 rounded-2xl font-black text-sm uppercase tracking-widest hover:bg-slate-800 transition shadow-xl cursor-pointer">
                                Registrar Locação e Bloquear Datas
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>