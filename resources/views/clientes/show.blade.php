<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-slate-800 leading-tight uppercase tracking-tighter">
                Ficha do <span class="text-[#fbbf24]">Cliente</span>
            </h2>
            <a href="{{ route('clientes.index') }}" class="text-slate-400 hover:text-slate-600 font-bold text-sm uppercase tracking-widest transition">
                &larr; Voltar para Lista
            </a>
        </div>
    </x-slot>

    <div class="py-8 relative">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- ALERTA DE SUCESSO -->
            @if (session('success'))
                <div class="p-6 bg-emerald-50 border border-emerald-100 rounded-[2rem] shadow-sm flex items-center mb-6">
                    <div class="bg-emerald-100 text-emerald-600 w-12 h-12 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-emerald-800 font-black text-sm uppercase tracking-wider mb-1">Tudo certo!</h3>
                        <p class="text-emerald-600 font-medium text-sm">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <!-- CABEÇALHO DO CLIENTE -->
            <div class="bg-slate-900 rounded-[2.5rem] p-8 shadow-2xl relative overflow-hidden flex flex-col md:flex-row items-center justify-between">
                <div class="absolute -right-10 -bottom-10 text-slate-800 opacity-50 pointer-events-none">
                    <i class="fas fa-id-badge text-9xl"></i>
                </div>
                
                <div class="flex items-center relative z-10">
                    <div class="w-20 h-20 bg-slate-800 rounded-full flex items-center justify-center text-[#fbbf24] text-3xl font-black mr-6 border-4 border-slate-700 shadow-inner">
                        {{ substr($cliente->nome, 0, 1) }}
                    </div>
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <span class="bg-[#fbbf24] text-slate-900 text-[10px] font-black uppercase tracking-widest px-2 py-1 rounded-md">CLI-{{ str_pad($cliente->id, 5, '0', STR_PAD_LEFT) }}</span>
                            <span class="bg-slate-800 text-slate-300 text-[10px] font-black uppercase tracking-widest px-2 py-1 rounded-md">{{ $cliente->tipo }}</span>
                        </div>
                        <h1 class="text-3xl font-black text-white tracking-tighter">{{ $cliente->nome }}</h1>
                    </div>
                </div>

                <div class="flex gap-4 mt-6 md:mt-0 relative z-10">
                    <a href="https://api.whatsapp.com/send?phone=55{{ preg_replace('/[^0-9]/', '', $cliente->telefone) }}" target="_blank" class="bg-emerald-500 hover:bg-emerald-400 text-white px-6 py-4 rounded-2xl font-black text-xs uppercase tracking-widest transition shadow-lg flex items-center cursor-pointer">
                        <i class="fab fa-whatsapp text-lg mr-2"></i> WhatsApp
                    </a>
                    <a href="{{ route('encomendas.create', ['cliente_id' => $cliente->id]) }}" class="bg-[#fbbf24] hover:bg-amber-300 text-slate-900 px-6 py-4 rounded-2xl font-black text-xs uppercase tracking-widest transition shadow-lg flex items-center">
                        <i class="fas fa-pen-ruler text-sm mr-2"></i> Sob Medida
                    </a>
                    <a href="{{ route('clientes.edit', $cliente->id) }}" class="bg-slate-800 hover:bg-slate-700 text-white px-6 py-4 rounded-2xl font-black text-xs uppercase tracking-widest transition shadow-lg flex items-center">
                        <i class="fas fa-pen text-sm mr-2"></i> Editar
                    </a>
                </div>
            </div>

            <!-- MENU DE ABAS INTERATIVAS -->
            <div class="bg-white rounded-2xl p-2 shadow-sm border border-slate-100 flex overflow-x-auto hide-scrollbar">
                <button type="button" onclick="mudarAba('dados')" id="btn_dados" class="btn-aba bg-slate-900 text-[#fbbf24] px-6 py-3 rounded-xl font-bold text-xs uppercase tracking-widest whitespace-nowrap transition">
                    <i class="fas fa-info-circle mr-2"></i> Dados
                </button>
                <button type="button" onclick="mudarAba('medidas')" id="btn_medidas" class="btn-aba text-slate-500 hover:bg-slate-50 px-6 py-3 rounded-xl font-bold text-xs uppercase tracking-widest whitespace-nowrap transition">
                    <i class="fas fa-ruler-combined mr-2"></i> Medidas
                </button>
                <button type="button" onclick="mudarAba('eventos')" id="btn_eventos" class="btn-aba text-slate-500 hover:bg-slate-50 px-6 py-3 rounded-xl font-bold text-xs uppercase tracking-widest whitespace-nowrap transition">
                    <i class="fas fa-calendar-alt mr-2"></i> Eventos
                </button>
                <button type="button" onclick="mudarAba('locacoes')" id="btn_locacoes" class="btn-aba text-slate-500 hover:bg-slate-50 px-6 py-3 rounded-xl font-bold text-xs uppercase tracking-widest whitespace-nowrap transition">
                    <i class="fas fa-tshirt mr-2"></i> Locações
                </button>
                <button type="button" onclick="mudarAba('historico')" id="btn_historico" class="btn-aba text-slate-500 hover:bg-slate-50 px-6 py-3 rounded-xl font-bold text-xs uppercase tracking-widest whitespace-nowrap transition">
                    <i class="fas fa-history mr-2"></i> Histórico
                </button>
            </div>

            <!-- ABA 1: DADOS -->
            <div id="aba_dados" class="conteudo-aba bg-white rounded-[2rem] p-8 shadow-sm border border-slate-100 block">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Contato Principal</h3>
                            <p class="text-lg font-bold text-slate-800">{{ $cliente->telefone }}</p>
                        </div>
                        <div>
                            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">CPF</h3>
                            <p class="text-lg font-bold text-slate-800">{{ $cliente->cpf ?? 'Não informado' }}</p>
                        </div>
                    </div>
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Endereço Completo</h3>
                            <p class="text-sm font-bold text-slate-800 bg-slate-50 p-4 rounded-xl border border-slate-100">
                                @if($cliente->rua)
                                    {{ $cliente->rua }}, {{ $cliente->numero }} {{ $cliente->complemento }}<br>
                                    {{ $cliente->bairro }} - {{ $cliente->cidade }}/{{ $cliente->estado }}
                                @else
                                    <span class="text-slate-400 italic">Nenhum endereço cadastrado.</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ABA 2: MEDIDAS -->
            <div id="aba_medidas" class="conteudo-aba bg-white rounded-[2rem] p-8 shadow-sm border border-slate-100 hidden">
                <div class="flex justify-between items-center mb-8 border-b border-slate-100 pb-6">
                    <div>
                        <h3 class="text-xl font-black text-slate-800 tracking-tighter">Histórico de Medidas</h3>
                        <p class="text-sm font-medium text-slate-500">Nunca apague uma medida antiga. Crie uma nova medição para acompanhar o histórico.</p>
                    </div>
                    <!-- Botão que abre a Janela Modal -->
                    <button type="button" onclick="abrirModalMedida()" class="bg-slate-900 text-[#fbbf24] px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition shadow-lg flex items-center">
                        <i class="fas fa-plus mr-2"></i> Nova Medição
                    </button>
                </div>

                <!-- Lista de Histórico -->
                <div class="space-y-4">
                    @forelse($cliente->medidas as $medida)
                        <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100 flex flex-col md:flex-row justify-between items-center hover:shadow-md transition">
                            <div class="flex items-center mb-4 md:mb-0">
                                <div class="bg-slate-200 text-slate-500 w-12 h-12 rounded-full flex items-center justify-center mr-4 text-xl">
                                    <i class="fas fa-ruler"></i>
                                </div>
                                <div>
                                    <p class="font-black text-slate-800 text-lg">{{ \Carbon\Carbon::parse($medida->data_medicao)->format('d/m/Y') }}</p>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Registrado por: {{ $medida->responsavel_medicao ?? 'Ateliê' }}</p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                                @if($medida->busto_torax)
                                <div><p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Busto</p><p class="font-black text-slate-800">{{ $medida->busto_torax }}cm</p></div>
                                @endif
                                @if($medida->cintura)
                                <div><p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Cintura</p><p class="font-black text-slate-800">{{ $medida->cintura }}cm</p></div>
                                @endif
                                @if($medida->quadril)
                                <div><p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Quadril</p><p class="font-black text-slate-800">{{ $medida->quadril }}cm</p></div>
                                @endif
                                @if($medida->comprimento_total)
                                <div><p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Comprimento</p><p class="font-black text-slate-800">{{ $medida->comprimento_total }}cm</p></div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-10">
                            <i class="fas fa-ruler-combined text-4xl text-slate-200 mb-4"></i>
                            <h3 class="text-lg font-black text-slate-800 mb-1">Nenhuma medida registrada</h3>
                            <p class="text-slate-500 text-sm">Clique no botão "Nova Medição" acima para registrar a primeira prova.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Abas Restantes (Eventos, Locações, Histórico) continuam ocultas por enquanto -->
            <div id="aba_eventos" class="conteudo-aba hidden"><div class="bg-white rounded-[2rem] p-8 shadow-sm border border-slate-100 text-center py-10"><i class="fas fa-glass-cheers text-4xl text-slate-200 mb-4"></i><h3 class="text-xl font-black text-slate-800 mb-2">Eventos do Cliente</h3><p class="text-slate-500">Módulo em construção.</p></div></div>
            <div id="aba_locacoes" class="conteudo-aba hidden"><div class="bg-white rounded-[2rem] p-8 shadow-sm border border-slate-100 text-center py-10"><i class="fas fa-tags text-4xl text-slate-200 mb-4"></i><h3 class="text-xl font-black text-slate-800 mb-2">Locações e Provas</h3><p class="text-slate-500">Módulo em construção.</p></div></div>
            <div id="aba_historico" class="conteudo-aba hidden"><div class="bg-white rounded-[2rem] p-8 shadow-sm border border-slate-100 text-center py-10"><i class="fas fa-stream text-4xl text-slate-200 mb-4"></i><h3 class="text-xl font-black text-slate-800 mb-2">Linha do Tempo</h3><p class="text-slate-500">Módulo em construção.</p></div></div>

        </div>
    </div>

    <!-- JANELA MODAL PARA NOVA MEDIÇÃO COM OPÇÃO SOB MEDIDA -->
    <div id="modal_medida" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-4xl border border-slate-100 my-8">
            <div class="bg-slate-900 p-6 flex justify-between items-center rounded-t-[2rem]">
                <h3 class="text-lg font-black text-white uppercase tracking-tighter"><i class="fas fa-plus text-[#fbbf24] mr-2"></i> Nova Ficha de Medidas</h3>
                <button type="button" onclick="fecharModalMedida()" class="text-slate-400 hover:text-white transition"><i class="fas fa-times text-xl"></i></button>
            </div>
            
            <form action="{{ route('medidas.store', $cliente->id) }}" method="POST" class="p-8">
                @csrf
                
                <!-- MEDIDAS BÁSICAS (Padrão) -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Data da Medição *</label>
                        <input type="date" name="data_medicao" value="{{ date('Y-m-d') }}" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Responsável (Estilista)</label>
                        <input type="text" name="responsavel_medicao" placeholder="Quem tirou as medidas?" class="w-full p-3 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Busto (cm)</label>
                        <input type="text" name="busto_torax" placeholder="Ex: 92" class="w-full p-3 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Cintura (cm)</label>
                        <input type="text" name="cintura" placeholder="Ex: 75" class="w-full p-3 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Quadril (cm)</label>
                        <input type="text" name="quadril" placeholder="Ex: 102" class="w-full p-3 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Comprimento Total (cm)</label>
                        <input type="text" name="comprimento_total" placeholder="Ex: 155" class="w-full p-3 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none">
                    </div>
                </div>

                <!-- CHAVE DO SOB MEDIDA -->
                <div class="bg-amber-50 border border-amber-100 p-4 rounded-xl mb-6 flex items-center justify-between cursor-pointer hover:bg-amber-100 transition" onclick="toggleSobMedida()">
                    <div>
                        <h4 class="text-amber-800 font-black text-sm uppercase tracking-wider mb-1"><i class="fas fa-cut mr-2"></i>Ficha Sob Medida (Alta Costura)</h4>
                        <p class="text-amber-600 text-xs font-medium">Ative para exibir todos os campos avançados de modelagem.</p>
                    </div>
                    <div class="text-2xl text-amber-500">
                        <i class="fas fa-toggle-off transition-all" id="icone_toggle"></i>
                    </div>
                </div>

                <!-- CAMPOS AVANÇADOS (Escondidos por padrão) -->
                <div id="bloco_sob_medida" class="hidden space-y-6 mb-6">
                    
                    <h5 class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 pb-2">1. Tronco e Costas</h5>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div><label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Alto Busto</label><input type="text" name="alto_busto" class="w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-sm"></div>
                        <div><label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Altura do Busto</label><input type="text" name="altura_busto" class="w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-sm"></div>
                        <div><label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Distância do Busto</label><input type="text" name="distancia_busto" class="w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-sm"></div>
                        <div><label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Cava a Cava</label><input type="text" name="cava_a_cava" class="w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-sm"></div>
                        <div><label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Ombro a Ombro</label><input type="text" name="ombro_a_ombro" class="w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-sm"></div>
                        <div><label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Centro Frente</label><input type="text" name="centro_frente" class="w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-sm"></div>
                        <div><label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Centro Costas</label><input type="text" name="centro_costas" class="w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-sm"></div>
                        <div><label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Altura Corpo</label><input type="text" name="altura_corpo" class="w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-sm"></div>
                    </div>

                    <h5 class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 pb-2 mt-4">2. Braços e Mangas</h5>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div><label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Circunferência Cava</label><input type="text" name="circ_cava" class="w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-sm"></div>
                        <div><label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Cotovelo Dobrado</label><input type="text" name="cotovelo_dobrado" class="w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-sm"></div>
                        <div><label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Altura Cotovelo</label><input type="text" name="altura_cotovelo" class="w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-sm"></div>
                        <div><label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Cabeça da Manga</label><input type="text" name="cabeca_manga" class="w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-sm"></div>
                        <div><label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Punho</label><input type="text" name="punho" class="w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-sm"></div>
                        <div><label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Mão</label><input type="text" name="mao" class="w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-sm"></div>
                    </div>

                    <h5 class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 pb-2 mt-4">3. Quadril e Comprimentos Específicos</h5>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div><label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Altura do Quadril</label><input type="text" name="altura_quadril" class="w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-sm"></div>
                        <div><label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Quadril Alto</label><input type="text" name="quadril_alto" class="w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-sm"></div>
                        <div><label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Quadril Baixo</label><input type="text" name="quadril_baixo" class="w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-sm"></div>
                        <div><label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Altura Quad. Alto</label><input type="text" name="altura_q_alto" class="w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-sm"></div>
                        <div><label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Comp. Saia</label><input type="text" name="comp_saia" class="w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-sm"></div>
                        <div><label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Comp. Cauda</label><input type="text" name="comp_cauda" class="w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-sm"></div>
                        <div><label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Diagonais</label><input type="text" name="diagonais" class="w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-sm"></div>
                        <div><label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Lateral</label><input type="text" name="lateral" class="w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-sm"></div>
                    </div>
                </div>
                
                <div class="pt-6 border-t border-slate-100 flex justify-end gap-4">
                    <button type="button" onclick="fecharModalMedida()" class="px-6 py-3 font-bold text-slate-500 hover:bg-slate-100 rounded-xl transition">Cancelar</button>
                    <button type="submit" class="bg-slate-900 text-[#fbbf24] px-8 py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 shadow-xl transition">
                        Salvar Histórico
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- SCRIPTS -->
    <script>
        function mudarAba(nomeDaAba) {
            let conteudos = document.querySelectorAll('.conteudo-aba');
            conteudos.forEach(c => { c.classList.add('hidden'); c.classList.remove('block'); });

            let botoes = document.querySelectorAll('.btn-aba');
            botoes.forEach(b => { b.classList.remove('bg-slate-900', 'text-[#fbbf24]'); b.classList.add('text-slate-500'); });

            document.getElementById('aba_' + nomeDaAba).classList.remove('hidden');
            document.getElementById('aba_' + nomeDaAba).classList.add('block');

            let botaoAtivo = document.getElementById('btn_' + nomeDaAba);
            botaoAtivo.classList.remove('text-slate-500');
            botaoAtivo.classList.add('bg-slate-900', 'text-[#fbbf24]');
        }

        function abrirModalMedida() {
            document.getElementById('modal_medida').classList.remove('hidden');
        }
        
        function fecharModalMedida() {
            document.getElementById('modal_medida').classList.add('hidden');
        }

        // Função UX para expandir o formulário de Alta Costura
        function toggleSobMedida() {
            const bloco = document.getElementById('bloco_sob_medida');
            const icone = document.getElementById('icone_toggle');
            
            if (bloco.classList.contains('hidden')) {
                bloco.classList.remove('hidden');
                icone.classList.remove('fa-toggle-off');
                icone.classList.add('fa-toggle-on', 'text-emerald-500');
            } else {
                bloco.classList.add('hidden');
                icone.classList.remove('fa-toggle-on', 'text-emerald-500');
                icone.classList.add('fa-toggle-off');
            }
        }

        @if(session('aba_ativa') == 'medidas')
            window.onload = function() { mudarAba('medidas'); };
        @endif
    </script>
</x-app-layout>