<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-slate-800 uppercase tracking-tighter">Novo <span class="text-[#fbbf24]">Orçamento</span></h2>
    </x-slot>

    <div class="py-10" x-data="orcamento()">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @if($errors->any())
                <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-700 px-6 py-4 rounded-2xl text-sm">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('orcamentos.store') }}">
                @csrf
                {{-- campos calculados enviados ocultos --}}
                <input type="hidden" name="valor_final" :value="precoFinal.toFixed(2)">
                <input type="hidden" name="custo_total" :value="custoTotal.toFixed(2)">
                <input type="hidden" name="lucro_estimado" :value="valorMargem.toFixed(2)">
                <input type="hidden" name="modo" x-model="modo">
                <input type="hidden" name="cliente_id" x-model="clienteId">

                <div class="grid lg:grid-cols-5 gap-6">
                    <!-- COLUNA ESQUERDA: dados + custos -->
                    <div class="lg:col-span-3 space-y-6">
                        <!-- Cliente + peça -->
                        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 space-y-4">
                            <h3 class="text-xs font-black uppercase text-slate-400 tracking-widest">1. Cliente e peça</h3>
                            <div class="relative">
                                <label class="text-[10px] font-black uppercase text-slate-400">Cliente</label>
                                <input type="text" x-model="termoCliente" @focus="abertoCli=true" @input="abertoCli=true" placeholder="🔎 Buscar cliente..." autocomplete="off" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700 focus:ring-2 focus:ring-[#fbbf24]">
                                <div x-show="abertoCli && termoCliente.length>0" x-cloak @click.outside="abertoCli=false" class="absolute z-20 left-0 right-0 mt-1 bg-white border border-slate-200 rounded-xl shadow-xl max-h-56 overflow-y-auto">
                                    <template x-for="c in clientesFiltrados()" :key="c.id">
                                        <button type="button" @click="escolherCliente(c)" class="w-full text-left px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-violet-50 border-b border-slate-50" x-text="c.nome"></button>
                                    </template>
                                    <div x-show="clientesFiltrados().length===0" class="px-4 py-3 text-center">
                                        <a :href="'{{ route('clientes.create') }}?nome=' + encodeURIComponent(termoCliente)" class="inline-block bg-slate-900 text-[#fbbf24] px-3 py-2 rounded-lg text-xs font-black uppercase">+ Cadastrar "<span x-text="termoCliente"></span>"</a>
                                    </div>
                                </div>
                                <p x-show="clienteNome" x-cloak class="mt-1 text-xs text-emerald-600 font-bold">✓ <span x-text="clienteNome"></span> <button type="button" @click="limparCliente()" class="text-slate-400">(trocar)</button></p>
                            </div>
                            <div>
                                <label class="text-[10px] font-black uppercase text-slate-400">Peça do acervo (opcional)</label>
                                <select name="acervo_id" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                                    <option value="">— nenhuma —</option>
                                    @foreach($acervos as $a)
                                        <option value="{{ $a->id }}">{{ $a->codigo }} · {{ $a->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="text-[10px] font-black uppercase text-slate-400">Descrição (o que será feito/alugado)</label>
                                <input type="text" name="descricao" placeholder="Ex: Vestido de noiva sob medida, renda francesa" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                            </div>
                        </div>

                        <!-- Modo + custos -->
                        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 space-y-4">
                            <h3 class="text-xs font-black uppercase text-slate-400 tracking-widest">2. Como calcular</h3>
                            <div class="flex rounded-xl bg-slate-100 p-1">
                                <template x-for="(label, m) in modos" :key="m">
                                    <button type="button" @click="modo=m" :class="modo===m ? 'bg-slate-900 text-[#fbbf24]' : 'text-slate-500'" class="flex-1 py-2 rounded-lg text-xs font-black uppercase tracking-widest transition" x-text="label"></button>
                                </template>
                            </div>

                            {{-- Campos conforme o modo --}}
                            <div x-show="modo!=='aluguel'" class="grid grid-cols-2 gap-4">
                                <div><label class="text-[10px] font-black uppercase text-slate-400">Custo material (R$)</label><input type="text" inputmode="decimal" name="custo_material" x-model="material" placeholder="0,00" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl"></div>
                                <div></div>
                                <div><label class="text-[10px] font-black uppercase text-slate-400">Horas de trabalho</label><input type="text" inputmode="decimal" name="horas_trabalho" x-model="horas" placeholder="0" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl"></div>
                                <div><label class="text-[10px] font-black uppercase text-slate-400">Valor da hora (R$)</label><input type="text" inputmode="decimal" name="valor_hora" x-model="valorHora" placeholder="0,00" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl"></div>
                            </div>
                            <div x-show="modo==='sob_medida'">
                                <label class="text-[10px] font-black uppercase text-slate-400">Rateio de despesas fixas (R$)</label>
                                <input type="text" inputmode="decimal" name="despesas_fixas" x-model="despesasFixas" placeholder="0,00" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl">
                                <p class="text-[11px] text-slate-400 mt-1">Quanto das contas do mês (luz, água, aluguel) esta peça deve "pagar".</p>
                            </div>
                            <div x-show="modo==='aluguel'" class="grid grid-cols-2 gap-4">
                                <div><label class="text-[10px] font-black uppercase text-slate-400">Quanto a peça custou (R$)</label><input type="text" inputmode="decimal" name="custo_peca" x-model="custoPeca" placeholder="0,00" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl"></div>
                                <div><label class="text-[10px] font-black uppercase text-slate-400">Nº de aluguéis previstos</label><input type="number" name="num_alugueis" x-model="numAlugueis" placeholder="10" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl"></div>
                                <div><label class="text-[10px] font-black uppercase text-slate-400">Custo por aluguel (R$)</label><input type="text" inputmode="decimal" name="custo_manutencao" x-model="custoManutencao" placeholder="0,00" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl"></div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 pt-2 border-t border-slate-100">
                                <div><label class="text-[10px] font-black uppercase text-slate-400">Margem de lucro (%)</label><input type="text" inputmode="decimal" name="margem_percent" x-model="margem" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl"></div>
                                <div><label class="text-[10px] font-black uppercase text-slate-400">Taxa de cartão (%)</label><input type="text" inputmode="decimal" name="taxa_cartao_percent" x-model="taxaCartao" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl"></div>
                            </div>
                        </div>
                    </div>

                    <!-- COLUNA DIREITA: resultado -->
                    <div class="lg:col-span-2">
                        <div class="bg-slate-900 rounded-3xl p-6 sticky top-6">
                            <h3 class="text-xs font-black uppercase text-[#fbbf24] tracking-widest mb-4">Resumo (interno)</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between text-slate-300"><span x-text="modo==='aluguel' ? 'Custo da peça (diluído)' : 'Material'"></span><span x-text="fmt(modo==='aluguel' ? custoPecaDiluido : material)"></span></div>
                                <div x-show="modo!=='aluguel'" class="flex justify-between text-slate-300"><span>Mão de obra</span><span x-text="fmt(maoDeObra)"></span></div>
                                <div x-show="modo==='sob_medida'" class="flex justify-between text-slate-300"><span>Despesas fixas</span><span x-text="fmt(num(despesasFixas))"></span></div>
                                <div x-show="modo==='aluguel'" class="flex justify-between text-slate-300"><span>Custo por aluguel</span><span x-text="fmt(num(custoManutencao))"></span></div>
                                <div class="flex justify-between text-white font-bold border-t border-slate-700 pt-2"><span>Custo total</span><span x-text="fmt(custoTotal)"></span></div>
                                <div class="flex justify-between text-slate-300"><span>+ Margem (<span x-text="num(margem)"></span>%)</span><span x-text="fmt(valorMargem)"></span></div>
                                <div class="flex justify-between text-slate-300"><span>+ Taxa de cartão</span><span x-text="fmt(valorTaxa)"></span></div>
                            </div>
                            <div class="mt-5 pt-4 border-t border-slate-700 text-center">
                                <p class="text-[10px] uppercase tracking-widest text-slate-400">Preço do orçamento</p>
                                <p class="text-4xl font-black text-[#fbbf24]" x-text="fmt(precoFinal)"></p>
                                <p class="text-xs text-emerald-400 mt-1">Lucro estimado: <span x-text="fmt(valorMargem)"></span></p>
                            </div>
                            <p class="text-[10px] text-slate-500 text-center mt-4">🔒 Os custos acima são só seus. O cliente verá apenas o preço do orçamento.</p>
                            <button type="submit" class="w-full mt-4 bg-[#fbbf24] text-black py-3 rounded-xl font-black text-xs uppercase tracking-widest">Salvar orçamento</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function orcamento() {
            return {
                clientes: @json($clientes->map(fn($c) => ['id'=>$c->id,'nome'=>$c->nome])->values()),
                modos: { sob_medida: 'Sob Medida', aluguel: 'Aluguel', ajuste: 'Ajuste' },
                modo: 'sob_medida',
                // cliente
                termoCliente: '', abertoCli: false, clienteId: '', clienteNome: '',
                // custos
                material: '', horas: '', valorHora: '', despesasFixas: '',
                custoPeca: '', numAlugueis: '10', custoManutencao: '',
                margem: '100', taxaCartao: '0',

                num(v) { v = (''+v).replace(/\./g,'').replace(',','.'); const n = parseFloat(v); return isNaN(n)?0:n; },
                fmt(v) { return 'R$ ' + (v||0).toLocaleString('pt-BR',{minimumFractionDigits:2,maximumFractionDigits:2}); },

                clientesFiltrados() {
                    const t = this.termoCliente.trim().toLowerCase();
                    if (!t) return this.clientes.slice(0,8);
                    return this.clientes.filter(c => c.nome.toLowerCase().includes(t)).slice(0,8);
                },
                escolherCliente(c){ this.clienteId=c.id; this.clienteNome=c.nome; this.termoCliente=c.nome; this.abertoCli=false; },
                limparCliente(){ this.clienteId=''; this.clienteNome=''; this.termoCliente=''; this.abertoCli=true; },

                get maoDeObra(){ return this.num(this.horas) * this.num(this.valorHora); },
                get custoPecaDiluido(){ const n = Math.max(1, this.num(this.numAlugueis)); return this.num(this.custoPeca) / n; },
                get custoTotal(){
                    if (this.modo==='aluguel') return this.custoPecaDiluido + this.num(this.custoManutencao);
                    if (this.modo==='ajuste') return this.num(this.material) + this.maoDeObra;
                    return this.num(this.material) + this.maoDeObra + this.num(this.despesasFixas);
                },
                get valorMargem(){ return this.custoTotal * (this.num(this.margem)/100); },
                get precoAntesTaxa(){ return this.custoTotal + this.valorMargem; },
                get precoFinal(){ const t=this.num(this.taxaCartao); return t>0 ? this.precoAntesTaxa/(1-t/100) : this.precoAntesTaxa; },
                get valorTaxa(){ return this.precoFinal - this.precoAntesTaxa; },
            }
        }
    </script>
</x-app-layout>
