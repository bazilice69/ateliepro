<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-slate-800 leading-tight uppercase tracking-tighter">
                Novo <span class="text-[#fbbf24]">Cliente</span>
            </h2>
            <span class="bg-slate-200 text-slate-600 text-xs font-black uppercase px-3 py-1 rounded-lg">Ficha de Entrada</span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-2xl sm:rounded-[2rem] border border-slate-100 p-10">

                <form action="{{ route('clientes.store') }}" method="POST" class="space-y-8">
                    @csrf

                    <!-- BLOCO 1: DADOS PESSOAIS E CONTATO -->
                    <div class="p-6 bg-slate-50 rounded-2xl border border-slate-200">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">1. Identificação e Contato</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-bold text-slate-700 mb-2">Nome Completo *</label>
                                <input type="text" name="nome" required class="w-full p-3 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none" placeholder="Ex: Ana Paula Silva">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">CPF</label>
                                <input type="text" name="cpf" class="w-full p-3 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none" placeholder="000.000.000-00">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-bold text-slate-700 mb-2">Telefone / WhatsApp *</label>
                                <div class="flex">
                                    <input type="text" name="telefone" required class="w-full p-3 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none" placeholder="(00) 00000-0000">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Data de Nascimento</label>
                                <input type="date" name="data_nascimento" class="w-full p-3 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none">
                            </div>
                        </div>
                    </div>

                    <!-- BLOCO 2: PERFIL E RESPONSÁVEL -->
                    <div class="p-6 bg-amber-50/50 rounded-2xl border border-amber-100">
                        <h3 class="text-xs font-bold text-amber-500 uppercase tracking-widest mb-4">2. Perfil do Cliente</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Público / Gênero</label>
                                <select name="tipo" id="tipo_cliente" onchange="toggleResponsavel()" class="w-full p-3 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none">
                                    <option value="Feminino">Feminino</option>
                                    <option value="Masculino">Masculino</option>
                                    <option value="Infantil">Infantil</option>
                                </select>
                            </div>
                        </div>

                        <!-- Sub-bloco: Responsável (Oculto por padrão) -->
                        <div id="bloco_responsavel" class="hidden mt-6 pt-6 border-t border-amber-200">
                            <h4 class="text-[10px] font-black text-amber-600 uppercase tracking-widest mb-4"><i class="fas fa-child mr-2"></i>Dados do Responsável</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Nome do Responsável *</label>
                                    <input type="text" name="responsavel_nome" class="w-full p-3 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Parentesco</label>
                                    <input type="text" name="responsavel_parentesco" class="w-full p-3 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none" placeholder="Ex: Mãe, Pai, Avó">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- BLOCO 3: ENDEREÇO (Com ViaCEP) -->
                    <div class="p-6 bg-slate-50 rounded-2xl border border-slate-200">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">3. Endereço</h3>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">CEP</label>
                                <input type="text" name="cep" id="cep" onblur="buscaCep()" class="w-full p-3 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none" placeholder="00000-000">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-bold text-slate-700 mb-2">Rua / Logradouro</label>
                                <input type="text" name="rua" id="rua" class="w-full p-3 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Número</label>
                                <input type="text" name="numero" class="w-full p-3 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Complemento</label>
                                <input type="text" name="complemento" class="w-full p-3 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Bairro</label>
                                <input type="text" name="bairro" id="bairro" class="w-full p-3 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Cidade</label>
                                <input type="text" name="cidade" id="cidade" class="w-full p-3 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Estado</label>
                                <input type="text" name="estado" id="estado" class="w-full p-3 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none" placeholder="SP">
                            </div>
                        </div>
                    </div>

                    <!-- BLOCO 4: OBSERVAÇÕES -->
                    <div class="p-6 bg-slate-50 rounded-2xl border border-slate-200">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">4. Preferências e Observações</h3>
                        <textarea name="observacoes" rows="2" class="w-full p-3 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none" placeholder="Ex: Gosta de vestidos sereia, não gosta de cor vermelha..."></textarea>
                    </div>

                    <div class="flex justify-end pt-4 space-x-4">
                        <a href="{{ route('dashboard') }}" class="px-8 py-4 rounded-2xl font-bold text-slate-500 hover:bg-slate-100 transition cursor-pointer">
                            Cancelar
                        </a>
                        <button type="submit" class="bg-slate-900 text-[#fbbf24] px-8 py-4 rounded-2xl font-black text-sm uppercase tracking-widest hover:bg-slate-800 transition shadow-xl cursor-pointer">
                            Salvar Cliente
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- SCRIPTS MÁGICOS -->
    <script>
        // 1. Mostrar/Esconder Bloco do Responsável
        function toggleResponsavel() {
            const tipo = document.getElementById('tipo_cliente').value;
            const bloco = document.getElementById('bloco_responsavel');
            if (tipo === 'Infantil') {
                bloco.classList.remove('hidden');
            } else {
                bloco.classList.add('hidden');
            }
        }

        // 2. Busca Automática de CEP (ViaCEP)
        function buscaCep() {
            let cep = document.getElementById('cep').value.replace(/\D/g, '');
            if (cep.length === 8) {
                fetch(`https://viacep.com.br/ws/${cep}/json/`)
                    .then(response => response.json())
                    .then(data => {
                        if (!data.erro) {
                            document.getElementById('rua').value = data.logradouro;
                            document.getElementById('bairro').value = data.bairro;
                            document.getElementById('cidade').value = data.localidade;
                            document.getElementById('estado').value = data.uf;
                            // Dá foco no número para o usuário continuar digitando rápido
                            document.querySelector('input[name="numero"]').focus();
                        }
                    });
            }
        }
    </script>
</x-app-layout>