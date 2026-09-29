<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-slate-800 leading-tight uppercase tracking-tighter">
                Painel <span class="text-[#fbbf24]">Financeiro</span>
            </h2>
            <div class="flex gap-3">
                <a href="{{ route('financeiro.index') }}" class="bg-emerald-500 hover:bg-emerald-400 text-white px-6 py-2 rounded-xl font-black text-xs uppercase tracking-widest transition shadow-lg flex items-center">
                    <i class="fas fa-plus mr-2"></i> Novo Lançamento
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 relative">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- CARTÕES DE RESUMO (dados reais da loja) -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex flex-col relative overflow-hidden group">
                    <div class="absolute -right-4 -bottom-4 text-emerald-50 opacity-50 group-hover:scale-110 transition-transform duration-500"><i class="fas fa-wallet text-8xl"></i></div>
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 relative z-10">Recebido no Mês</h3>
                    <p class="text-3xl font-black text-slate-800 relative z-10">R$ {{ number_format($recebidoMes, 2, ',', '.') }}</p>
                    <div class="mt-4 text-xs font-bold text-emerald-500 relative z-10"><i class="fas fa-check-circle mr-1"></i> Receitas pagas</div>
                </div>

                <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex flex-col relative overflow-hidden group">
                    <div class="absolute -right-4 -bottom-4 text-amber-50 opacity-50 group-hover:scale-110 transition-transform duration-500"><i class="fas fa-clock text-8xl"></i></div>
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 relative z-10">A Receber</h3>
                    <p class="text-3xl font-black text-slate-800 relative z-10">R$ {{ number_format($aReceber, 2, ',', '.') }}</p>
                    <div class="mt-4 text-xs font-bold text-amber-500 relative z-10"><i class="fas fa-hourglass-half mr-1"></i> Cobranças pendentes</div>
                </div>

                <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-rose-100 flex flex-col relative overflow-hidden group">
                    <div class="absolute -right-4 -bottom-4 text-rose-50 opacity-50 group-hover:scale-110 transition-transform duration-500"><i class="fas fa-exclamation-circle text-8xl"></i></div>
                    <h3 class="text-[10px] font-black text-rose-400 uppercase tracking-widest mb-2 relative z-10">Em Atraso</h3>
                    <p class="text-3xl font-black text-rose-600 relative z-10">R$ {{ number_format($emAtraso, 2, ',', '.') }}</p>
                    <div class="mt-4 text-xs font-bold text-rose-500 relative z-10"><i class="fas fa-exclamation-triangle mr-1"></i> Ação de cobrança</div>
                </div>

                <div class="bg-slate-900 p-6 rounded-[2rem] shadow-xl border border-slate-800 flex flex-col relative overflow-hidden group">
                    <div class="absolute -right-4 -bottom-4 text-slate-800 opacity-50 group-hover:scale-110 transition-transform duration-500"><i class="fas fa-chart-line text-8xl"></i></div>
                    <h3 class="text-[10px] font-black text-[#fbbf24] uppercase tracking-widest mb-2 relative z-10">Projeção do Mês</h3>
                    <p class="text-3xl font-black text-white relative z-10">R$ {{ number_format($saldoPrevisto, 2, ',', '.') }}</p>
                    <div class="mt-4 text-xs font-bold text-slate-400 relative z-10">Recebido + a receber − despesas</div>
                </div>
            </div>

            <!-- MINI-INDICADORES OPERACIONAIS -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <a href="{{ route('clientes.index') }}" class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center"><i class="fas fa-users text-amber-500"></i></div>
                    <div><p class="text-2xl font-black text-slate-800">{{ $totais['clientes'] }}</p><p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Clientes</p></div>
                </a>
                <a href="{{ route('acervo.index') }}" class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center"><i class="fas fa-tshirt text-amber-500"></i></div>
                    <div><p class="text-2xl font-black text-slate-800">{{ $totais['pecas'] }}</p><p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Peças no Acervo</p></div>
                </a>
                <a href="{{ route('locacao.create') }}" class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center"><i class="fas fa-file-contract text-amber-500"></i></div>
                    <div><p class="text-2xl font-black text-slate-800">{{ $totais['locacoes_ativas'] }}</p><p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Locações Ativas</p></div>
                </a>
                <a href="{{ route('agenda.index') }}" class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center"><i class="fas fa-calendar-day text-amber-500"></i></div>
                    <div><p class="text-2xl font-black text-slate-800">{{ $agendaHoje->count() }}</p><p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Compromissos Hoje</p></div>
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- MOVIMENTAÇÕES RECENTES (reais) -->
                <div class="lg:col-span-2 bg-white rounded-[2rem] p-8 shadow-sm border border-slate-100">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-xl font-black text-slate-800 tracking-tighter">Movimentações Recentes</h3>
                            <p class="text-sm font-medium text-slate-500">Últimas entradas e saídas no caixa.</p>
                        </div>
                        <a href="{{ route('financeiro.index') }}" class="text-slate-400 hover:text-slate-600 font-bold text-xs uppercase tracking-widest transition">Ver tudo &rarr;</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100">
                                    <th class="py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Data</th>
                                    <th class="py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Descrição</th>
                                    <th class="py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Status</th>
                                    <th class="py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Valor</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                @forelse($movimentacoes as $mov)
                                    <tr class="border-b border-slate-50 hover:bg-slate-50 transition">
                                        <td class="py-3 font-bold text-slate-600">{{ optional($mov->data_vencimento)->format('d/m/Y') }}</td>
                                        <td class="py-3">
                                            <p class="font-black text-slate-800">{{ $mov->descricao }}</p>
                                            <p class="text-xs text-slate-400">{{ $mov->categoria->nome ?? '—' }}{{ $mov->cliente ? ' · '.$mov->cliente->nome : '' }}</p>
                                        </td>
                                        <td class="py-3 text-center">
                                            @php
                                                $st = $mov->status;
                                                $cor = $st === 'Pago' ? 'bg-emerald-100 text-emerald-700' : ($st === 'Vencido' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700');
                                            @endphp
                                            <span class="{{ $cor }} px-3 py-1 rounded-lg font-black text-[10px] uppercase">{{ $st }}</span>
                                        </td>
                                        <td class="py-3 font-black text-right {{ $mov->tipo === 'Receita' ? 'text-emerald-600' : 'text-rose-500' }}">
                                            {{ $mov->tipo === 'Receita' ? '+' : '−' }} R$ {{ number_format((float) $mov->valor_final, 2, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="py-12 text-center text-slate-400">
                                        <i class="fas fa-receipt text-4xl text-slate-200 mb-3 block"></i>
                                        Nenhuma movimentação ainda. Registre seu primeiro lançamento no Financeiro.
                                    </td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- PRÓXIMOS EVENTOS (contagem regressiva) -->
                <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-slate-100">
                    <h3 class="text-xl font-black text-slate-800 tracking-tighter mb-6">Próximos Eventos</h3>
                    <div class="space-y-4">
                        @forelse($proximosEventos as $evento)
                            @php $dias = (int) now()->startOfDay()->diffInDays($evento->data_evento, false); @endphp
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-2xl bg-slate-900 text-[#fbbf24] flex flex-col items-center justify-center leading-none">
                                    <span class="text-sm font-black">{{ $dias }}</span>
                                    <span class="text-[8px] uppercase">dias</span>
                                </div>
                                <div>
                                    <p class="font-black text-slate-800 text-sm">{{ $evento->nome_evento }}</p>
                                    <p class="text-xs text-slate-400">{{ $evento->tipo_evento }} · {{ optional($evento->data_evento)->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-slate-400 text-center py-6">
                                <i class="fas fa-calendar-heart text-4xl text-slate-200 mb-3 block"></i>
                                Nenhum evento futuro cadastrado.
                            </p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
