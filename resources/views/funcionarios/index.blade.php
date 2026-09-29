<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-slate-800 leading-tight uppercase tracking-tighter">
                Equipe da <span class="text-[#fbbf24]">Loja</span>
            </h2>
            <a href="{{ route('funcionarios.create') }}" class="bg-slate-900 text-[#fbbf24] px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition shadow-lg">
                + Novo Funcionário
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl font-medium text-sm">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-700 px-6 py-4 rounded-2xl font-medium text-sm">{{ $errors->first() }}</div>
            @endif

            <div class="bg-white overflow-x-auto shadow-sm sm:rounded-[2rem] border border-slate-100">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                            <th class="py-5 pl-8">Nome</th>
                            <th class="py-5">E-mail</th>
                            <th class="py-5 text-center">Função</th>
                            <th class="py-5">Último acesso</th>
                            <th class="py-5 text-right pr-8">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($funcionarios as $f)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-5 pl-8 font-bold text-slate-800 text-sm">{{ $f->name }}</td>
                                <td class="py-5 text-sm text-slate-600">{{ $f->email }}</td>
                                <td class="py-5 text-center">
                                    <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider {{ $f->role === 'admin_loja' ? 'bg-amber-50 text-amber-600' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $f->role === 'admin_loja' ? 'Administrador' : 'Funcionário' }}
                                    </span>
                                </td>
                                <td class="py-5 text-sm text-slate-500">{{ $f->last_login_at?->format('d/m/Y H:i') ?? 'Nunca' }}</td>
                                <td class="py-5 text-right pr-8 space-x-2">
                                    <a href="{{ route('funcionarios.edit', $f) }}" class="text-xs font-bold text-slate-500 hover:text-slate-900 uppercase">Editar</a>
                                    @if($f->id !== auth()->id())
                                        <form method="POST" action="{{ route('funcionarios.destroy', $f) }}" class="inline" onsubmit="return confirm('Remover este funcionário?')">
                                            @csrf @method('DELETE')
                                            <button class="text-xs font-bold text-rose-500 hover:text-rose-700 uppercase">Remover</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
