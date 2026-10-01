{{--
    Selos de confiança do checkout (reutilizável).
    Passa segurança: ambiente protegido, SSL, bandeiras aceitas e gateways.
    Uso: @include('checkout._selos-confianca')
--}}
<div class="mt-8 space-y-5">

    {{-- Faixa de segurança --}}
    <div class="flex items-center justify-center gap-2 text-emerald-600">
        <i class="fas fa-lock"></i>
        <span class="text-xs font-black uppercase tracking-widest">Pagamento 100% seguro</span>
    </div>

    {{-- Bandeiras / meios aceitos --}}
    <div>
        <p class="text-center text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Formas de pagamento aceitas</p>
        <div class="flex flex-wrap items-center justify-center gap-3 text-3xl text-slate-400">
            @foreach(\App\Support\PaymentGateways::BANDEIRAS as $bandeira)
                <i class="{{ $bandeira['icone'] }}" title="{{ $bandeira['nome'] }}"></i>
            @endforeach
        </div>
    </div>

    {{-- Gateways / processadores (marcas que passam confiança) --}}
    <div>
        <p class="text-center text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Processado com segurança por</p>
        <div class="flex flex-wrap items-center justify-center gap-2">
            @foreach(\App\Support\PaymentGateways::paraExibir() as $gw)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border text-xs font-bold
                    {{ $gw['ativo'] ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-slate-200 bg-slate-50 text-slate-500' }}">
                    <i class="{{ $gw['icone'] }} {{ $gw['ativo'] ? '' : $gw['cor'] }}"></i>
                    {{ $gw['nome'] }}
                    @if($gw['ativo'])
                        <i class="fas fa-circle-check text-emerald-500 text-[10px]"></i>
                    @endif
                </span>
            @endforeach
        </div>
    </div>

    {{-- Garantias / confiança --}}
    <div class="flex flex-wrap items-center justify-center gap-x-6 gap-y-2 pt-2 text-[11px] font-medium text-slate-500">
        <span class="inline-flex items-center gap-1.5"><i class="fas fa-shield-halved text-emerald-500"></i> Conexão criptografada (SSL)</span>
        <span class="inline-flex items-center gap-1.5"><i class="fas fa-user-shield text-emerald-500"></i> Seus dados protegidos</span>
        <span class="inline-flex items-center gap-1.5"><i class="fas fa-headset text-emerald-500"></i> Suporte humano</span>
    </div>
</div>
