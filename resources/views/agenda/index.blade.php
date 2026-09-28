<x-app-layout>
    <div class="max-w-7xl mx-auto p-6 relative">
        
        <!-- Cabeçalho -->
        <div class="flex justify-between items-end mb-8">
            <div>
                <h1 class="text-4xl font-light text-slate-900 tracking-tighter uppercase">Agenda <span class="font-black text-[#fbbf24]">Master</span></h1>
                <p class="text-slate-400 mt-2 font-medium tracking-wide">Gestão de provas, retiradas e fluxo de ateliê.</p>
            </div>
            <!-- Botão Atualizado para abrir o Modal -->
            <button onclick="document.getElementById('modalNovoAgendamento').classList.remove('hidden')" class="bg-slate-900 text-white px-8 py-4 rounded-2xl font-bold hover:bg-slate-800 transition shadow-xl flex items-center cursor-pointer">
                <i class="fas fa-calendar-plus mr-3 text-[#fbbf24]"></i> NOVO AGENDAMENTO
            </button>
        </div>

        <!-- Cards de Resumo (Dashboard da Agenda) -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
            <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm flex items-center justify-between">
                <div><p class="text-[10px] font-black uppercase text-slate-400">Atendimentos</p><p class="text-2xl font-black text-slate-800">18</p></div>
                <div class="w-10 h-10 bg-slate-50 rounded-full flex items-center justify-center text-slate-400"><i class="fas fa-calendar-day"></i></div>
            </div>
            <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm flex items-center justify-between">
                <div><p class="text-[10px] font-black uppercase text-slate-400">Provas</p><p class="text-2xl font-black text-slate-800">07</p></div>
                <div class="w-10 h-10 bg-blue-50 rounded-full flex items-center justify-center text-blue-500"><i class="fas fa-ruler-combined"></i></div>
            </div>
            <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm flex items-center justify-between">
                <div><p class="text-[10px] font-black uppercase text-slate-400">Ajustes</p><p class="text-2xl font-black text-slate-800">04</p></div>
                <div class="w-10 h-10 bg-purple-50 rounded-full flex items-center justify-center text-purple-500"><i class="fas fa-cut"></i></div>
            </div>
            <div class="bg-white rounded-2xl p-4 border border-rose-100 shadow-sm flex items-center justify-between">
                <div><p class="text-[10px] font-black uppercase text-rose-400">Atrasos</p><p class="text-2xl font-black text-rose-600">02</p></div>
                <div class="w-10 h-10 bg-rose-50 rounded-full flex items-center justify-center text-rose-500"><i class="fas fa-exclamation-triangle"></i></div>
            </div>
            <div class="bg-white rounded-2xl p-4 border border-emerald-100 shadow-sm flex items-center justify-between">
                <div><p class="text-[10px] font-black uppercase text-emerald-400">R$ Pendente</p><p class="text-2xl font-black text-emerald-600">03</p></div>
                <div class="w-10 h-10 bg-emerald-50 rounded-full flex items-center justify-center text-emerald-500"><i class="fas fa-hand-holding-usd"></i></div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Lista de Atendimentos do Dia -->
            <div class="lg:col-span-2 bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-100">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-black uppercase tracking-tighter text-slate-900">Horários de Hoje</h2>
                    <div class="flex space-x-2">
                        <span class="text-[9px] font-bold px-2 py-1 bg-emerald-100 text-emerald-700 rounded uppercase">Confirmado</span>
                        <span class="text-[9px] font-bold px-2 py-1 bg-blue-100 text-blue-700 rounded uppercase">Agendado</span>
                        <span class="text-[9px] font-bold px-2 py-1 bg-orange-100 text-orange-700 rounded uppercase">Em Prova</span>
                    </div>
                </div>

                <div class="space-y-4">
                    <!-- Item de Agenda: Confirmado -->
                    <div class="group flex items-center p-4 bg-white hover:bg-slate-50 border-l-4 border-emerald-500 rounded-r-2xl border-y border-r border-slate-100 shadow-sm transition-colors cursor-pointer">
                        <div class="font-black text-lg text-slate-800 w-20">10:00</div>
                        <div class="flex-1">
                            <p class="font-bold text-slate-900 text-sm uppercase">Ana Clara (Noiva)</p>
                            <p class="text-xs text-slate-500 font-medium">Evento: Casamento Ana & João (15/11/2026)</p>
                            <div class="mt-2 flex space-x-2">
                                <span class="bg-slate-100 text-slate-600 text-[9px] font-black uppercase px-2 py-1 rounded">Prova Final</span>
                                <span class="bg-purple-50 text-purple-600 text-[9px] font-black uppercase px-2 py-1 rounded"><i class="fas fa-door-open mr-1"></i> Sala 1</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 mb-2 ml-auto"><i class="fas fa-check"></i></div>
                            <span class="text-[10px] font-bold text-slate-400">Vestido: V-034</span>
                        </div>
                    </div>

                    <!-- Item de Agenda: Em Atendimento -->
                    <div class="group flex items-center p-4 bg-orange-50 border-l-4 border-orange-500 rounded-r-2xl border-y border-r border-orange-100 shadow-sm transition-colors cursor-pointer">
                        <div class="font-black text-lg text-orange-700 w-20">11:30</div>
                        <div class="flex-1">
                            <p class="font-bold text-slate-900 text-sm uppercase">Maria Silva (Madrinha)</p>
                            <p class="text-xs text-orange-600/80 font-medium">Evento: Casamento Ana & João</p>
                            <div class="mt-2 flex space-x-2">
                                <span class="bg-white text-orange-600 border border-orange-200 text-[9px] font-black uppercase px-2 py-1 rounded shadow-sm">Tirada de Medidas</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="w-8 h-8 rounded-full bg-orange-200 flex items-center justify-center text-orange-700 mb-2 ml-auto"><i class="fas fa-cut"></i></div>
                            <span class="text-[10px] font-bold text-orange-500">Pendência Financeira</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- A Grande Sacada: Linha do Tempo do Evento -->
            <div class="bg-slate-900 rounded-[2.5rem] p-8 shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 right-0 p-8 opacity-10 text-[#fbbf24]"><i class="fas fa-ring text-6xl"></i></div>
                
                <h2 class="text-xl font-black uppercase tracking-tighter text-white mb-2 relative z-10">Dossiê do Evento</h2>
                <p class="text-xs font-medium text-[#fbbf24] mb-8 relative z-10 uppercase tracking-widest">Casamento Ana & João</p>

                <!-- Timeline Vertical -->
                <div class="relative border-l-2 border-slate-700 ml-3 space-y-6 relative z-10">
                    
                    <div class="relative pl-6">
                        <div class="absolute w-4 h-4 bg-emerald-500 rounded-full -left-[9px] top-1 ring-4 ring-slate-900"></div>
                        <p class="text-[10px] font-bold text-slate-400">10/08/2026</p>
                        <p class="text-sm font-bold text-white">Primeiro Atendimento</p>
                    </div>

                    <div class="relative pl-6">
                        <div class="absolute w-4 h-4 bg-emerald-500 rounded-full -left-[9px] top-1 ring-4 ring-slate-900"></div>
                        <p class="text-[10px] font-bold text-slate-400">20/09/2026</p>
                        <p class="text-sm font-bold text-white">Prova 1 (Medidas)</p>
                    </div>

                    <div class="relative pl-6">
                        <div class="absolute w-4 h-4 bg-blue-500 rounded-full -left-[9px] top-1 ring-4 ring-slate-900 animate-pulse"></div>
                        <p class="text-[10px] font-bold text-[#fbbf24]">HOJE - 10:00</p>
                        <p class="text-sm font-bold text-white">Prova Final</p>
                        <p class="text-xs text-slate-400 mt-1">Ajuste de barra e cintura prontos pela costureira.</p>
                    </div>

                    <div class="relative pl-6 opacity-50">
                        <div class="absolute w-4 h-4 bg-slate-700 rounded-full -left-[9px] top-1 ring-4 ring-slate-900"></div>
                        <p class="text-[10px] font-bold text-slate-400">12/11/2026</p>
                        <p class="text-sm font-bold text-white">Retirada Agendada</p>
                    </div>

                    <div class="relative pl-6 opacity-50">
                        <div class="absolute w-4 h-4 bg-[#fbbf24] rounded-full -left-[9px] top-1 ring-4 ring-slate-900"></div>
                        <p class="text-[10px] font-bold text-slate-400 text-[#fbbf24]">15/11/2026</p>
                        <p class="text-sm font-black text-[#fbbf24] uppercase tracking-widest">O Casamento</p>
                    </div>

                </div>
                
                <button class="w-full mt-8 border border-slate-700 text-slate-300 py-3 rounded-xl text-xs font-bold uppercase hover:bg-slate-800 transition">Ver Ficha Completa</button>
            </div>
        </div>
    </div>

    <!-- Modal de Novo Agendamento (Fica escondido até clicar no botão) -->
    <div id="modalNovoAgendamento" class="fixed inset-0 bg-slate-900/90 backdrop-blur-md hidden flex items-center justify-center p-4 z-50 overflow-y-auto">
        <div class="bg-white rounded-[3.5rem] w-full max-w-4xl my-8 overflow-hidden shadow-2xl relative">
            
            <!-- Cabeçalho do Modal -->
            <div class="bg-slate-900 p-8 text-white flex justify-between items-center border-b border-slate-800">
                <div>
                    <h2 class="text-2xl font-black uppercase tracking-tighter">Novo <span class="text-[#fbbf24]">Agendamento</span></h2>
                    <p class="text-xs text-slate-400 font-medium mt-1">Vincule o atendimento ao evento e à cliente.</p>
                </div>
                <!-- Botão de Fechar o Modal -->
                <button onclick="document.getElementById('modalNovoAgendamento').classList.add('hidden')" class="text-slate-500 hover:text-white text-3xl transition-colors cursor-pointer">&times;</button>
            </div>
            
            <!-- Formulário -->
            <form action="#" method="POST" class="p-10">
                @csrf
                <div class="grid grid-cols-6 gap-6">
                    
                    <!-- SEÇÃO 1: Envolvidos -->
                    <div class="col-span-6 border-b border-slate-100 pb-2 font-black text-[10px] uppercase text-slate-400 tracking-[0.2em] italic">1. Cliente e Evento</div>
                    
                    <div class="col-span-3">
                        <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Cliente</label>
                        <select name="cliente_id" class="w-full p-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-[#fbbf24] font-bold text-slate-700 cursor-pointer" required>
                            <option value="">Selecione uma cliente...</option>
                            <!-- LOOP MÁGICO DE CLIENTES -->
                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente->id }}">{{ $cliente->nome }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-span-3">
                        <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Evento Vinculado</label>
                        <select name="evento_id" class="w-full p-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-[#fbbf24] font-bold text-slate-700 cursor-pointer">
                            <option value="">Selecione o evento...</option>
                            <!-- LOOP MÁGICO DE EVENTOS -->
                            @foreach($eventos as $evento)
                                <option value="{{ $evento->id }}">{{ $evento->nome_evento }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- SEÇÃO 2: Dados do Atendimento -->
                    <div class="col-span-6 border-b border-slate-100 pb-2 mt-4 font-black text-[10px] uppercase text-slate-400 tracking-[0.2em] italic">2. Detalhes do Atendimento</div>
                    
                    <div class="col-span-2">
                        <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Data e Hora</label>
                        <input type="datetime-local" name="data_hora" class="w-full p-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-[#fbbf24] font-bold text-slate-700 cursor-pointer" required>
                    </div>

                    <div class="col-span-2">
                        <label class="text-[10px] font-black uppercase text-blue-500 ml-2">Tipo de Atendimento</label>
                        <select name="tipo_atendimento" class="w-full p-4 bg-blue-50 border-none rounded-2xl font-black text-blue-800 cursor-pointer">
                            <option value="Primeiro Atendimento">Primeiro Atendimento</option>
                            <option value="Tirada de Medidas">Tirada de Medidas</option>
                            <option value="Prova 1">Prova 1</option>
                            <option value="Prova Final">Prova Final</option>
                            <option value="Retirada">Retirada</option>
                            <option value="Devolução">Devolução</option>
                        </select>
                    </div>

                    <div class="col-span-2">
                        <label class="text-[10px] font-black uppercase text-purple-500 ml-2">Sala / Local</label>
                        <select name="sala_atendimento" class="w-full p-4 bg-purple-50 border-none rounded-2xl font-black text-purple-800 cursor-pointer">
                            <option value="Sala 1 - Noivas">Sala 1 - Noivas</option>
                            <option value="Sala 2 - Festas">Sala 2 - Festas</option>
                            <option value="Sala 3 - Alfaiataria">Sala 3 - Alfaiataria</option>
                        </select>
                    </div>

                    <div class="col-span-6">
                        <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Peça / Produto (Opcional)</label>
                        <select name="produto_id" class="w-full p-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-[#fbbf24] font-bold text-slate-700 cursor-pointer">
                            <option value="">Vincular a uma peça do acervo...</option>
                            <!-- LOOP MÁGICO DE PRODUTOS -->
                            @foreach($produtos as $produto)
                                <option value="{{ $produto->id }}">{{ $produto->nome }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <!-- Botões de Ação -->
                <div class="flex justify-end mt-12 space-x-6 bg-slate-50 -mx-10 -mb-10 p-8 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('modalNovoAgendamento').classList.add('hidden')" class="px-6 py-3 font-bold text-slate-400 uppercase text-xs hover:text-slate-600 cursor-pointer">Cancelar</button>
                    <button type="submit" class="bg-slate-900 text-[#fbbf24] px-12 py-4 rounded-[2rem] font-black text-sm hover:scale-105 transition-all shadow-xl cursor-pointer">
                        SALVAR AGENDAMENTO
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>