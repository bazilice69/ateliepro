<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-slate-800 uppercase tracking-tighter">
                Despesas <span class="text-[#fbbf24]">Fixas</span>
            </h2>
            <form method="POST" action="{{ route('recorrentes.gerar') }}">
                @csrf
                <button class="bg-slate-900 text-[#fbbf24] px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition">
                    <i class="fas fa-bolt mr-1"></i> Gerar lançamentos do mês
                </button>
            </form>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl font-medium text-sm">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-700 px-6 py-4 rounded-2xl font-medium text-sm">{{ $errors->first() }}</div>
            @endif

            <p class="text-sm text-slate-500">Cadastre aqui contas que se repetem todo mês (internet, luz, água, aluguel, mão de obra...). O sistema gera o lançamento no financeiro automaticamente todo mês.</p>

            <!-- Form de nova despesa fixa -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                <h3 class="font-black text-slate-800 uppercase text-sm tracking-wider mb-4">Nova despesa fixa</h3>
                <form method="POST" action="{{ route('recorrentes.store') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    @csrf
                    <div class="md:col-span-2">
                        <label class="text-[10px] font-black uppercase text-slate-400">Descrição</label>
                        <input type="text" name="descricao" required placeholder="Ex: Internet, Aluguel..." class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400">Valor (R$)</label>
                        <input type="number" step="0.01" name="valor" required class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400">Dia venc.</label>
                        <input type="number" name="dia_vencimento" min="1" max="28" value="5" required class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                    </div>
                    <div class="md:col-span-3">
                        <label class="text-[10px] font-black uppercase text-slate-400">Categoria (opcional)</label>
                        <select name="categoria_id" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                            <option value="">— sem categoria —</option>
                            @foreach($categorias as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <button class="w-full bg-emerald-500 hover:bg-emerald-600 text-white py-3 rounded-xl font-black text-xs uppercase tracking-widest transition">Adicionar</button>
                    </div>
                </form>
            </div>

            <!-- Lista -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                            <th class="py-4 pl-6">Descrição</th>
                            <th class="py-4">Categoria</th>
                            <th class="py-4 text-center">Dia</th>
                            <th class="py-4 text-right">Valor</th>
                            <th class="py-4 text-center">Ativa</th>
                            <th class="py-4 pr-6"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($despesas as $d)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-4 pl-6 font-bold text-slate-800 text-sm">{{ $d->descricao }}</td>
                                <td class="py-4 text-sm text-slate-600">{{ $d->categoria->nome ?? '—' }}</td>
                                <td class="py-4 text-center text-sm text-slate-600">{{ $d->dia_vencimento }}</td>
                                <td class="py-4 text-right font-bold text-slate-800 text-sm">R$ {{ number_format((float)$d->valor, 2, ',', '.') }}</td>
                                <td class="py-4 text-center">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $d->ativo ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-500' }}">
                                        {{ $d->ativo ? 'Sim' : 'Não' }}
                                    </span>
                                </td>
                                <td class="py-4 pr-6 text-right">
                                    <form method="POST" action="{{ route('recorrentes.destroy', $d) }}" class="inline" onsubmit="return confirm('Remover esta despesa fixa?')">
                                        @csrf @method('DELETE')
                                        <button class="text-xs font-bold text-rose-500 hover:text-rose-700 uppercase">Remover</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-12 text-center text-slate-400">Nenhuma despesa fixa cadastrada.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
