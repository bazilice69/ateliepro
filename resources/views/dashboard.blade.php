<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-slate-800 leading-tight uppercase tracking-tighter">
                Painel <span class="text-[#fbbf24]">Financeiro</span>
            </h2>
            <div class="flex gap-4">
                <button class="bg-rose-500 hover:bg-rose-400 text-white px-6 py-2 rounded-xl font-black text-xs uppercase tracking-widest transition shadow-lg flex items-center">
                    <i class="fas fa-arrow-down mr-2"></i> Nova Despesa
                </button>
                <button class="bg-emerald-500 hover:bg-emerald-400 text-white px-6 py-2 rounded-xl font-black text-xs uppercase tracking-widest transition shadow-lg flex items-center">
                    <i class="fas fa-arrow-up mr-2"></i> Nova Receita
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-8 relative">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- LINHA 1: CARTÕES DE RESUMO (Conforme seu checklist!) -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                
                <!-- Card 1: Recebido no Mês -->
                <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex flex-col relative overflow-hidden group">
                    <div class="absolute -right-4 -bottom-4 text-emerald-50 opacity-50 group-hover:scale-110 transition-transform duration-500">
                        <i class="fas fa-wallet text-8xl"></i>
                    </div>
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 relative z-10">Recebido no Mês</h3>
                    <p class="text-3xl font-black text-slate-800 relative z-10">R$ 12.850<span class="text-lg text-slate-400">,00</span></p>
                    <div class="mt-4 flex items-center text-xs font-bold text-emerald-500 relative z-10">
                        <i class="fas fa-arrow-up mr-1"></i> +15% vs mês anterior
                    </div>
                </div>

                <!-- Card 2: A Receber -->
                <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex flex-col relative overflow-hidden group">
                    <div class="absolute -right-4 -bottom-4 text-amber-50 opacity-50 group-hover:scale-110 transition-transform duration-500">
                        <i class="fas fa-clock text-8xl"></i>
                    </div>
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 relative z-10">A Receber (Setembro)</h3>
                    <p class="text-3xl font-black text-slate-800 relative z-10">R$ 8.430<span class="text-lg text-slate-400">,00</span></p>
                    <div class="mt-4 flex items-center text-xs font-bold text-amber-500 relative z-10">
                        <i class="fas fa-hourglass-half mr-1"></i> 12 cobranças pendentes
                    </div>
                </div>

                <!-- Card 3: Em Atraso -->
                <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-rose-100 flex flex-col relative overflow-hidden group">
                    <div class="absolute -right-4 -bottom-4 text-rose-50 opacity-50 group-hover:scale-110 transition-transform duration-500">
                        <i class="fas fa-exclamation-circle text-8xl"></i>
                    </div>
                    <h3 class="text-[10px] font-black text-rose-400 uppercase tracking-widest mb-2 relative z-10">Em Atraso</h3>
                    <p class="text-3xl font-black text-rose-600 relative z-10">R$ 1.250<span class="text-lg text-rose-400">,00</span></p>
                    <div class="mt-4 flex items-center text-xs font-bold text-rose-500 relative z-10">
                        <i class="fas fa-exclamation-triangle mr-1"></i> Ação de cobrança necessária
                    </div>
                </div>

                <!-- Card 4: Faturamento Previsto -->
                <div class="bg-slate-900 p-6 rounded-[2rem] shadow-xl border border-slate-800 flex flex-col relative overflow-hidden group">
                    <div class="absolute -right-4 -bottom-4 text-slate-800 opacity-50 group-hover:scale-110 transition-transform duration-500">
                        <i class="fas fa-chart-line text-8xl"></i>
                    </div>
                    <h3 class="text-[10px] font-black text-[#fbbf24] uppercase tracking-widest mb-2 relative z-10">Faturamento Previsto</h3>
                    <p class="text-3xl font-black text-white relative z-10">R$ 21.280<span class="text-lg text-slate-400">,00</span></p>
                    <div class="mt-4 flex items-center text-xs font-bold text-slate-400 relative z-10">
                        Projeção até 30/09
                    </div>
                </div>

            </div>

            <!-- LINHA 2: TABELA DE LANÇAMENTOS RECENTES -->
            <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-100">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-xl font-black text-slate-800 tracking-tighter">Movimentações Recentes</h3>
                        <p class="text-sm font-medium text-slate-500">Últimas entradas e saídas registradas no caixa.</p>
                    </div>
                    <button class="text-slate-400 hover:text-slate-600 font-bold text-xs uppercase tracking-widest transition">
                        Ver Relatório Completo &rarr;
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100">
                                <th class="py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Data</th>
                                <th class="py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Descrição</th>
                                <th class="py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Categoria</th>
                                <th class="py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Status</th>
                                <th class="py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Valor</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            <!-- Exemplo de Receita (Locação) -->
                            <tr class="border-b border-slate-50 hover:bg-slate-50 transition">
                                <td class="py-4 font-bold text-slate-600">07/09/2026</td>
                                <td class="py-4">
                                    <p class="font-black text-slate-800">Locação Vestido Sereia</p>
                                    <p class="text-xs text-slate-400">Cliente: Ana Silva</p>
                                </td>
                                <td class="py-4"><span class="bg-slate-100 text-slate-600 px-3 py-1 rounded-lg font-bold text-[10px] uppercase">Locação de Trajes</span></td>
                                <td class="py-4 text-center"><span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-lg font-black text-[10px] uppercase"><i class="fas fa-check-circle mr-1"></i> Pago</span></td>
                                <td class="py-4 font-black text-emerald-600 text-right">+ R$ 1.500,00</td>
                            </tr>

                            <!-- Exemplo de Despesa (Material) -->
                            <tr class="border-b border-slate-50 hover:bg-slate-50 transition">
                                <td class="py-4 font-bold text-slate-600">06/09/2026</td>
                                <td class="py-4">
                                    <p class="font-black text-slate-800">3m Renda Guipir + Zíperes</p>
                                    <p class="text-xs text-slate-400">Loja 25 de Março</p>
                                </td>
                                <td class="py-4"><span class="bg-slate-100 text-slate-600 px-3 py-1 rounded-lg font-bold text-[10px] uppercase">Materiais / Aviamentos</span></td>
                                <td class="py-4 text-center"><span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-lg font-black text-[10px] uppercase"><i class="fas fa-check-circle mr-1"></i> Pago</span></td>
                                <td class="py-4 font-black text-rose-500 text-right">- R$ 320,00</td>
                            </tr>

                            <!-- Exemplo de Pendente -->
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-4 font-bold text-slate-600">10/09/2026</td>
                                <td class="py-4">
                                    <p class="font-black text-slate-800">Parcela 1/3 - Casamento</p>
                                    <p class="text-xs text-slate-400">Cliente: Maria Souza</p>
                                </td>
                                <td class="py-4"><span class="bg-slate-100 text-slate-600 px-3 py-1 rounded-lg font-bold text-[10px] uppercase">Locação de Trajes</span></td>
                                <td class="py-4 text-center"><span class="bg-amber-100 text-amber-700 px-3 py-1 rounded-lg font-black text-[10px] uppercase"><i class="fas fa-clock mr-1"></i> Pendente</span></td>
                                <td class="py-4 font-black text-slate-800 text-right">R$ 1.000,00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>