<x-app-layout>
    <div class="py-8 relative">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- CABEÇALHO DO FINANCEIRO (Agora dentro da tela, sem bloqueios!) -->
            <div class="flex justify-between items-center mb-2">
                <h2 class="font-black text-3xl text-slate-800 leading-tight uppercase tracking-tighter">
                    Central <span class="text-emerald-500">Financeira</span>
                </h2>
                <div class="flex gap-4">
                    <button onclick="abrirModal('Despesa')" class="bg-rose-500 hover:bg-rose-400 text-white px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest transition shadow-lg flex items-center">
                        <i class="fas fa-arrow-down mr-2"></i> Nova Despesa
                    </button>
                    <button onclick="abrirModal('Receita')" class="bg-emerald-500 hover:bg-emerald-400 text-white px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest transition shadow-lg flex items-center">
                        <i class="fas fa-arrow-up mr-2"></i> Nova Receita
                    </button>
                </div>
            </div>

            <!-- MENSAGEM DE SUCESSO -->
            @if(session('success'))
                <div class="bg-emerald-100 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded-xl shadow-sm font-bold text-sm flex items-center justify-between">
                    <div><i class="fas fa-check-circle mr-2"></i> {{ session('success') }}</div>
                    <span class="text-xs uppercase tracking-widest opacity-75">AteliêPro</span>
                </div>
            @endif

            <!-- CARTÕES REAIS DO BANCO DE DADOS -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex flex-col relative overflow-hidden group">
                    <div class="absolute -right-4 -bottom-4 text-emerald-50 opacity-50 group-hover:scale-110 transition-transform duration-500"><i class="fas fa-wallet text-8xl"></i></div>
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 relative z-10">Recebido (Pagos)</h3>
                    <p class="text-3xl font-black text-slate-800 relative z-10">R$ {{ number_format($recebidoMes, 2, ',', '.') }}</p>
                </div>

                <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex flex-col relative overflow-hidden group">
                    <div class="absolute -right-4 -bottom-4 text-amber-50 opacity-50 group-hover:scale-110 transition-transform duration-500"><i class="fas fa-clock text-8xl"></i></div>
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 relative z-10">A Receber (Pendentes)</h3>
                    <p class="text-3xl font-black text-slate-800 relative z-10">R$ {{ number_format($aReceber, 2, ',', '.') }}</p>
                </div>

                <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-rose-100 flex flex-col relative overflow-hidden group">
                    <div class="absolute -right-4 -bottom-4 text-rose-50 opacity-50 group-hover:scale-110 transition-transform duration-500"><i class="fas fa-exclamation-circle text-8xl"></i></div>
                    <h3 class="text-[10px] font-black text-rose-400 uppercase tracking-widest mb-2 relative z-10">Em Atraso (Vencidos)</h3>
                    <p class="text-3xl font-black text-rose-600 relative z-10">R$ {{ number_format($emAtraso, 2, ',', '.') }}</p>
                </div>

                <div class="bg-slate-900 p-6 rounded-[2rem] shadow-xl border border-slate-800 flex flex-col relative overflow-hidden group">
                    <div class="absolute -right-4 -bottom-4 text-slate-800 opacity-50 group-hover:scale-110 transition-transform duration-500"><i class="fas fa-chart-line text-8xl"></i></div>
                    <h3 class="text-[10px] font-black text-[#fbbf24] uppercase tracking-widest mb-2 relative z-10">Saldo Líquido Caixa</h3>
                    <p class="text-3xl font-black text-white relative z-10">R$ {{ number_format($saldoPrevisto, 2, ',', '.') }}</p>
                </div>
            </div>

            <!-- CALCULADORA DE PRECIFICAÇÃO (interativa, Alpine.js) -->
            <div x-data="calculadoraPreco()" class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-100">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-11 h-11 rounded-2xl bg-slate-900 text-[#fbbf24] flex items-center justify-center"><i class="fas fa-calculator"></i></div>
                    <div>
                        <h3 class="text-xl font-black text-slate-800 tracking-tighter">Calculadora de Preço</h3>
                        <p class="text-sm font-medium text-slate-500">Descubra o preço ideal considerando custos, despesas e a margem que você quer ganhar.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Entradas -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Custo de Material (R$)</label>
                            <input type="number" step="0.01" min="0" x-model.number="material" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold text-slate-700 focus:outline-none focus:border-amber-400" placeholder="Tecido, aviamentos...">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Horas de Trabalho</label>
                                <input type="number" step="0.5" min="0" x-model.number="horas" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold text-slate-700 focus:outline-none focus:border-amber-400" placeholder="Ex: 10">
                            </div>
                            <div>
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Valor da Hora (R$)</label>
                                <input type="number" step="0.01" min="0" x-model.number="valorHora" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold text-slate-700 focus:outline-none focus:border-amber-400" placeholder="Ex: 30">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Rateio de Despesas Fixas (R$)</label>
                            <input type="number" step="0.01" min="0" x-model.number="despesasFixas" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold text-slate-700 focus:outline-none focus:border-amber-400" placeholder="Luz, água, internet, aluguel rateados">
                            <p class="text-[11px] text-slate-400 mt-1">Quanto das contas do mês (luz, água, internet, aluguel...) esta peça deve "pagar".</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Margem de Lucro (%)</label>
                                <input type="number" step="1" min="0" x-model.number="margem" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold text-slate-700 focus:outline-none focus:border-amber-400" placeholder="Ex: 40">
                            </div>
                            <div>
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Taxa de Cartão (%)</label>
                                <input type="number" step="0.1" min="0" x-model.number="taxaCartao" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold text-slate-700 focus:outline-none focus:border-amber-400" placeholder="Ex: 3.5">
                            </div>
                        </div>
                    </div>

                    <!-- Resultado -->
                    <div class="bg-slate-900 rounded-[2rem] p-8 flex flex-col justify-center">
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between text-slate-400"><span>Material</span><span class="font-bold text-slate-200">R$ <span x-text="fmt(material)"></span></span></div>
                            <div class="flex justify-between text-slate-400"><span>Mão de obra (<span x-text="horas||0"></span>h)</span><span class="font-bold text-slate-200">R$ <span x-text="fmt(maoDeObra)"></span></span></div>
                            <div class="flex justify-between text-slate-400"><span>Despesas fixas</span><span class="font-bold text-slate-200">R$ <span x-text="fmt(despesasFixas)"></span></span></div>
                            <div class="flex justify-between text-slate-300 border-t border-slate-700 pt-3"><span class="font-bold">Custo total</span><span class="font-black">R$ <span x-text="fmt(custoTotal)"></span></span></div>
                            <div class="flex justify-between text-slate-400"><span>+ Margem (<span x-text="margem||0"></span>%)</span><span class="font-bold text-slate-200">R$ <span x-text="fmt(valorMargem)"></span></span></div>
                            <div class="flex justify-between text-slate-400"><span>+ Taxa de cartão</span><span class="font-bold text-slate-200">R$ <span x-text="fmt(valorTaxa)"></span></span></div>
                        </div>
                        <div class="mt-6 pt-6 border-t border-slate-700 text-center">
                            <p class="text-[10px] font-black text-[#fbbf24] uppercase tracking-widest">Preço Sugerido</p>
                            <p class="text-4xl font-black text-white mt-1">R$ <span x-text="fmt(precoFinal)"></span></p>
                            <p class="text-xs text-emerald-400 font-bold mt-2">Lucro estimado: R$ <span x-text="fmt(valorMargem)"></span></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TABELA REAL DE LANÇAMENTOS -->
            <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-100">
                <h3 class="text-xl font-black text-slate-800 tracking-tighter mb-6">Todos os Lançamentos</h3>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100">
                                <th class="py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Vencimento</th>
                                <th class="py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Descrição</th>
                                <th class="py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Categoria</th>
                                <th class="py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Status</th>
                                <th class="py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Valor</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @forelse($lancamentos as $lancamento)
                                <tr class="border-b border-slate-50 hover:bg-slate-50 transition">
                                    <td class="py-4 font-bold text-slate-600">{{ date('d/m/Y', strtotime($lancamento->data_vencimento)) }}</td>
                                    <td class="py-4">
                                        <p class="font-black text-slate-800">{{ $lancamento->descricao }}</p>
                                        @if($lancamento->cliente)
                                            <p class="text-xs text-slate-400">Cliente: {{ $lancamento->cliente->nome }}</p>
                                        @endif
                                    </td>
                                    <td class="py-4"><span class="bg-slate-100 text-slate-600 px-3 py-1 rounded-lg font-bold text-[10px] uppercase">{{ $lancamento->categoria->nome ?? 'Sem Categoria' }}</span></td>
                                    <td class="py-4 text-center">
                                        @if($lancamento->status == 'Pago')
                                            <span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-lg font-black text-[10px] uppercase">Pago</span>
                                        @elseif($lancamento->status == 'Pendente')
                                            <span class="bg-amber-100 text-amber-700 px-3 py-1 rounded-lg font-black text-[10px] uppercase">Pendente</span>
                                        @else
                                            <span class="bg-rose-100 text-rose-700 px-3 py-1 rounded-lg font-black text-[10px] uppercase">{{ $lancamento->status }}</span>
                                        @endif
                                    </td>
                                    <td class="py-4 font-black text-right {{ $lancamento->tipo == 'Receita' ? 'text-emerald-600' : 'text-rose-500' }}">
                                        {{ $lancamento->tipo == 'Receita' ? '+' : '-' }} R$ {{ number_format($lancamento->valor_final, 2, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-slate-400">
                                        <i class="fas fa-box-open text-4xl mb-3 opacity-50"></i>
                                        <p class="font-bold">O caixa está zerado.</p>
                                        <p class="text-xs">Registre sua primeira receita ou despesa nos botões acima!</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL DE NOVO LANÇAMENTO (JANELA FLUTUANTE) -->
    <div id="modalLancamento" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-[2.5rem] w-full max-w-lg p-8 shadow-2xl border border-slate-100 relative">
            <button onclick="fecharModal()" class="absolute top-6 right-6 text-slate-400 hover:text-slate-600 font-bold text-lg">
                <i class="fas fa-times"></i>
            </button>

            <h3 id="modalTitulo" class="text-xl font-black text-slate-800 tracking-tighter mb-6">Novo Lançamento</h3>

            <form action="{{ route('financeiro.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="tipo" id="inputTipo">

                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Descrição do Lançamento</label>
                    <input type="text" name="descricao" required placeholder="Ex: Conta de Luz / Aluguel Vestido" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold text-slate-700 focus:outline-none focus:border-amber-400">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Categoria</label>
                        <select name="categoria_financeira_id" id="selectCategoria" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold text-slate-700 focus:outline-none focus:border-amber-400">
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Valor (R$)</label>
                        <input type="number" step="0.01" name="valor_original" required placeholder="0.00" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold text-slate-700 focus:outline-none focus:border-amber-400">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Data de Vencimento</label>
                        <input type="date" name="data_vencimento" value="{{ date('Y-m-d') }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold text-slate-700 focus:outline-none focus:border-amber-400">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Status</label>
                        <select name="status" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold text-slate-700 focus:outline-none focus:border-amber-400">
                            <option value="Pendente">Pendente</option>
                            <option value="Pago">Pago</option>
                            <option value="Vencido">Vencido</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Cliente Vinculado (Opcional)</label>
                    <select name="cliente_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold text-slate-700 focus:outline-none focus:border-amber-400">
                        <option value="">Nenhum / Sem Cliente</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}">{{ $cliente->nome }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Forma de Pagamento</label>
                    <select name="forma_pagamento" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold text-slate-700 focus:outline-none focus:border-amber-400">
                        <option value="Dinheiro">Dinheiro</option>
                        <option value="Pix">Pix</option>
                        <option value="Cartão de Crédito">Cartão de Crédito</option>
                        <option value="Cartão de Débito">Cartão de Débito</option>
                        <option value="Transferência">Transferência</option>
                    </select>
                </div>

                <div class="pt-4 flex gap-4">
                    <button type="button" onclick="fecharModal()" class="w-1/2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-black py-3 rounded-xl transition uppercase tracking-widest text-xs">Cancelar</button>
                    <button type="submit" class="w-1/2 bg-slate-900 hover:bg-slate-800 text-white font-black py-3 rounded-xl transition uppercase tracking-widest text-xs shadow-lg">Salvar Lançamento</button>
                </div>
            </form>
        </div>
    </div>

    <!-- SCRIPT DE CONTROLE DO MODAL E CATEGORIAS -->
    <script>
        const catsReceita = @json($categoriasReceita);
        const catsDespesa = @json($categoriasDespesa);

        function abrirModal(tipo) {
            document.getElementById('modalLancamento').classList.remove('hidden');
            document.getElementById('inputTipo').value = tipo;
            
            const titulo = document.getElementById('modalTitulo');
            const selectCat = document.getElementById('selectCategoria');
            selectCat.innerHTML = '';

            let lista = tipo === 'Receita' ? catsReceita : catsDespesa;

            if(tipo === 'Receita') {
                titulo.innerText = 'Nova Receita';
                titulo.className = 'text-xl font-black text-emerald-600 tracking-tighter mb-6';
            } else {
                titulo.innerText = 'Nova Despesa';
                titulo.className = 'text-xl font-black text-rose-500 tracking-tighter mb-6';
            }

            lista.forEach(cat => {
                let opt = document.createElement('option');
                opt.value = cat.id;
                opt.innerText = cat.nome;
                selectCat.appendChild(opt);
            });
        }

        function fecharModal() {
            document.getElementById('modalLancamento').classList.add('hidden');
        }

        // Calculadora de precificação (Alpine.js)
        function calculadoraPreco() {
            return {
                material: 0, horas: 0, valorHora: 0, despesasFixas: 0, margem: 40, taxaCartao: 0,
                get maoDeObra() { return (this.horas || 0) * (this.valorHora || 0); },
                get custoTotal() { return (this.material || 0) + this.maoDeObra + (this.despesasFixas || 0); },
                get valorMargem() { return this.custoTotal * ((this.margem || 0) / 100); },
                // Preço antes da taxa (custo + margem), depois embute a taxa de cartão.
                get precoAntesTaxa() { return this.custoTotal + this.valorMargem; },
                get precoFinal() {
                    const t = (this.taxaCartao || 0) / 100;
                    return t > 0 && t < 1 ? this.precoAntesTaxa / (1 - t) : this.precoAntesTaxa;
                },
                get valorTaxa() { return this.precoFinal - this.precoAntesTaxa; },
                fmt(v) { return (Number(v) || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); },
            };
        }
    </script>
</x-app-layout>