@extends('admin.layout')

@section('titulo', 'Minha Conta')

@section('conteudo')
    <div class="max-w-xl bg-white rounded-3xl p-8 shadow-sm border border-slate-100">
        <h3 class="font-black text-slate-800 uppercase text-sm tracking-wider mb-4">Dados do Administrador</h3>
        <form method="POST" action="{{ route('admin.conta.update') }}" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="text-[10px] font-black uppercase text-slate-400">Nome</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
            </div>
            <div>
                <label class="text-[10px] font-black uppercase text-slate-400">E-mail</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
            </div>

            <div class="pt-2 border-t border-slate-100">
                <p class="text-[11px] text-slate-400 mt-3 mb-2">Deixe os campos de senha em branco para manter a atual.</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400">Nova senha</label>
                        <input type="password" name="password" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400">Confirmar nova senha</label>
                        <input type="password" name="password_confirmation" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                    </div>
                </div>
            </div>

            <button class="bg-slate-900 text-[#fbbf24] px-8 py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition">Salvar</button>
        </form>
    </div>
@endsection
