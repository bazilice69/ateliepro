@php $nomeIA = \App\Support\IA::nomeAssistente(); @endphp

<div x-data="valentinaWidget()" x-cloak class="fixed bottom-6 right-6 z-[60]">

    <!-- Janela do chat -->
    <div x-show="aberto"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="absolute bottom-20 right-0 w-80 sm:w-96 bg-white rounded-3xl shadow-2xl border border-slate-100 flex flex-col overflow-hidden"
         style="height: 30rem;">

        <!-- Cabeçalho -->
        <div class="bg-gradient-to-r from-slate-900 to-slate-800 p-4 flex items-center gap-3">
            <div class="w-11 h-11 rounded-full bg-[#fbbf24] flex items-center justify-center overflow-hidden shrink-0">
                @include('partials.valentina-avatar')
            </div>
            <div class="flex-1">
                <p class="text-white font-black text-sm leading-tight">{{ $nomeIA }}</p>
                <p class="text-[10px] text-emerald-300 font-bold flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Sua estilista virtual
                </p>
            </div>
            <button @click="aberto = false" class="text-slate-400 hover:text-white text-lg px-1">&times;</button>
        </div>

        <!-- Mensagens -->
        <div class="flex-1 overflow-y-auto p-4 space-y-3 bg-slate-50" x-ref="mensagens">
            <template x-for="(m, i) in mensagens" :key="i">
                <div :class="m.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                    <div :class="m.role === 'user'
                            ? 'bg-slate-900 text-white rounded-2xl rounded-br-sm'
                            : 'bg-white border border-slate-100 text-slate-700 rounded-2xl rounded-bl-sm shadow-sm'"
                         class="px-4 py-2 max-w-[85%] text-sm whitespace-pre-line" x-text="m.content"></div>
                </div>
            </template>
            <div x-show="digitando" class="flex justify-start">
                <div class="bg-white border border-slate-100 rounded-2xl rounded-bl-sm px-4 py-3 shadow-sm">
                    <span class="flex gap-1">
                        <span class="w-1.5 h-1.5 bg-slate-300 rounded-full animate-bounce" style="animation-delay:0s"></span>
                        <span class="w-1.5 h-1.5 bg-slate-300 rounded-full animate-bounce" style="animation-delay:.15s"></span>
                        <span class="w-1.5 h-1.5 bg-slate-300 rounded-full animate-bounce" style="animation-delay:.3s"></span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Sugestões rápidas -->
        <div class="px-3 pt-2 flex flex-wrap gap-1.5 bg-slate-50" x-show="mensagens.length <= 1">
            <template x-for="s in sugestoes" :key="s">
                <button @click="enviar(s)" class="text-[11px] bg-white border border-slate-200 text-slate-600 px-2.5 py-1 rounded-full hover:bg-slate-100" x-text="s"></button>
            </template>
        </div>

        <!-- Input -->
        <form @submit.prevent="enviar()" class="p-3 border-t border-slate-100 flex gap-2 bg-white">
            <input type="text" x-model="texto" placeholder="Escreva para {{ $nomeIA }}..." autocomplete="off"
                   class="flex-1 bg-slate-50 border-none rounded-xl px-3 py-2.5 text-sm text-slate-700 focus:ring-2 focus:ring-[#fbbf24]">
            <button type="submit" :disabled="digitando" class="bg-slate-900 text-[#fbbf24] px-4 rounded-xl font-black text-sm hover:bg-slate-800 transition">
                <i class="fas fa-paper-plane"></i>
            </button>
        </form>
    </div>

    <!-- Botão flutuante -->
    <button @click="toggle()" class="w-16 h-16 rounded-full bg-[#fbbf24] shadow-2xl flex items-center justify-center hover:scale-105 transition relative ring-4 ring-white">
        <div class="w-14 h-14 rounded-full overflow-hidden flex items-center justify-center">
            @include('partials.valentina-avatar')
        </div>
        <span x-show="!aberto" class="absolute -top-1 -right-1 w-4 h-4 bg-emerald-400 rounded-full border-2 border-white"></span>
    </button>

    <script>
        function valentinaWidget() {
            return {
                aberto: false,
                texto: '',
                digitando: false,
                sugestoes: ['Quantas provas tenho hoje?', 'Quanto recebi esse mês?', 'Peças disponíveis', 'Próximo evento'],
                mensagens: [
                    { role: 'assistant', content: 'Oi! Eu sou a {{ $nomeIA }}, sua estilista virtual do AteliêPro. 💛 Como posso te ajudar hoje?' }
                ],
                toggle() {
                    this.aberto = !this.aberto;
                    if (this.aberto) this.$nextTick(() => this.scrollFim());
                },
                scrollFim() {
                    const el = this.$refs.mensagens;
                    if (el) el.scrollTop = el.scrollHeight;
                },
                async enviar(preset) {
                    const msg = (preset || this.texto).trim();
                    if (!msg || this.digitando) return;
                    this.mensagens.push({ role: 'user', content: msg });
                    this.texto = '';
                    this.digitando = true;
                    this.$nextTick(() => this.scrollFim());
                    try {
                        const r = await fetch("{{ route('assistente.conversar') }}", {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ mensagem: msg }),
                        });
                        const data = await r.json();
                        this.mensagens.push({ role: 'assistant', content: data.resposta || 'Desculpe, não consegui responder agora.' });
                    } catch (e) {
                        this.mensagens.push({ role: 'assistant', content: 'Ops, tive um probleminha de conexão. Tenta de novo? 💛' });
                    }
                    this.digitando = false;
                    this.$nextTick(() => this.scrollFim());
                },
            };
        }
    </script>
</div>
