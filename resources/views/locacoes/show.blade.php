<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-slate-800 uppercase tracking-tighter">
                Locação <span class="text-[#fbbf24]">#{{ $locacao->id }}</span>
            </h2>
            <a href="{{ route('locacao.index') }}" class="text-xs font-bold text-slate-400 hover:text-slate-700 uppercase tracking-widest self-center">← Locações</a>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ aba: 'resumo' }">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl font-medium text-sm">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-700 px-6 py-4 rounded-2xl text-sm">{{ $errors->first() }}</div>
            @endif

            <!-- Cabeçalho da locação -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <p class="text-[10px] font-black uppercase text-slate-400">Cliente</p>
                    <p class="font-bold text-slate-800">{{ $locacao->cliente->nome ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase text-slate-400">Peça</p>
                    <p class="font-bold text-slate-800">{{ $locacao->acervo->nome ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase text-slate-400">Evento</p>
                    <p class="font-bold text-slate-800">{{ optional($locacao->data_evento)->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase text-slate-400">Status</p>
                    <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 uppercase">{{ str_replace('_', ' ', $locacao->status) }}</span>
                </div>
            </div>

            <!-- Abas -->
            <div class="flex flex-wrap gap-2 bg-slate-100 p-1 rounded-xl w-fit">
                @foreach(['resumo' => 'Resumo', 'provas' => 'Provas', 'ajustes' => 'Ajustes', 'retirada' => 'Retirada', 'devolucao' => 'Devolução'] as $k => $rot)
                    <button @click="aba='{{ $k }}'" :class="aba==='{{ $k }}' ? 'bg-white shadow text-slate-800' : 'text-slate-500'" class="px-4 py-2 rounded-lg text-xs font-black uppercase tracking-widest transition">{{ $rot }}</button>
                @endforeach
            </div>

            <!-- RESUMO / Financeiro -->
            <div x-show="aba==='resumo'" class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 space-y-4">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                    <div class="bg-slate-50 rounded-2xl p-4"><p class="text-[10px] font-black uppercase text-slate-400">Valor total</p><p class="font-black text-slate-800">R$ {{ number_format((float)$locacao->valor_total, 2, ',', '.') }}</p></div>
                    <div class="bg-slate-50 rounded-2xl p-4"><p class="text-[10px] font-black uppercase text-slate-400">Sinal pago</p><p class="font-black text-emerald-600">R$ {{ number_format((float)$locacao->sinal_pago, 2, ',', '.') }}</p></div>
                    <div class="bg-slate-50 rounded-2xl p-4"><p class="text-[10px] font-black uppercase text-slate-400">Caução</p><p class="font-black text-slate-800">R$ {{ number_format((float)$locacao->caucao, 2, ',', '.') }}</p></div>
                    <div class="bg-slate-900 rounded-2xl p-4"><p class="text-[10px] font-black uppercase text-[#fbbf24]">Saldo</p><p class="font-black text-white">R$ {{ number_format($locacao->saldo(), 2, ',', '.') }}</p></div>
                </div>
                <div class="text-sm text-slate-600">
                    <p><strong>Retirada:</strong> {{ optional($locacao->data_retirada)->format('d/m/Y') }} · <strong>Devolução:</strong> {{ optional($locacao->data_devolucao)->format('d/m/Y') }}</p>
                    @if($locacao->observacoes)<p class="mt-2 text-slate-500">{{ $locacao->observacoes }}</p>@endif
                </div>
            </div>

            <!-- PROVAS -->
            <div x-show="aba==='provas'" x-cloak class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-black text-slate-800 uppercase text-sm tracking-wider">Provas</h3>
                    <a href="{{ route('provas.create', ['locacao_id' => $locacao->id, 'cliente_id' => $locacao->cliente_id]) }}" class="text-xs font-black text-slate-900 bg-[#fbbf24] px-4 py-2 rounded-lg uppercase">+ Nova prova</a>
                </div>
                <div class="space-y-3">
                    @forelse($locacao->provas as $prova)
                        <div class="border border-slate-100 rounded-2xl p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-bold text-slate-800 text-sm">{{ optional($prova->data_prova)->format('d/m/Y H:i') }}</p>
                                    <p class="text-xs text-slate-400">{{ $prova->responsavel ?: 'Sem responsável' }} · <span class="uppercase">{{ $prova->status }}</span></p>
                                    @if($prova->resultado)<p class="text-xs mt-1 {{ $prova->resultado === 'aprovada' ? 'text-emerald-600' : 'text-amber-600' }} font-bold uppercase">{{ str_replace('_',' ',$prova->resultado) }}</p>@endif
                                    @if($prova->ajustes_necessarios)<p class="text-sm text-slate-600 mt-1">Ajustes: {{ $prova->ajustes_necessarios }}</p>@endif
                                </div>
                            </div>
                            @if($prova->status === 'agendada')
                                <form method="POST" action="{{ route('provas.registrar', $prova) }}" class="mt-3 border-t border-slate-100 pt-3 space-y-2">
                                    @csrf @method('PUT')
                                    <div class="flex flex-wrap gap-2 items-center">
                                        <select name="resultado" required class="p-2 bg-slate-50 border-none rounded-lg text-sm font-semibold">
                                            <option value="aprovada">Aprovada</option>
                                            <option value="precisa_ajuste">Precisa de ajuste</option>
                                        </select>
                                        <input type="date" name="proxima_prova" class="p-2 bg-slate-50 border-none rounded-lg text-sm" placeholder="Próxima prova">
                                        <button class="bg-slate-900 text-[#fbbf24] px-4 py-2 rounded-lg text-xs font-black uppercase">Registrar</button>
                                    </div>
                                    <input type="text" name="ajustes_necessarios" placeholder="O que precisa ajustar? (barra, cintura...)" class="w-full p-2 bg-slate-50 border-none rounded-lg text-sm">
                                </form>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-slate-400 text-center py-6">Nenhuma prova registrada.</p>
                    @endforelse
                </div>
            </div>

            <!-- AJUSTES (oficina) -->
            <div x-show="aba==='ajustes'" x-cloak class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-black text-slate-800 uppercase text-sm tracking-wider">Ajustes / Oficina</h3>
                    <a href="{{ route('servicos.create', ['locacao_id' => $locacao->id, 'acervo_id' => $locacao->acervo_id, 'cliente_id' => $locacao->cliente_id]) }}" class="text-xs font-black text-slate-900 bg-[#fbbf24] px-4 py-2 rounded-lg uppercase">+ Novo serviço</a>
                </div>
                <div class="space-y-3">
                    @forelse($locacao->servicos as $s)
                        <div class="border border-slate-100 rounded-2xl p-4 flex justify-between items-center">
                            <div>
                                <p class="font-bold text-slate-800 text-sm">{{ $s->descricao }} <span class="text-[10px] text-slate-400 uppercase">({{ $s->tipo }})</span></p>
                                <p class="text-xs text-slate-400">{{ $s->responsavel ?: 'Sem responsável' }} · prazo {{ optional($s->prazo)->format('d/m/Y') ?? '—' }}</p>
                                @if($s->itens)<p class="text-xs text-slate-500 mt-1">{{ implode(', ', $s->itens) }}</p>@endif
                            </div>
                            <form method="POST" action="{{ route('servicos.status', $s) }}">
                                @csrf @method('PUT')
                                <select name="status" onchange="this.form.submit()" class="p-2 bg-slate-50 border-none rounded-lg text-xs font-bold uppercase">
                                    @foreach(['solicitado','em_andamento','pronto','aprovado','cancelado'] as $st)
                                        <option value="{{ $st }}" @selected($s->status === $st)>{{ str_replace('_',' ',$st) }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400 text-center py-6">Nenhum ajuste aberto.</p>
                    @endforelse
                </div>
            </div>

            <!-- RETIRADA -->
            <div x-show="aba==='retirada'" x-cloak class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                <h3 class="font-black text-slate-800 uppercase text-sm tracking-wider mb-4">Retirada</h3>
                @if($locacao->retirada_em)
                    <p class="text-emerald-600 font-bold text-sm"><i class="fas fa-check-circle mr-1"></i> Retirada em {{ $locacao->retirada_em->format('d/m/Y H:i') }}</p>
                    @if($locacao->retirada_checklist)<p class="text-sm text-slate-600 mt-2">{{ $locacao->retirada_checklist }}</p>@endif
                @else
                    <form method="POST" action="{{ route('locacao.retirar', $locacao) }}" enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        <label class="text-[10px] font-black uppercase text-slate-400">Checklist de itens entregues</label>
                        <textarea name="retirada_checklist" rows="3" placeholder="Ex: Vestido, véu, sapato, acessórios..." class="w-full p-3 bg-slate-50 border-none rounded-xl text-sm"></textarea>
                        <label class="text-[10px] font-black uppercase text-slate-400">Fotos (opcional)</label>
                        <input type="file" name="fotos[]" multiple accept="image/*" class="block w-full text-sm text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-slate-900 file:text-[#fbbf24] file:text-xs file:font-bold">
                        <button class="bg-emerald-500 hover:bg-emerald-600 text-white py-3 px-6 rounded-xl font-black text-xs uppercase tracking-widest">Confirmar Retirada</button>
                    </form>
                @endif
            </div>

            <!-- DEVOLUÇÃO / VISTORIA -->
            <div x-show="aba==='devolucao'" x-cloak class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                <h3 class="font-black text-slate-800 uppercase text-sm tracking-wider mb-4">Devolução & Vistoria</h3>
                @if($locacao->devolucao_em)
                    <p class="text-emerald-600 font-bold text-sm"><i class="fas fa-check-circle mr-1"></i> Devolvida em {{ $locacao->devolucao_em->format('d/m/Y H:i') }}</p>
                    <p class="text-sm text-slate-600 mt-2">Estado: <strong>{{ $locacao->vistoria_estado }}</strong> · Multa: R$ {{ number_format((float)$locacao->multa, 2, ',', '.') }} · Caução: {{ $locacao->caucao_status }}</p>
                    @if($locacao->vistoria_observacoes)<p class="text-sm text-slate-500 mt-1">{{ $locacao->vistoria_observacoes }}</p>@endif
                @else
                    <form method="POST" action="{{ route('locacao.devolver', $locacao) }}" enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="text-[10px] font-black uppercase text-slate-400">Estado da peça</label>
                                <select name="vistoria_estado" required class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl text-sm font-semibold">
                                    <option value="ok">OK (sem problemas)</option>
                                    <option value="mancha">Mancha (lavanderia)</option>
                                    <option value="dano">Dano (manutenção)</option>
                                    <option value="rasgo">Rasgo (manutenção)</option>
                                    <option value="falta_item">Falta item</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-[10px] font-black uppercase text-slate-400">Caução</label>
                                <select name="caucao_status" required class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl text-sm font-semibold">
                                    <option value="devolvida">Devolver caução</option>
                                    <option value="retida">Reter caução</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="text-[10px] font-black uppercase text-slate-400">Multa (R$, se houver)</label>
                            <input type="number" step="0.01" name="multa" value="0" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl text-sm">
                        </div>
                        <textarea name="vistoria_observacoes" rows="2" placeholder="Observações da vistoria..." class="w-full p-3 bg-slate-50 border-none rounded-xl text-sm"></textarea>
                        <label class="text-[10px] font-black uppercase text-slate-400">Fotos (opcional)</label>
                        <input type="file" name="fotos[]" multiple accept="image/*" class="block w-full text-sm text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-slate-900 file:text-[#fbbf24] file:text-xs file:font-bold">
                        <button class="bg-slate-900 text-[#fbbf24] py-3 px-6 rounded-xl font-black text-xs uppercase tracking-widest">Registrar Devolução</button>
                    </form>
                @endif
            </div>

        </div>
    </div>
    <style>[x-cloak]{display:none!important}</style>
</x-app-layout>
