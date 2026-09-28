<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-slate-800 leading-tight uppercase tracking-tighter">
                Gestão de <span class="text-[#fbbf24]">Clientes</span>
            </h2>
            <a href="{{ route('clientes.create') }}" class="bg-slate-900 text-[#fbbf24] px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition shadow-lg">
                + Novo Cliente
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- BARRA DE PESQUISA E FILTROS -->
            <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-slate-100 flex flex-col md:flex-row gap-4 items-center justify-between">
                <div class="flex-1 w-full relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-search text-slate-400"></i>
                    </div>
                    <input type="text" placeholder="Buscar por Nome, CPF ou ID (Ex: CLI-00001)..." class="w-full pl-11 pr-4 py-4 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-[#fbbf24] text-sm font-medium text-slate-700 placeholder-slate-400">
                </div>
                <div class="flex gap-4 w-full md:w-auto">
                    <select class="py-4 px-6 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-[#fbbf24] text-sm font-bold text-slate-600">
                        <option value="">Todos os Perfis</option>
                        <option value="Feminino">Feminino</option>
                        <option value="Masculino">Masculino</option>
                        <option value="Infantil">Infantil</option>
                    </select>
                    <button class="bg-slate-900 hover:bg-slate-800 text-[#fbbf24] px-8 py-4 rounded-xl font-black text-xs uppercase tracking-widest transition">
                        Filtrar
                    </button>
                </div>
            </div>

            <!-- TABELA DE CLIENTES -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-[2rem] border border-slate-100">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                            <th class="py-5 pl-8">ID</th>
                            <th class="py-5">Nome do Cliente</th>
                            <th class="py-5">Contato</th>
                            <th class="py-5">Perfil</th>
                            <th class="py-5 text-center">Status</th>
                            <th class="py-5 text-right pr-8">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($clientes as $cliente)
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <td class="py-5 pl-8">
                                    <!-- Formata o ID do banco (Ex: 1) para o padrão CLI-00001 -->
                                    <span class="text-xs font-bold text-slate-400">CLI-{{ str_pad($cliente->id, 5, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td class="py-5">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-slate-800 text-sm">{{ $cliente->nome }}</span>
                                        @if($cliente->cpf)
                                            <span class="text-[10px] text-slate-400 font-medium mt-0.5">CPF: {{ $cliente->cpf }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-5">
                                    <div class="flex items-center text-sm font-medium text-slate-600">
                                        <i class="fab fa-whatsapp text-emerald-500 mr-2 text-lg"></i> {{ $cliente->telefone }}
                                    </div>
                                </td>
                                <td class="py-5">
                                    <span class="bg-amber-50 text-amber-600 px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider">
                                        {{ $cliente->tipo }}
                                    </span>
                                </td>
                                <td class="py-5 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                        Ativo
                                    </span>
                                </td>
                                <td class="py-5 text-right pr-8">
                                    <!-- AQUI ESTÁ A ROTA CORRIGIDA PARA ABRIR A FICHA -->
                                    <a href="{{ route('clientes.show', $cliente->id) }}" class="inline-flex items-center justify-center bg-slate-100 text-slate-600 px-4 py-2 rounded-xl font-bold text-xs uppercase tracking-wide hover:bg-slate-900 hover:text-[#fbbf24] transition">
                                        Abrir Ficha
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-16 text-center">
                                    <div class="text-slate-200 mb-4"><i class="fas fa-id-card text-5xl"></i></div>
                                    <h3 class="text-lg font-black text-slate-700 mb-1">Nenhum cliente por aqui</h3>
                                    <p class="text-slate-500 font-medium text-sm">Comece adicionando seu primeiro cliente ao sistema.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>