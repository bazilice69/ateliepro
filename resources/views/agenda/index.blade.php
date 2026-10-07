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

        <!-- Cards de Resumo (dados REAIS do dia) -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
            <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm flex items-center justify-between">
                <div><p class="text-[10px] font-black uppercase text-slate-400">Atendimentos</p><p class="text-2xl font-black text-slate-800">{{ str_pad($cards['atendimentos'], 2, '0', STR_PAD_LEFT) }}</p></div>
                <div class="w-10 h-10 bg-slate-50 rounded-full flex items-center justify-center text-slate-400"><i class="fas fa-calendar-day"></i></div>
            </div>
            <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm flex items-center justify-between">
                <div><p class="text-[10px] font-black uppercase text-slate-400">Provas</p><p class="text-2xl font-black text-slate-800">{{ str_pad($cards['provas'], 2, '0', STR_PAD_LEFT) }}</p></div>
                <div class="w-10 h-10 bg-blue-50 rounded-full flex items-center justify-center text-blue-500"><i class="fas fa-ruler-combined"></i></div>
            </div>
            <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm flex items-center justify-between">
                <div><p class="text-[10px] font-black uppercase text-slate-400">Ajustes</p><p class="text-2xl font-black text-slate-800">{{ str_pad($cards['ajustes'], 2, '0', STR_PAD_LEFT) }}</p></div>
                <div class="w-10 h-10 bg-purple-50 rounded-full flex items-center justify-center text-purple-500"><i class="fas fa-cut"></i></div>
            </div>
            <div class="bg-white rounded-2xl p-4 border border-rose-100 shadow-sm flex items-center justify-between">
                <div><p class="text-[10px] font-black uppercase text-rose-400">Atrasos</p><p class="text-2xl font-black text-rose-600">{{ str_pad($cards['atrasos'], 2, '0', STR_PAD_LEFT) }}</p></div>
                <div class="w-10 h-10 bg-rose-50 rounded-full flex items-center justify-center text-rose-500"><i class="fas fa-exclamation-triangle"></i></div>
            </div>
            <div class="bg-white rounded-2xl p-4 border border-emerald-100 shadow-sm flex items-center justify-between">
                <div><p class="text-[10px] font-black uppercase text-emerald-400">C/ Pendência</p><p class="text-2xl font-black text-emerald-600">{{ str_pad($cards['pendentes'], 2, '0', STR_PAD_LEFT) }}</p></div>
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
                    @forelse($agendamentosHoje as $ag)
                        @php
                            // Cor da borda/estado conforme status_cor do agendamento.
                            $cor = match($ag->status_cor) {
                                'green'  => ['borda' => 'border-emerald-500', 'bg' => 'bg-white hover:bg-slate-50', 'hora' => 'text-slate-800', 'ic' => 'bg-emerald-100 text-emerald-600', 'icone' => 'fa-check'],
                                'orange', 'yellow' => ['borda' => 'border-orange-500', 'bg' => 'bg-orange-50', 'hora' => 'text-orange-700', 'ic' => 'bg-orange-200 text-orange-700', 'icone' => 'fa-cut'],
                                default  => ['borda' => 'border-blue-500', 'bg' => 'bg-white hover:bg-slate-50', 'hora' => 'text-slate-800', 'ic' => 'bg-blue-100 text-blue-600', 'icone' => 'fa-clock'],
                            };
                            $temPendencia = $ag->cliente_id && $clientesComPendencia->contains($ag->cliente_id);
                            $atrasado = $ag->data_hora->isPast() && $ag->status_cor !== 'green';
                        @endphp
                        <div class="group flex items-center p-4 {{ $cor['bg'] }} border-l-4 {{ $cor['borda'] }} rounded-r-2xl border-y border-r border-slate-100 shadow-sm transition-colors">
                            <div class="font-black text-lg {{ $cor['hora'] }} w-20">{{ $ag->data_hora->format('H:i') }}</div>
                            <div class="flex-1 min-w-0">
                                <p class="font-bold text-slate-900 text-sm uppercase truncate">{{ $ag->cliente?->nome ?? 'Cliente' }}</p>
                                @if($ag->evento)
                                    <p class="text-xs text-slate-500 font-medium">Evento: {{ $ag->evento->nome_evento }}@if($ag->evento->data_evento) ({{ $ag->evento->data_evento->format('d/m/Y') }})@endif</p>
                                @endif
                                <div class="mt-2 flex flex-wrap gap-2">
                                    <span class="bg-slate-100 text-slate-600 text-[9px] font-black uppercase px-2 py-1 rounded">{{ $ag->tipo_atendimento }}</span>
                                    @if($ag->sala_atendimento)
                                        <span class="bg-purple-50 text-purple-600 text-[9px] font-black uppercase px-2 py-1 rounded"><i class="fas fa-door-open mr-1"></i>{{ $ag->sala_atendimento }}</span>
                                    @endif
                                    @if($atrasado)
                                        <span class="bg-rose-50 text-rose-600 text-[9px] font-black uppercase px-2 py-1 rounded"><i class="fas fa-exclamation-triangle mr-1"></i>Atrasado</span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right pl-2">
                                <div class="w-8 h-8 rounded-full {{ $cor['ic'] }} flex items-center justify-center mb-2 ml-auto"><i class="fas {{ $cor['icone'] }}"></i></div>
                                @if($ag->acervo)
                                    <span class="block text-[10px] font-bold text-slate-400">{{ $ag->acervo->codigo ?? $ag->acervo->nome }}</span>
                                @endif
                                @if($temPendencia)
                                    <a href="{{ route('clientes.show', $ag->cliente_id) }}" class="block text-[10px] font-bold text-rose-500 hover:underline">Pendência financeira</a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 text-slate-400">
                            <i class="fas fa-calendar-day text-4xl mb-3 opacity-30"></i>
                            <p class="font-medium">Nenhum atendimento agendado para hoje.</p>
                            <button onclick="document.getElementById('modalNovoAgendamento').classList.remove('hidden')" class="mt-3 text-[#fbbf24] font-bold text-sm hover:underline">+ Criar o primeiro</button>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- A Grande Sacada: Linha do Tempo do Evento (REAL) -->
            <div class="bg-slate-900 rounded-[2.5rem] p-8 shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 right-0 p-8 opacity-10 text-[#fbbf24]"><i class="fas fa-ring text-6xl"></i></div>

                <h2 class="text-xl font-black uppercase tracking-tighter text-white mb-2 relative z-10">Dossiê do Evento</h2>

                @if($eventoDossie)
                    <p class="text-xs font-medium text-[#fbbf24] mb-2 relative z-10 uppercase tracking-widest">{{ $eventoDossie->nome_evento }}</p>

                    @php
                        $diasRestantes = $eventoDossie->data_evento ? now()->startOfDay()->diffInDays($eventoDossie->data_evento, false) : null;
                    @endphp
                    @if(!is_null($diasRestantes) && $diasRestantes >= 0)
                        <p class="text-[11px] text-slate-400 mb-8 relative z-10">
                            @if($diasRestantes === 0)
                                <span class="text-[#fbbf24] font-bold">É HOJE! 🎉</span>
                            @else
                                Faltam <span class="text-white font-bold">{{ $diasRestantes }}</span> dia(s) para o grande dia.
                            @endif
                        </p>
                    @else
                        <div class="mb-8"></div>
                    @endif

                    <!-- Timeline Vertical montada dos agendamentos reais -->
                    <div class="relative border-l-2 border-slate-700 ml-3 space-y-6 z-10">
                        @forelse($eventoDossie->agendamentos as $marco)
                            @php
                                $passou = $marco->data_hora->isPast();
                                $ehHoje = $marco->data_hora->isToday();
                                $cor = $ehHoje ? 'bg-blue-500 animate-pulse' : ($passou ? 'bg-emerald-500' : 'bg-slate-700');
                            @endphp
                            <div class="relative pl-6 {{ (!$passou && !$ehHoje) ? 'opacity-50' : '' }}">
                                <div class="absolute w-4 h-4 {{ $cor }} rounded-full -left-[9px] top-1 ring-4 ring-slate-900"></div>
                                <p class="text-[10px] font-bold {{ $ehHoje ? 'text-[#fbbf24]' : 'text-slate-400' }}">
                                    {{ $ehHoje ? 'HOJE - '.$marco->data_hora->format('H:i') : $marco->data_hora->format('d/m/Y') }}
                                </p>
                                <p class="text-sm font-bold text-white">{{ $marco->tipo_atendimento }}</p>
                                <p class="text-xs text-slate-400 mt-0.5">{{ $marco->cliente?->nome }}@if($marco->sala_atendimento) · {{ $marco->sala_atendimento }}@endif</p>
                            </div>
                        @empty
                            <div class="relative pl-6 opacity-70">
                                <div class="absolute w-4 h-4 bg-slate-700 rounded-full -left-[9px] top-1 ring-4 ring-slate-900"></div>
                                <p class="text-sm text-slate-400">Nenhum agendamento neste evento ainda.</p>
                            </div>
                        @endforelse

                        <!-- O grande dia -->
                        @if($eventoDossie->data_evento)
                            <div class="relative pl-6 {{ $eventoDossie->data_evento->isFuture() ? 'opacity-80' : '' }}">
                                <div class="absolute w-4 h-4 bg-[#fbbf24] rounded-full -left-[9px] top-1 ring-4 ring-slate-900"></div>
                                <p class="text-[10px] font-bold text-[#fbbf24]">{{ $eventoDossie->data_evento->format('d/m/Y') }}</p>
                                <p class="text-sm font-black text-[#fbbf24] uppercase tracking-widest">{{ $eventoDossie->tipo_evento ?: 'O Evento' }}</p>
                            </div>
                        @endif
                    </div>

                    <a href="{{ route('clientes.index') }}" class="block text-center w-full mt-8 border border-slate-700 text-slate-300 py-3 rounded-xl text-xs font-bold uppercase hover:bg-slate-800 transition">Ver Clientes do Evento</a>
                @else
                    <div class="relative z-10 text-center py-10">
                        <i class="fas fa-ring text-4xl text-slate-700 mb-4"></i>
                        <p class="text-slate-400 text-sm font-medium">Nenhum evento ativo no momento.</p>
                        <p class="text-slate-600 text-xs mt-1">Crie um evento ao agendar um atendimento.</p>
                    </div>
                @endif
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