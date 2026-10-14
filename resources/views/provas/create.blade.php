<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-slate-800 uppercase tracking-tighter">Nova <span class="text-[#fbbf24]">Prova</span></h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            @if($errors->any())
                <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-700 px-6 py-4 rounded-2xl text-sm">{{ $errors->first() }}</div>
            @endif
            <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100" x-data="buscaCliente()">
                <form method="POST" action="{{ route('provas.store') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="locacao_id" value="{{ $locacaoId }}">

                    {{-- Busca de cliente (digita o nome e filtra) --}}
                    <div class="relative">
                        <label class="text-[10px] font-black uppercase text-slate-400">Cliente</label>
                        <input type="hidden" name="cliente_id" x-model="selecionadoId" required>
                        <input type="text" x-model="termo" @focus="aberto=true" @input="aberto=true"
                               placeholder="🔎 Digite o nome do cliente..."
                               autocomplete="off"
                               class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700 focus:ring-2 focus:ring-[#fbbf24]">

                        {{-- Dropdown de resultados --}}
                        <div x-show="aberto && termo.length > 0" x-cloak @click.outside="aberto=false"
                             class="absolute z-20 left-0 right-0 mt-1 bg-white border border-slate-200 rounded-xl shadow-xl max-h-60 overflow-y-auto">
                            <template x-for="c in filtrados()" :key="c.id">
                                <button type="button" @click="escolher(c)"
                                        class="w-full text-left px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-violet-50 border-b border-slate-50">
                                    <span x-text="c.nome"></span>
                                </button>
                            </template>
                            {{-- Nada encontrado -> cadastrar --}}
                            <div x-show="filtrados().length === 0" class="px-4 py-4 text-center">
                                <p class="text-sm text-slate-400 mb-2">Nenhum cliente encontrado.</p>
                                <a :href="'{{ route('clientes.create') }}?nome=' + encodeURIComponent(termo)"
                                   class="inline-block bg-slate-900 text-[#fbbf24] px-4 py-2 rounded-lg text-xs font-black uppercase tracking-widest">
                                    + Cadastrar "<span x-text="termo"></span>"
                                </a>
                            </div>
                        </div>

                        {{-- Cliente selecionado (confirmação visual) --}}
                        <p x-show="selecionadoNome" x-cloak class="mt-2 text-xs text-emerald-600 font-bold">
                            ✓ Selecionado: <span x-text="selecionadoNome"></span>
                            <button type="button" @click="limpar()" class="text-slate-400 hover:text-rose-500 ml-1">(trocar)</button>
                        </p>
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400">Peça (opcional)</label>
                        <select name="acervo_id" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                            <option value="">— nenhuma —</option>
                            @foreach($acervos as $a)
                                <option value="{{ $a->id }}">{{ $a->codigo }} · {{ $a->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-black uppercase text-slate-400">Data e hora</label>
                            <input type="datetime-local" name="data_prova" required class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                        </div>
                        <div>
                            <label class="text-[10px] font-black uppercase text-slate-400">Responsável</label>
                            <input type="text" name="responsavel" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                        </div>
                    </div>
                    <textarea name="observacoes" rows="2" placeholder="Observações..." class="w-full p-3 bg-slate-50 border-none rounded-xl text-sm"></textarea>
                    <div class="flex gap-3">
                        <a href="{{ route('provas.index') }}" class="px-6 py-3 text-slate-400 font-bold text-xs uppercase">Cancelar</a>
                        <button class="flex-1 bg-slate-900 text-[#fbbf24] py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition">Agendar Prova</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function buscaCliente() {
            return {
                clientes: @json($clientes->map(fn($c) => ['id' => $c->id, 'nome' => $c->nome])->values()),
                termo: '',
                aberto: false,
                selecionadoId: '{{ $clienteId ?? '' }}',
                selecionadoNome: '{{ optional($clientes->firstWhere('id', $clienteId))->nome ?? '' }}',
                init() {
                    if (this.selecionadoNome) this.termo = this.selecionadoNome;
                },
                filtrados() {
                    const t = this.termo.trim().toLowerCase();
                    if (!t) return this.clientes.slice(0, 8);
                    return this.clientes.filter(c => c.nome.toLowerCase().includes(t)).slice(0, 8);
                },
                escolher(c) {
                    this.selecionadoId = c.id;
                    this.selecionadoNome = c.nome;
                    this.termo = c.nome;
                    this.aberto = false;
                },
                limpar() {
                    this.selecionadoId = '';
                    this.selecionadoNome = '';
                    this.termo = '';
                    this.aberto = true;
                },
            }
        }
    </script>
</x-app-layout>
