<x-app-layout>
    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-6">
                <h2 class="font-black text-3xl text-slate-800 leading-tight uppercase tracking-tighter">
                    Fale <span class="text-[#fbbf24]">Conosco</span>
                </h2>
                <p class="text-slate-500 font-medium mt-1">Problema, dúvida, sugestão ou um elogio — a gente quer ouvir você.</p>
            </div>

            @if(session('success'))
                <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl font-medium text-sm flex items-start gap-2">
                    <i class="fas fa-check-circle mt-0.5"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if($errors->any())
                <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-700 px-6 py-4 rounded-2xl font-medium text-sm">{{ $errors->first() }}</div>
            @endif

            @php
                $waSuporte = $waSuporte ?? preg_replace('/\D/', '', \App\Models\Setting::get('saas_whatsapp_suporte', '5511957866836'));
                $nomeAtual = Auth::user()?->name ?? '';
                $lojaAtualNome = Auth::user()?->loja?->nome_fantasia ?? '';
            @endphp

            {{-- Blindagem: se o Alpine não carregar, o x-show não roda e os
                 blocos ficariam escondidos. Este CSS garante que, SEM Alpine,
                 tudo apareça (o atributo x-data some do DOM só quando o Alpine
                 inicializa — enquanto não inicializa, forçamos visibilidade). --}}
            <noscript>
                <style>[x-show]{display:block !important}</style>
            </noscript>

            <div x-data="faleConosco()" class="bg-white rounded-[2rem] p-6 sm:p-8 shadow-sm border border-slate-100">

                <!-- Abas de tipo -->
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Sobre o que você quer falar?</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mb-6">
                    <template x-for="t in tipos" :key="t">
                        <button type="button" @click="tipo = t"
                                :class="btnClasse(t)"
                                class="py-2.5 rounded-xl font-black text-[11px] uppercase tracking-wide transition" x-text="t"></button>
                    </template>
                </div>

                {{-- ============ ELOGIO: salva no banco (vira depoimento) ============ --}}
                <form x-show="tipo === 'Elogio'" method="POST" action="{{ route('depoimentos.store') }}" class="space-y-4">
                    @csrf
                    <div class="bg-rose-50 border border-rose-100 rounded-2xl p-4 text-sm text-rose-600 font-medium flex items-start gap-2">
                        <i class="fas fa-heart mt-0.5"></i>
                        <span>Seu elogio pode aparecer na nossa página inicial como depoimento — e o link da sua loja vai junto, divulgando você! 💛</span>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Seu nome</label>
                        <input type="text" name="autor_nome" value="{{ old('autor_nome', $nomeAtual) }}" required maxlength="120"
                               class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none text-sm" placeholder="Ex: Andreia">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Nome da sua loja</label>
                        <input type="text" name="loja_nome" value="{{ old('loja_nome', $lojaAtualNome) }}" maxlength="120"
                               class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none text-sm" placeholder="Como quer que apareça">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Seu elogio</label>
                        <textarea name="mensagem" rows="4" required maxlength="1000"
                                  class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none text-sm" placeholder="Conte como o sistema tem ajudado você...">{{ old('mensagem') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                            Link da sua loja <span class="text-slate-300 normal-case font-medium">(opcional — divulgamos você junto!)</span>
                        </label>
                        <input type="text" name="link_loja" value="{{ old('link_loja') }}" maxlength="255"
                               class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none text-sm" placeholder="site, @instagram ou wa.me/...">
                    </div>

                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 bg-rose-500 hover:bg-rose-400 text-white px-6 py-3.5 rounded-2xl font-black text-sm uppercase tracking-widest transition shadow-lg">
                        <i class="fas fa-heart"></i> Enviar elogio
                    </button>
                    <p class="text-[11px] text-slate-400 text-center">Passa por uma conferência rápida antes de ir ao ar.</p>
                </form>

                {{-- ============ PROBLEMA / SUGESTÃO / DÚVIDA: WhatsApp ============ --}}
                <div x-show="tipo !== 'Elogio'" class="space-y-4">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Mensagem</label>
                        <textarea x-model="msg" rows="5"
                                  class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none text-sm" placeholder="Descreva o que aconteceu ou sua ideia..."></textarea>
                    </div>
                    <a :href="linkWhatsapp()" target="_blank"
                       :class="msg.trim() ? '' : 'pointer-events-none opacity-50'"
                       class="w-full inline-flex items-center justify-center gap-2 bg-emerald-500 hover:bg-emerald-400 text-white px-6 py-3.5 rounded-2xl font-black text-sm uppercase tracking-widest transition shadow-lg">
                        <i class="fab fa-whatsapp text-lg"></i> Enviar pelo WhatsApp
                    </a>
                    <p class="text-[11px] text-slate-400 text-center">Abre o WhatsApp do suporte com sua mensagem já preenchida.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function faleConosco() {
            return {
                tipo: 'Problema',
                tipos: ['Problema', 'Sugestão', 'Dúvida', 'Elogio'],
                msg: '',
                waSuporte: @json($waSuporte),
                loja: @json($lojaAtualNome),
                autor: @json($nomeAtual),

                btnClasse(t) {
                    if (this.tipo === t) {
                        return t === 'Elogio' ? 'bg-rose-500 text-white' : 'bg-slate-900 text-[#fbbf24]';
                    }
                    return 'bg-slate-100 text-slate-500 hover:bg-slate-200';
                },

                linkWhatsapp() {
                    const texto =
                        '*' + this.tipo + ' — ' + this.loja + '*\n' +
                        'De: ' + this.autor + '\n\n' +
                        this.msg;
                    return 'https://wa.me/' + this.waSuporte + '?text=' + encodeURIComponent(texto);
                },
            };
        }
    </script>
</x-app-layout>
