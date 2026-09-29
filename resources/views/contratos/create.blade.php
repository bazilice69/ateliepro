<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-slate-800 uppercase tracking-tighter">Gerar <span class="text-[#fbbf24]">Contrato</span></h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-700 px-6 py-4 rounded-2xl text-sm">{{ $errors->first() }}</div>
            @endif

            <!-- PASSO 1: escolher modelo + cliente/locação e pré-visualizar -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                <h3 class="font-black text-slate-800 uppercase text-sm tracking-wider mb-4">1. Escolha os dados</h3>
                <form method="POST" action="{{ route('contratos.preview') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    @csrf
                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400">Modelo</label>
                        <select name="modelo_contrato_id" required class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                            <option value="">Selecione...</option>
                            @foreach($modelos as $m)
                                <option value="{{ $m->id }}" @selected(isset($modelo) && $modelo->id == $m->id)>{{ $m->titulo }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400">Cliente</label>
                        <select name="cliente_id" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                            <option value="">— nenhum —</option>
                            @foreach($clientes as $c)
                                <option value="{{ $c->id }}" @selected(isset($cliente) && $cliente && $cliente->id == $c->id)>{{ $c->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400">Locação (opcional)</label>
                        <select name="locacao_id" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                            <option value="">— nenhuma —</option>
                            @foreach($locacoes as $l)
                                <option value="{{ $l->id }}" @selected(isset($locacao) && $locacao && $locacao->id == $l->id)>
                                    #{{ $l->id }} · {{ $l->acervo->nome ?? 'Peça' }} · {{ optional($l->data_evento)->format('d/m/Y') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-3">
                        <button class="bg-slate-900 text-[#fbbf24] px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition">
                            <i class="fas fa-magic mr-1"></i> Pré-visualizar (auto-preencher)
                        </button>
                        <span class="text-xs text-slate-400 ml-3">Dica: selecione a locação e o cliente/valores/peça são preenchidos sozinhos.</span>
                    </div>
                </form>
            </div>

            <!-- PASSO 2: revisar/editar e salvar (aparece após pré-visualizar) -->
            @isset($conteudoPreenchido)
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                    <h3 class="font-black text-slate-800 uppercase text-sm tracking-wider mb-4">2. Revise e finalize</h3>
                    <form method="POST" action="{{ route('contratos.store') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="cliente_id" value="{{ $cliente->id ?? '' }}">
                        <input type="hidden" name="locacao_id" value="{{ $locacao->id ?? '' }}">
                        <input type="hidden" name="modelo_contrato_id" value="{{ $modelo->id ?? '' }}">
                        <div>
                            <label class="text-[10px] font-black uppercase text-slate-400">Título do contrato</label>
                            <input type="text" name="titulo" value="{{ $modelo->titulo ?? 'Contrato' }}{{ isset($cliente) && $cliente ? ' - '.$cliente->nome : '' }}" required class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                        </div>
                        <div>
                            <label class="text-[10px] font-black uppercase text-slate-400">Conteúdo (edite se precisar antes de finalizar)</label>
                            <textarea name="conteudo_final" rows="18" required class="w-full mt-1 p-4 bg-slate-50 border-none rounded-xl font-mono text-sm text-slate-700 leading-relaxed">{{ $conteudoPreenchido }}</textarea>
                            <p class="text-xs text-slate-400 mt-1">Campos que ficaram como "____________" não tinham dado cadastrado — preencha à mão se quiser.</p>
                        </div>
                        <button class="w-full bg-emerald-500 hover:bg-emerald-600 text-white py-3 rounded-xl font-black text-xs uppercase tracking-widest transition">
                            <i class="fas fa-file-signature mr-1"></i> Gerar Contrato
                        </button>
                    </form>
                </div>
            @endisset
        </div>
    </div>
</x-app-layout>
