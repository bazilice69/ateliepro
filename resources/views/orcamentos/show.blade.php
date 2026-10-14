<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-slate-800 uppercase tracking-tighter">Orçamento <span class="text-[#fbbf24]">#{{ $orcamento->id }}</span></h2>
            <a href="{{ route('orcamentos.index') }}" class="text-xs font-bold text-slate-400 hover:text-slate-700 uppercase tracking-widest self-center">← Orçamentos</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl text-sm">{{ session('success') }}</div>
            @endif

            {{-- Valor + ações --}}
            <div class="bg-slate-900 rounded-3xl p-8 text-center">
                <p class="text-[10px] uppercase tracking-widest text-slate-400">Valor do orçamento</p>
                <p class="text-5xl font-black text-[#fbbf24] mt-1">R$ {{ number_format((float)$orcamento->valor_final, 2, ',', '.') }}</p>
                <p class="text-sm text-slate-400 mt-2">{{ $orcamento->cliente?->nome ?? 'Sem cliente' }} · {{ $orcamento->modoLabel() }}</p>
                <div class="flex flex-wrap justify-center gap-3 mt-6">
                    @php $wa = preg_replace('/\D/', '', \App\Models\Setting::get('saas_whatsapp_suporte', '5511957866836')); @endphp
                    <a href="{{ route('orcamentos.imprimir', $orcamento) }}" target="_blank" class="bg-white text-slate-900 px-5 py-3 rounded-xl font-black text-xs uppercase tracking-widest">🖨️ Imprimir / PDF</a>
                    @if($orcamento->cliente)
                        <a href="https://wa.me/?text={{ urlencode('Olá '.$orcamento->cliente->nome.'! Segue seu orçamento: R$ '.number_format((float)$orcamento->valor_final,2,',','.').($orcamento->descricao ? ' — '.$orcamento->descricao : '')) }}" target="_blank" class="bg-[#25D366] text-black px-5 py-3 rounded-xl font-black text-xs uppercase tracking-widest">📤 Enviar no WhatsApp</a>
                    @endif
                </div>
            </div>

            {{-- Resumo interno (só a lojista vê) --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                <h3 class="text-xs font-black uppercase text-slate-400 tracking-widest mb-4">🔒 Resumo interno (não aparece para o cliente)</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                    @if($orcamento->modo !== 'aluguel')
                        <div><p class="text-[10px] uppercase text-slate-400">Material</p><p class="font-bold">R$ {{ number_format((float)$orcamento->custo_material,2,',','.') }}</p></div>
                        <div><p class="text-[10px] uppercase text-slate-400">Mão de obra</p><p class="font-bold">{{ (float)$orcamento->horas_trabalho }}h × R$ {{ number_format((float)$orcamento->valor_hora,2,',','.') }}</p></div>
                    @else
                        <div><p class="text-[10px] uppercase text-slate-400">Custo da peça</p><p class="font-bold">R$ {{ number_format((float)$orcamento->custo_peca,2,',','.') }}</p></div>
                        <div><p class="text-[10px] uppercase text-slate-400">Nº aluguéis</p><p class="font-bold">{{ $orcamento->num_alugueis ?? '—' }}</p></div>
                    @endif
                    <div><p class="text-[10px] uppercase text-slate-400">Custo total</p><p class="font-bold">R$ {{ number_format((float)$orcamento->custo_total,2,',','.') }}</p></div>
                    <div><p class="text-[10px] uppercase text-slate-400">Margem</p><p class="font-bold">{{ (float)$orcamento->margem_percent }}%</p></div>
                    <div><p class="text-[10px] uppercase text-slate-400">Lucro estimado</p><p class="font-bold text-emerald-600">R$ {{ number_format((float)$orcamento->lucro_estimado,2,',','.') }}</p></div>
                </div>
            </div>

            {{-- Status / aceite (o "virar contrato" vem no Bloco 2) --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 text-center">
                <p class="text-sm text-slate-500">Status atual: <strong class="uppercase">{{ $orcamento->status }}</strong></p>
                @if($orcamento->contrato_id)
                    <a href="{{ route('contratos.show', $orcamento->contrato_id) }}" class="inline-block mt-3 text-sm font-bold text-[#fbbf24]">Ver contrato gerado →</a>
                @else
                    <p class="text-xs text-slate-400 mt-2">Quando o cliente aceitar, você poderá gerar o contrato a partir deste orçamento.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
