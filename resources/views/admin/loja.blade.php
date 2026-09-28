@extends('admin.layout')

@section('titulo', $loja->nome_fantasia)

@section('conteudo')
    <div class="flex justify-between items-center">
        <a href="{{ route('admin.dashboard') }}" class="text-xs font-bold text-slate-400 hover:text-slate-700 uppercase tracking-widest">← Voltar para as lojas</a>
        <div class="flex gap-2">
            <a href="{{ route('admin.lojas.entrar', $loja) }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 rounded-xl font-bold text-xs uppercase tracking-wide">
                <i class="fas fa-right-to-bracket mr-1"></i> Entrar como
            </a>
            <form method="POST" action="{{ route('admin.lojas.estender', $loja) }}" class="flex items-center gap-1">
                @csrf
                <input type="number" name="meses" value="1" min="1" max="24" class="w-16 p-2 bg-slate-50 border-none rounded-lg text-sm font-semibold text-center">
                <button class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded-xl font-bold text-xs uppercase tracking-wide">+ Meses</button>
            </form>
        </div>
    </div>

    <!-- Indicadores -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4 mb-8">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
            <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Usuários</p>
            <p class="text-3xl font-black text-slate-800 mt-1">{{ $indicadores['usuarios'] }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
            <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Clientes</p>
            <p class="text-3xl font-black text-slate-800 mt-1">{{ $indicadores['clientes'] }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
            <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Peças</p>
            <p class="text-3xl font-black text-slate-800 mt-1">{{ $indicadores['pecas'] }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
            <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Locações</p>
            <p class="text-3xl font-black text-slate-800 mt-1">{{ $indicadores['locacoes'] }}</p>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
        <!-- Plano / Valor / Desconto -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
            <h3 class="font-black text-slate-800 uppercase text-sm tracking-wider mb-4">Plano & Cobrança</h3>
            <form method="POST" action="{{ route('admin.lojas.plano', $loja) }}" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="text-[10px] font-black uppercase text-slate-400">Plano</label>
                    <select name="plano" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                        @foreach(['Essencial', 'Profissional', 'Premium'] as $p)
                            <option value="{{ $p }}" @selected($loja->plano === $p)>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400">Valor mensal (R$)</label>
                        <input type="number" step="0.01" name="valor_mensal" value="{{ old('valor_mensal', $loja->valor_mensal) }}" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400">Desconto (R$)</label>
                        <input type="number" step="0.01" name="desconto" value="{{ old('desconto', $loja->desconto) }}" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                    </div>
                </div>
                <div>
                    <label class="text-[10px] font-black uppercase text-slate-400">Vencimento</label>
                    <input type="date" name="data_vencimento" value="{{ old('data_vencimento', $loja->data_vencimento?->format('Y-m-d')) }}" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                </div>
                <div>
                    <label class="text-[10px] font-black uppercase text-slate-400">Observações internas</label>
                    <textarea name="observacoes_admin" rows="2" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl text-sm text-slate-700">{{ old('observacoes_admin', $loja->observacoes_admin) }}</textarea>
                </div>
                <button class="w-full bg-slate-900 text-[#fbbf24] py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition">Salvar Plano</button>
            </form>
        </div>

        <!-- Status -->
        <div class="space-y-6">
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                <h3 class="font-black text-slate-800 uppercase text-sm tracking-wider mb-4">Status da Loja</h3>
                <form method="POST" action="{{ route('admin.lojas.status', $loja) }}" class="space-y-4">
                    @csrf @method('PUT')
                    <select name="status" class="w-full p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                        <option value="ativo" @selected($loja->status === 'ativo')>🟢 Ativa</option>
                        <option value="inadimplente" @selected($loja->status === 'inadimplente')>🟡 Inadimplente</option>
                        <option value="bloqueado" @selected($loja->status === 'bloqueado')>🔴 Bloqueada</option>
                    </select>
                    <button class="w-full bg-slate-900 text-[#fbbf24] py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition">Atualizar Status</button>
                </form>
                <p class="text-[11px] text-slate-400 mt-3">Lojas inadimplentes/bloqueadas não acessam o sistema.</p>
            </div>

            <!-- Usuários da loja -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                <h3 class="font-black text-slate-800 uppercase text-sm tracking-wider mb-4">Usuários</h3>
                <ul class="divide-y divide-slate-50">
                    @forelse($usuarios as $u)
                        <li class="py-2 flex justify-between items-center">
                            <div>
                                <div class="text-sm font-semibold text-slate-700">{{ $u->name }}</div>
                                <div class="text-[10px] text-slate-400">{{ $u->email }}</div>
                            </div>
                            <span class="text-[10px] font-bold text-slate-500 uppercase">{{ $u->role }}</span>
                        </li>
                    @empty
                        <li class="py-2 text-sm text-slate-400">Nenhum usuário.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
@endsection
