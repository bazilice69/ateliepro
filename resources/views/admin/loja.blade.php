@extends('admin.layout')

@section('titulo', $loja->nome_fantasia)

@section('conteudo')
    {{-- Senha provisória recém-gerada (mostrada uma única vez) --}}
    @if(session('senha_provisoria'))
        <div class="bg-amber-50 border-2 border-amber-200 rounded-2xl p-5 mb-4">
            <div class="flex items-start gap-3">
                <i class="fas fa-key text-amber-500 text-xl mt-0.5"></i>
                <div class="flex-1">
                    <p class="font-black text-amber-800 text-sm uppercase tracking-wider">Senha provisória gerada</p>
                    <p class="text-xs text-amber-700 mt-1">Repasse estes dados ao cliente. A senha <strong>não será mostrada novamente</strong>:</p>
                    <div class="mt-3 grid sm:grid-cols-2 gap-3">
                        <div class="bg-white rounded-xl px-4 py-2 border border-amber-200">
                            <p class="text-[9px] font-black uppercase text-slate-400 tracking-widest">Login (e-mail)</p>
                            <p class="font-mono font-bold text-slate-800 text-sm">{{ session('senha_usuario') }}</p>
                        </div>
                        <div class="bg-white rounded-xl px-4 py-2 border border-amber-200">
                            <p class="text-[9px] font-black uppercase text-slate-400 tracking-widest">Senha provisória</p>
                            <p class="font-mono font-black text-slate-900 text-lg tracking-wider">{{ session('senha_provisoria') }}</p>
                        </div>
                    </div>
                    <p class="text-[11px] text-amber-600 mt-2"><i class="fas fa-circle-info mr-1"></i> Oriente o cliente a trocar a senha após entrar (em "Minha Conta").</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('success') && !session('senha_provisoria'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3 rounded-2xl font-medium text-sm mb-4">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-700 px-5 py-3 rounded-2xl font-medium text-sm mb-4">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
    @endif

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
                        <li class="py-3">
                            <div class="flex justify-between items-center">
                                <div>
                                    <div class="text-sm font-semibold text-slate-700">{{ $u->name }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $u->email }}</div>
                                </div>
                                <span class="text-[10px] font-bold text-slate-500 uppercase">{{ $u->role }}</span>
                            </div>
                            {{-- Redefinir senha deste usuário (suporte) --}}
                            <details class="mt-2">
                                <summary class="cursor-pointer text-[11px] font-bold text-slate-400 hover:text-slate-700 uppercase tracking-wide">
                                    <i class="fas fa-key mr-1"></i> Redefinir senha
                                </summary>
                                <form method="POST" action="{{ route('admin.lojas.senha', $loja) }}" class="mt-2 flex flex-col sm:flex-row gap-2 items-stretch sm:items-end bg-slate-50 rounded-xl p-3">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $u->id }}">
                                    <div class="flex-1">
                                        <label class="text-[9px] font-black uppercase text-slate-400 tracking-widest">Nova senha (opcional)</label>
                                        <input type="text" name="nova_senha" placeholder="deixe vazio = senha automática" class="w-full mt-1 p-2 bg-white border border-slate-200 rounded-lg text-sm text-slate-700">
                                    </div>
                                    <button class="bg-slate-900 text-[#fbbf24] px-4 py-2 rounded-lg font-black text-[11px] uppercase tracking-widest hover:bg-slate-800 transition whitespace-nowrap"
                                            onclick="return confirm('Redefinir a senha de {{ $u->name }}? A senha atual deixará de funcionar.')">
                                        Redefinir
                                    </button>
                                </form>
                            </details>
                        </li>
                    @empty
                        <li class="py-2 text-sm text-slate-400">Nenhum usuário.</li>
                    @endforelse
                </ul>
                <p class="text-[11px] text-slate-400 mt-3"><i class="fas fa-circle-info mr-1"></i> Gera uma senha provisória para repassar ao cliente que esqueceu a dele.</p>
            </div>
        </div>
    </div>
@endsection
