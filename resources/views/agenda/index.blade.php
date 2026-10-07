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
            
            <!-- CALENDÁRIO (FullCalendar) + filtros por tipo -->
            <div class="lg:col-span-2 bg-white rounded-[2.5rem] p-5 md:p-8 shadow-sm border border-slate-100">
                <!-- Filtros de visualização -->
                <div class="flex flex-wrap gap-2 mb-5" id="filtrosAgenda">
                    <button data-cat="todos"       class="filtro-btn ativo text-[10px] md:text-xs font-black uppercase px-3 py-2 rounded-xl border transition">Todos</button>
                    <button data-cat="atendimento" class="filtro-btn text-[10px] md:text-xs font-black uppercase px-3 py-2 rounded-xl border transition"><span class="inline-block w-2 h-2 rounded-full align-middle mr-1" style="background:#64748b"></span>Atendimentos</button>
                    <button data-cat="prova"       class="filtro-btn text-[10px] md:text-xs font-black uppercase px-3 py-2 rounded-xl border transition"><span class="inline-block w-2 h-2 rounded-full align-middle mr-1" style="background:#3b82f6"></span>Provas</button>
                    <button data-cat="retirada"    class="filtro-btn text-[10px] md:text-xs font-black uppercase px-3 py-2 rounded-xl border transition"><span class="inline-block w-2 h-2 rounded-full align-middle mr-1" style="background:#10b981"></span>Retiradas</button>
                    <button data-cat="ajuste"      class="filtro-btn text-[10px] md:text-xs font-black uppercase px-3 py-2 rounded-xl border transition"><span class="inline-block w-2 h-2 rounded-full align-middle mr-1" style="background:#a855f7"></span>Ajustes</button>
                    <button data-cat="aniversario" class="filtro-btn text-[10px] md:text-xs font-black uppercase px-3 py-2 rounded-xl border transition"><span class="inline-block w-2 h-2 rounded-full align-middle mr-1" style="background:#ec4899"></span>Aniversários</button>
                </div>

                <div id="calendario"></div>
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

    {{-- ===== FullCalendar (calendário mês/semana/dia) ===== --}}
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    <style>
        /* Deixa o calendário no tom da marca */
        #calendario { --fc-border-color: #eef2f7; --fc-today-bg-color: #fffbeb; --fc-event-border-color: transparent; }
        #calendario .fc .fc-button-primary { background:#0f172a; border-color:#0f172a; text-transform:uppercase; font-size:11px; font-weight:800; letter-spacing:.05em; }
        #calendario .fc .fc-button-primary:not(:disabled).fc-button-active,
        #calendario .fc .fc-button-primary:hover { background:#1e293b; border-color:#1e293b; }
        #calendario .fc .fc-toolbar-title { font-weight:800; color:#0f172a; text-transform:capitalize; }
        #calendario .fc-daygrid-event { border-radius:9999px; padding:1px 6px; font-weight:700; font-size:11px; }
        #calendario .fc-event { cursor:pointer; }
        .filtro-btn { border-color:#e2e8f0; color:#64748b; background:#fff; }
        .filtro-btn.ativo { background:#0f172a; color:#fff; border-color:#0f172a; }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const el = document.getElementById('calendario');
            if (!el || typeof FullCalendar === 'undefined') return;

            const todosEventos = @json($eventosCalendario);
            let filtroAtual = 'todos';

            function eventosFiltrados() {
                if (filtroAtual === 'todos') return todosEventos;
                return todosEventos.filter(e => (e.extendedProps?.categoria) === filtroAtual);
            }

            const calendar = new FullCalendar.Calendar(el, {
                locale: 'pt-br',
                initialView: window.innerWidth < 768 ? 'listWeek' : 'dayGridMonth',
                height: 'auto',
                headerToolbar: {
                    left: 'prev,next hoje',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                customButtons: {
                    hoje: { text: 'Hoje', click: () => calendar.today() }
                },
                buttonText: { month: 'Mês', week: 'Semana', day: 'Dia', list: 'Lista' },
                events: eventosFiltrados(),
                eventClick: function (info) {
                    const url = info.event.extendedProps?.url;
                    if (url) { info.jsEvent.preventDefault(); window.location.href = url; }
                },
                eventDidMount: function (info) {
                    const p = info.event.extendedProps || {};
                    const partes = [];
                    if (p.evento) partes.push('Evento: ' + p.evento);
                    if (p.sala) partes.push(p.sala);
                    if (partes.length) info.el.setAttribute('title', partes.join(' · '));
                },
            });
            calendar.render();

            // Filtros por categoria
            document.querySelectorAll('#filtrosAgenda .filtro-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    document.querySelectorAll('#filtrosAgenda .filtro-btn').forEach(b => b.classList.remove('ativo'));
                    this.classList.add('ativo');
                    filtroAtual = this.dataset.cat;
                    calendar.removeAllEvents();
                    calendar.addEventSource(eventosFiltrados());
                });
            });
        });
    </script>
</x-app-layout>