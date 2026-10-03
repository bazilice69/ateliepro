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

                    @if($clientes->isEmpty())
                        <div class="mb-8 p-5 bg-sky-50 border border-sky-100 rounded-2xl flex items-start gap-3">
                            <i class="fas fa-circle-info text-sky-500 text-lg mt-0.5"></i>
                            <div>
                                <p class="font-bold text-sky-800 text-sm">Você ainda não tem clientes cadastrados.</p>
                                <p class="text-sky-600 text-xs mt-1">Use o botão <strong>“+ Cadastrar cliente”</strong> abaixo para criar a cliente na hora, sem sair desta tela.</p>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('locacao.store') }}" method="POST" class="space-y-8"
                          x-data="locacaoForm({{ Js::from($clientes->map(fn($c) => ['id' => $c->id, 'nome' => $c->nome])) }}, '{{ old('cliente_id') }}')">
                        @csrf

                        <!-- BLOCO 1: CLIENTE E PEÇA -->
                        <div class="p-6 bg-slate-50 rounded-2xl border border-slate-200">
                            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">1. Identificação</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <label class="block text-sm font-bold text-slate-700">Cliente</label>
                                        <button type="button" @click="abrirModal = true" class="text-[11px] font-black text-[#b8860b] hover:text-amber-700 uppercase tracking-wide">
                                            <i class="fas fa-plus mr-1"></i> Cadastrar cliente
                                        </button>
                                    </div>
                                    <select name="cliente_id" x-model="clienteId" required class="w-full p-3 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none">
                                        <option value="">Selecione a cliente...</option>
                                        <template x-for="c in clientes" :key="c.id">
                                            <option :value="c.id" x-text="c.nome"></option>
                                        </template>
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

                        <!-- Dica sobre o contrato -->
                        <div class="flex items-center gap-2 text-xs text-slate-400 bg-slate-50 rounded-xl px-4 py-3 border border-slate-100">
                            <i class="fas fa-file-signature text-slate-300"></i>
                            <span>Depois de registrar a locação, o <strong>contrato</strong> é gerado na central dela (ou no menu <strong>Contratos</strong>), com os dados preenchidos automaticamente.</span>
                        </div>

                        <div class="flex justify-end pt-4">
                            <button type="submit" class="bg-slate-900 text-[#fbbf24] px-8 py-4 rounded-2xl font-black text-sm uppercase tracking-widest hover:bg-slate-800 transition shadow-xl cursor-pointer">
                                Registrar Locação e Bloquear Datas
                            </button>
                        </div>

                        <!-- MODAL: cadastro rápido de cliente -->
                        <div x-show="abrirModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" @click.self="abrirModal = false">
                            <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-md p-8">
                                <div class="flex items-center justify-between mb-6">
                                    <h3 class="font-black text-slate-800 text-lg uppercase tracking-tighter">Cadastro rápido de cliente</h3>
                                    <button type="button" @click="abrirModal = false" class="text-slate-400 hover:text-slate-700 text-xl">&times;</button>
                                </div>
                                <p class="text-xs text-slate-400 mb-5">Só o essencial agora — os outros dados você completa depois na ficha da cliente.</p>

                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-600 mb-1">Nome *</label>
                                        <input type="text" x-model="novo.nome" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none" placeholder="Nome da cliente">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-600 mb-1">Telefone / WhatsApp *</label>
                                        <input type="text" x-model="novo.telefone" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none" placeholder="(00) 00000-0000">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-600 mb-1">CPF (opcional)</label>
                                        <input type="text" x-model="novo.cpf" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none" placeholder="000.000.000-00">
                                    </div>
                                    <p x-show="erro" x-text="erro" x-cloak class="text-rose-600 text-xs font-medium"></p>
                                </div>

                                <div class="flex justify-end gap-3 mt-6">
                                    <button type="button" @click="abrirModal = false" class="px-5 py-2.5 font-bold text-slate-500 hover:bg-slate-100 rounded-xl text-sm">Cancelar</button>
                                    <button type="button" @click="salvarCliente()" :disabled="salvando" class="bg-slate-900 text-[#fbbf24] px-6 py-2.5 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition disabled:opacity-50">
                                        <span x-show="!salvando">Salvar e usar</span>
                                        <span x-show="salvando" x-cloak>Salvando...</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script>
        function locacaoForm(clientesIniciais, clienteSelecionado) {
            return {
                clientes: clientesIniciais || [],
                clienteId: clienteSelecionado || '',
                abrirModal: false,
                salvando: false,
                erro: '',
                novo: { nome: '', telefone: '', cpf: '' },

                async salvarCliente() {
                    this.erro = '';
                    if (!this.novo.nome || !this.novo.telefone) {
                        this.erro = 'Preencha o nome e o telefone.';
                        return;
                    }
                    this.salvando = true;
                    try {
                        const resp = await fetch("{{ route('clientes.rapido') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                            },
                            body: JSON.stringify(this.novo),
                        });
                        if (!resp.ok) {
                            const data = await resp.json().catch(() => ({}));
                            // Mostra o primeiro erro de validação, se houver.
                            const primeiro = data.errors ? Object.values(data.errors)[0][0] : (data.message || 'Não foi possível cadastrar.');
                            this.erro = primeiro;
                            this.salvando = false;
                            return;
                        }
                        const cliente = await resp.json();
                        // Adiciona na lista e já seleciona.
                        this.clientes.push(cliente);
                        this.clienteId = String(cliente.id);
                        this.abrirModal = false;
                        this.novo = { nome: '', telefone: '', cpf: '' };
                    } catch (e) {
                        this.erro = 'Erro de conexão. Tente novamente.';
                    }
                    this.salvando = false;
                },
            };
        }
    </script>
</x-app-layout>