<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-slate-800 leading-tight uppercase tracking-tighter">
            Nova Peça do <span class="text-[#fbbf24]">Acervo</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-2xl sm:rounded-[2rem] border border-slate-100 p-10">

                <!-- Exibe erros de validação (ex: Código duplicado) -->
                @if ($errors->any())
                    <div class="mb-6 p-4 bg-rose-50 border-l-4 border-rose-500 rounded-r-xl text-rose-700">
                        <ul class="list-disc pl-5 font-medium">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('produto.store') }}" method="POST" class="space-y-8">
                    @csrf

                    <!-- BLOCO 1: IDENTIFICAÇÃO -->
                    <div class="p-6 bg-slate-50 rounded-2xl border border-slate-200">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">1. Identificação da Peça</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Código da Peça *</label>
                                <input type="text" name="codigo" value="{{ $codigoSugerido }}" required class="w-full p-3 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none uppercase">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-bold text-slate-700 mb-2">Nome ou Modelo *</label>
                                <input type="text" name="nome" required class="w-full p-3 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none" placeholder="Ex: Vestido Sereia Renda Francesa">
                            </div>
                        </div>
                    </div>

                    <!-- BLOCO 2: CARACTERÍSTICAS -->
                    <div class="p-6 bg-amber-50/50 rounded-2xl border border-amber-100">
                        <h3 class="text-xs font-bold text-amber-500 uppercase tracking-widest mb-4">2. Características e Valores</h3>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-bold text-slate-700 mb-2">Categoria *</label>
                                <select name="categoria" required class="w-full p-3 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none">
                                    <!-- Noivas e Debutantes -->
                                    <option value="Noiva">Vestido de Noiva</option>
                                    <option value="Debutante">Vestido de Debutante</option>
                                    
                                    <!-- Linha Festa (Desmembrada conforme pedido) -->
                                    <option value="Mae de Noiva/Noivo">Mãe de Noiva / Noivo</option>
                                    <option value="Madrinha">Madrinha</option>
                                    <option value="Formatura">Formatura</option>
                                    <option value="Convidada">Convidada</option>
                                    
                                    <!-- Masculino e Infantil -->
                                    <option value="Terno">Terno / Masculino</option>
                                    <option value="Infantil">Infantil</option>
                                    
                                    <!-- Acessórios (Novos) -->
                                    <option value="Veu">Véu</option>
                                    <option value="Saiote">Saiote</option>
                                    <option value="Arranjo de Cabelo">Arranjo de Cabelo</option>
                                    <option value="Acessorio">Outros Acessórios</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Tamanho</label>
                                <input type="text" name="tamanho" class="w-full p-3 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none" placeholder="Ex: 42 ou M">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Cor</label>
                                <input type="text" name="cor" class="w-full p-3 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none" placeholder="Ex: Off-White">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-bold text-slate-700 mb-2">Valor de Locação (R$) *</label>
                                <input type="number" step="0.01" name="valor_locacao" required class="w-full p-3 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none" placeholder="0.00">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-bold text-slate-700 mb-2">Status Inicial</label>
                                <select name="status" class="w-full p-3 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none">
                                    <option value="disponivel">Disponível</option>
                                    <option value="manutencao">Em Manutenção</option>
                                    <option value="indisponivel">Indisponível</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- BLOCO 3: OBSERVAÇÕES -->
                    <div class="p-6 bg-slate-50 rounded-2xl border border-slate-200">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">3. Observações</h3>
                        <textarea name="observacoes" rows="2" class="w-full p-3 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none" placeholder="Detalhes sobre bordados, danos existentes, ajustes padrão..."></textarea>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit" class="bg-slate-900 text-[#fbbf24] px-8 py-4 rounded-2xl font-black text-sm uppercase tracking-widest hover:bg-slate-800 transition shadow-xl cursor-pointer">
                            Cadastrar Peça
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>