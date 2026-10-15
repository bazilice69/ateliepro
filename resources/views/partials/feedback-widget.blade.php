{{--
    Botão flutuante de FEEDBACK / SUPORTE.
    - Problema / Sugestão / Dúvida -> enviados pelo WhatsApp do suporte.
    - Elogio ❤️ -> salvo no banco como DEPOIMENTO (fica pendente de aprovação
      do super_admin e, uma vez aprovado, aparece na landing como prova social,
      com o link da loja do lojista junto para divulgá-lo).
    Visível só para lojas (o super admin não vê).
--}}
@php
    $waSuporteFb = preg_replace('/\D/', '', \App\Models\Setting::get('saas_whatsapp_suporte', '5511957866836'));
    $lojaFb = Auth::user()?->loja?->nome_fantasia ?? '';
    $nomeFb = Auth::user()?->name ?? '';
@endphp

<div x-data="{ aberto: false, tipo: 'Problema', msg: '', autor: @js($nomeFb), lojaNome: @js($lojaFb), link: '' }"
     class="fixed bottom-6 left-6 z-40">
    {{-- Botão flutuante --}}
    <button @click="aberto = true"
            class="w-14 h-14 rounded-full bg-slate-900 text-[#fbbf24] shadow-xl hover:scale-105 transition flex items-center justify-center border-2 border-[#fbbf24]/40"
            title="Reportar problema, sugestão ou elogio">
        <i class="fas fa-comment-dots text-xl"></i>
    </button>

    {{-- Modal --}}
    <div x-show="aberto" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" @click.self="aberto = false">
        <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-md p-7 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-1">
                <h3 class="font-black text-slate-800 text-lg tracking-tighter"><i class="fas fa-comment-dots text-[#fbbf24] mr-2"></i>Fale com a gente</h3>
                <button type="button" @click="aberto = false" class="text-slate-400 hover:text-slate-700 text-xl">&times;</button>
            </div>
            <p class="text-xs text-slate-400 mb-5" x-text="tipo === 'Elogio'
                ? 'Gostou do sistema? Deixe seu elogio — ele pode aparecer na nossa página inicial, com o link da sua loja junto. 💛'
                : 'Encontrou um problema ou tem uma sugestão? Conta pra gente — vai direto pro nosso WhatsApp.'"></p>

            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Tipo</label>
            <div class="grid grid-cols-4 gap-2 mb-4">
                <template x-for="t in ['Problema','Sugestão','Dúvida','Elogio']" :key="t">
                    <button type="button" @click="tipo = t"
                            :class="tipo === t ? (t === 'Elogio' ? 'bg-rose-500 text-white' : 'bg-slate-900 text-[#fbbf24]') : 'bg-slate-100 text-slate-500 hover:bg-slate-200'"
                            class="py-2 rounded-xl font-black text-[11px] uppercase tracking-wide transition" x-text="t"></button>
                </template>
            </div>

            {{-- ===================== FLUXO ELOGIO (salva no banco) ===================== --}}
            <form x-show="tipo === 'Elogio'" method="POST" action="{{ route('depoimentos.store') }}" class="space-y-3">
                @csrf

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Seu nome</label>
                    <input type="text" name="autor_nome" x-model="autor" required maxlength="120"
                           class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none text-sm" placeholder="Ex: Andreia">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Nome da sua loja</label>
                    <input type="text" name="loja_nome" x-model="lojaNome" maxlength="120"
                           class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none text-sm" placeholder="Como quer que apareça">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Seu elogio</label>
                    <textarea name="mensagem" x-model="msg" rows="4" required maxlength="1000"
                              class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none text-sm" placeholder="Conte como o sistema tem ajudado você..."></textarea>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                        Link da sua loja <span class="text-slate-300 normal-case font-medium">(opcional — divulgamos você junto!)</span>
                    </label>
                    <input type="text" name="link_loja" x-model="link" maxlength="255"
                           class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none text-sm" placeholder="site, @instagram ou wa.me/...">
                </div>

                <button type="submit"
                        :disabled="!msg.trim() || !autor.trim()"
                        :class="(msg.trim() && autor.trim()) ? '' : 'opacity-50 pointer-events-none'"
                        class="mt-2 w-full inline-flex items-center justify-center gap-2 bg-rose-500 hover:bg-rose-400 text-white px-6 py-3.5 rounded-2xl font-black text-sm uppercase tracking-widest transition shadow-lg">
                    <i class="fas fa-heart"></i> Enviar elogio
                </button>
                <p class="text-[11px] text-slate-400 text-center">Passa por uma conferência rápida antes de ir ao ar.</p>
            </form>

            {{-- ============== FLUXO PROBLEMA/SUGESTÃO/DÚVIDA (WhatsApp) ============== --}}
            <div x-show="tipo !== 'Elogio'">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Mensagem</label>
                <textarea x-model="msg" rows="4" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none text-sm" placeholder="Descreva o que aconteceu ou sua ideia..."></textarea>

                <a :href="'https://wa.me/{{ $waSuporteFb }}?text=' + encodeURIComponent(
                        '*' + tipo + ' — {{ $lojaFb }}*\n' +
                        'De: {{ $nomeFb }}\n' +
                        'Página: ' + window.location.pathname + '\n\n' +
                        msg
                   )"
                   target="_blank"
                   @click="aberto = false"
                   :class="msg.trim() ? '' : 'pointer-events-none opacity-50'"
                   class="mt-5 w-full inline-flex items-center justify-center gap-2 bg-emerald-500 hover:bg-emerald-400 text-white px-6 py-3.5 rounded-2xl font-black text-sm uppercase tracking-widest transition shadow-lg">
                    <i class="fab fa-whatsapp text-lg"></i> Enviar pelo WhatsApp
                </a>
            </div>
        </div>
    </div>
</div>
