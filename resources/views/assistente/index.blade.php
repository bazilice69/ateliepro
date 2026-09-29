<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-slate-800 uppercase tracking-tighter">
                Assistente <span class="text-[#fbbf24]">IA</span>
            </h2>
            @unless($iaAtiva)
                <span class="text-[10px] font-black uppercase tracking-widest bg-amber-100 text-amber-700 px-3 py-1 rounded-full">Modo Demonstração</span>
            @else
                <span class="text-[10px] font-black uppercase tracking-widest bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full">IA Ativa</span>
            @endunless
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- CHAT -->
            <div class="lg:col-span-2 bg-white rounded-3xl shadow-sm border border-slate-100 flex flex-col" style="height: 32rem;">
                <div class="p-5 border-b border-slate-100 flex justify-between items-center">
                    <div>
                        <h3 class="font-black text-slate-800">Converse com o AteliêPro</h3>
                        <p class="text-xs text-slate-400">Pergunte sobre agenda, financeiro, peças, clientes...</p>
                    </div>
                    @if(count($historico) > 0)
                        <form method="POST" action="{{ route('assistente.limpar') }}">
                            @csrf
                            <button class="text-xs font-bold text-slate-400 hover:text-rose-500 uppercase">Limpar</button>
                        </form>
                    @endif
                </div>

                <!-- Mensagens -->
                <div class="flex-1 overflow-y-auto p-5 space-y-4" id="chatBox">
                    @forelse($historico as $msg)
                        @if($msg['role'] === 'user')
                            <div class="flex justify-end">
                                <div class="bg-slate-900 text-white rounded-2xl rounded-br-sm px-4 py-2 max-w-[80%] text-sm">{{ $msg['content'] }}</div>
                            </div>
                        @else
                            <div class="flex justify-start">
                                <div class="bg-slate-100 text-slate-700 rounded-2xl rounded-bl-sm px-4 py-2 max-w-[80%] text-sm whitespace-pre-line">{{ $msg['content'] }}</div>
                            </div>
                        @endif
                    @empty
                        <div class="text-center text-slate-400 mt-16">
                            <i class="fas fa-robot text-5xl text-slate-200 mb-4"></i>
                            <p class="font-bold text-slate-500">Olá! Como posso ajudar hoje?</p>
                            <p class="text-xs mt-1">Ex: "Quantas provas tenho hoje?" · "Quanto recebi esse mês?"</p>
                        </div>
                    @endforelse
                </div>

                <!-- Input -->
                <form method="POST" action="{{ route('assistente.enviar') }}" class="p-4 border-t border-slate-100 flex gap-2">
                    @csrf
                    <input type="text" name="mensagem" required autofocus autocomplete="off" placeholder="Digite sua pergunta..." class="flex-1 bg-slate-50 border-none rounded-xl px-4 py-3 text-sm font-medium text-slate-700 focus:ring-2 focus:ring-[#fbbf24]">
                    <button class="bg-slate-900 text-[#fbbf24] px-5 py-3 rounded-xl font-black text-sm hover:bg-slate-800 transition"><i class="fas fa-paper-plane"></i></button>
                </form>
            </div>

            <!-- GERADOR DE WHATSAPP -->
            <div x-data="geradorWhatsapp()" class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 h-fit">
                <div class="flex items-center gap-2 mb-4">
                    <i class="fab fa-whatsapp text-emerald-500 text-2xl"></i>
                    <h3 class="font-black text-slate-800">Mensagem de WhatsApp</h3>
                </div>
                <p class="text-xs text-slate-400 mb-4">Gere uma mensagem pronta e personalizada para enviar ao cliente.</p>

                <label class="text-[10px] font-black uppercase text-slate-400">Tipo</label>
                <select x-model="tipo" class="w-full mt-1 mb-3 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700 text-sm">
                    <option value="confirmacao">Confirmação de atendimento</option>
                    <option value="prova">Lembrete de prova</option>
                    <option value="retirada">Aviso de retirada</option>
                    <option value="devolucao">Lembrete de devolução</option>
                    <option value="pagamento">Cobrança amigável</option>
                    <option value="pos_venda">Pós-venda</option>
                    <option value="aniversario">Aniversário</option>
                </select>

                <label class="text-[10px] font-black uppercase text-slate-400">Cliente (opcional)</label>
                <select x-model="clienteId" class="w-full mt-1 mb-4 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700 text-sm">
                    <option value="">— genérico —</option>
                    @foreach($clientes as $c)
                        <option value="{{ $c->id }}">{{ $c->nome }}</option>
                    @endforeach
                </select>

                <button @click="gerar()" :disabled="carregando" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white py-3 rounded-xl font-black text-xs uppercase tracking-widest transition">
                    <span x-show="!carregando"><i class="fas fa-magic mr-1"></i> Gerar mensagem</span>
                    <span x-show="carregando"><i class="fas fa-spinner fa-spin mr-1"></i> Gerando...</span>
                </button>

                <div x-show="mensagem" x-cloak class="mt-4">
                    <textarea x-model="mensagem" rows="6" class="w-full p-3 bg-slate-50 border-none rounded-xl text-sm text-slate-700"></textarea>
                    <button @click="copiar()" class="mt-2 w-full bg-slate-100 hover:bg-slate-200 text-slate-700 py-2 rounded-xl font-bold text-xs uppercase">
                        <i class="fas fa-copy mr-1"></i> <span x-text="copiado ? 'Copiado!' : 'Copiar'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>[x-cloak]{display:none!important}</style>
    <script>
        // Rola o chat para o fim ao carregar.
        window.addEventListener('load', () => {
            const box = document.getElementById('chatBox');
            if (box) box.scrollTop = box.scrollHeight;
        });

        function geradorWhatsapp() {
            return {
                tipo: 'confirmacao', clienteId: '', mensagem: '', carregando: false, copiado: false,
                async gerar() {
                    this.carregando = true; this.copiado = false;
                    try {
                        const r = await fetch("{{ route('assistente.whatsapp') }}", {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ tipo: this.tipo, cliente_id: this.clienteId || null }),
                        });
                        const data = await r.json();
                        this.mensagem = data.mensagem || 'Não foi possível gerar.';
                    } catch (e) { this.mensagem = 'Erro ao gerar a mensagem.'; }
                    this.carregando = false;
                },
                copiar() {
                    navigator.clipboard.writeText(this.mensagem);
                    this.copiado = true;
                    setTimeout(() => this.copiado = false, 2000);
                },
            };
        }
    </script>
</x-app-layout>
