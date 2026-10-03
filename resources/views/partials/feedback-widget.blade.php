{{--
    Botão flutuante de FEEDBACK / SUPORTE.
    Abre um modal onde o lojista escolhe o tipo (problema/sugestão/dúvida),
    escreve a mensagem e envia pelo WhatsApp do suporte — já formatada com o
    nome da loja. Evita áudios longos e organiza os reports.
    Visível só para lojas (o super admin não vê).
--}}
@php
    $waSuporteFb = preg_replace('/\D/', '', \App\Models\Setting::get('saas_whatsapp_suporte', '5511957866836'));
    $lojaFb = Auth::user()?->loja?->nome_fantasia ?? '';
    $nomeFb = Auth::user()?->name ?? '';
@endphp

<div x-data="{ aberto: false, tipo: 'Problema', msg: '' }" class="fixed bottom-6 left-6 z-40">
    {{-- Botão flutuante --}}
    <button @click="aberto = true"
            class="w-14 h-14 rounded-full bg-slate-900 text-[#fbbf24] shadow-xl hover:scale-105 transition flex items-center justify-center border-2 border-[#fbbf24]/40"
            title="Reportar problema ou sugestão">
        <i class="fas fa-comment-dots text-xl"></i>
    </button>

    {{-- Modal --}}
    <div x-show="aberto" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" @click.self="aberto = false">
        <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-md p-7">
            <div class="flex items-center justify-between mb-1">
                <h3 class="font-black text-slate-800 text-lg tracking-tighter"><i class="fas fa-comment-dots text-[#fbbf24] mr-2"></i>Fale com o suporte</h3>
                <button type="button" @click="aberto = false" class="text-slate-400 hover:text-slate-700 text-xl">&times;</button>
            </div>
            <p class="text-xs text-slate-400 mb-5">Encontrou um problema ou tem uma sugestão? Conta pra gente — vai direto pro nosso WhatsApp.</p>

            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Tipo</label>
            <div class="grid grid-cols-3 gap-2 mb-4">
                <template x-for="t in ['Problema','Sugestão','Dúvida']" :key="t">
                    <button type="button" @click="tipo = t"
                            :class="tipo === t ? 'bg-slate-900 text-[#fbbf24]' : 'bg-slate-100 text-slate-500 hover:bg-slate-200'"
                            class="py-2 rounded-xl font-black text-[11px] uppercase tracking-wide transition" x-text="t"></button>
                </template>
            </div>

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
