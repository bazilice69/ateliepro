<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-slate-800 uppercase tracking-tighter">{{ $modelo->exists ? 'Editar' : 'Novo' }} <span class="text-[#fbbf24]">Modelo</span></h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            @if($errors->any())
                <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-700 px-6 py-4 rounded-2xl text-sm">{{ $errors->first() }}</div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Formulário -->
                <form method="POST" action="{{ $modelo->exists ? route('contratos.modelos.update', $modelo) : route('contratos.modelos.store') }}" class="lg:col-span-2 bg-white rounded-3xl p-6 shadow-sm border border-slate-100 space-y-4">
                    @csrf
                    @if($modelo->exists) @method('PUT') @endif
                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400">Título do modelo</label>
                        <input type="text" name="titulo" value="{{ old('titulo', $modelo->titulo) }}" required placeholder="Ex: Contrato de Locação" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400">Conteúdo (use as variáveis ao lado)</label>
                        <textarea name="conteudo" rows="20" required class="w-full mt-1 p-4 bg-slate-50 border-none rounded-xl font-mono text-sm text-slate-700 leading-relaxed">{{ old('conteudo', $modelo->conteudo) }}</textarea>
                    </div>
                    <label class="flex items-center gap-2 text-sm font-semibold text-slate-600">
                        <input type="checkbox" name="ativo" value="1" @checked(old('ativo', $modelo->ativo ?? true)) class="rounded"> Modelo ativo
                    </label>
                    <div class="flex gap-3">
                        <a href="{{ route('contratos.modelos.index') }}" class="px-6 py-3 text-slate-400 font-bold text-xs uppercase">Cancelar</a>
                        <button class="flex-1 bg-slate-900 text-[#fbbf24] py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition">Salvar Modelo</button>
                    </div>
                </form>

                <!-- Variáveis disponíveis -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 h-fit">
                    <h3 class="font-black text-slate-800 uppercase text-sm tracking-wider mb-2">Variáveis disponíveis</h3>
                    <p class="text-xs text-slate-400 mb-4">Copie e cole no texto. Elas serão trocadas pelos dados reais ao gerar o contrato.</p>
                    <div class="space-y-1.5 max-h-[28rem] overflow-y-auto pr-1">
                        @foreach($variaveis as $chave => $desc)
                            @php $tag = '{'.'{'.$chave.'}'.'}'; @endphp
                            <div class="flex items-center justify-between gap-2 text-xs">
                                <code class="bg-slate-100 text-slate-700 px-2 py-1 rounded font-mono cursor-pointer hover:bg-amber-100" onclick="navigator.clipboard.writeText('{{ $tag }}')" title="Clique para copiar">{{ $tag }}</code>
                                <span class="text-slate-400 text-right flex-1">{{ $desc }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
