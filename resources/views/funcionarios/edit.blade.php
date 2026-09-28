<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-slate-800 uppercase tracking-tighter">Editar <span class="text-[#fbbf24]">Funcionário</span></h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-[2rem] border border-slate-100 p-8">
                @if($errors->any())
                    <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-700 px-6 py-4 rounded-2xl text-sm">{{ $errors->first() }}</div>
                @endif
                <form method="POST" action="{{ route('funcionarios.update', $user) }}" class="space-y-5">
                    @csrf @method('PUT')
                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400">Nome</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400">E-mail</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400">Função</label>
                        <select name="role" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                            <option value="funcionario" @selected($user->role === 'funcionario')>Funcionário</option>
                            <option value="admin_loja" @selected($user->role === 'admin_loja')>Administrador da Loja</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400">Nova senha (deixe em branco para manter)</label>
                        <input type="password" name="password" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400">Confirmar nova senha</label>
                        <input type="password" name="password_confirmation" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                    </div>
                    <div class="flex gap-3 pt-2">
                        <a href="{{ route('funcionarios.index') }}" class="px-6 py-3 text-slate-400 font-bold text-xs uppercase">Cancelar</a>
                        <button class="flex-1 bg-slate-900 text-[#fbbf24] py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
