<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-slate-800 uppercase tracking-tighter">
                Editar <span class="text-[#fbbf24]">{{ $peca->codigo }}</span>
            </h2>
            <a href="{{ route('acervo.show', $peca) }}" class="text-xs font-bold text-slate-400 hover:text-slate-700 uppercase tracking-widest self-center">← Voltar</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl font-medium text-sm">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="p-4 bg-rose-50 border-l-4 border-rose-500 rounded-r-xl text-rose-700">
                    <ul class="list-disc pl-5 font-medium">
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            {{-- ===== GALERIA DE FOTOS ===== --}}
            <div class="bg-white shadow-sm sm:rounded-[2rem] border border-slate-100 p-8">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">📸 Galeria de Fotos</h3>

                @if($peca->fotos->isNotEmpty())
                    <div class="grid grid-cols-3 md:grid-cols-5 gap-3 mb-6">
                        @foreach($peca->fotos as $foto)
                            <div class="relative aspect-[3/4] rounded-xl overflow-hidden border border-slate-200 bg-slate-50 group">
                                <img src="{{ $foto->url }}" class="w-full h-full object-cover">
                                @if($foto->capa)
                                    <span class="absolute bottom-1 left-1 text-[9px] bg-[#fbbf24] text-black font-black px-1.5 py-0.5 rounded">CAPA</span>
                                @else
                                    <form method="POST" action="{{ route('acervo.fotos.capa', [$peca, $foto]) }}" class="absolute bottom-1 left-1 opacity-0 group-hover:opacity-100 transition">
                                        @csrf
                                        <button class="text-[9px] bg-white/90 text-slate-700 font-bold px-1.5 py-0.5 rounded shadow">tornar capa</button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('acervo.fotos.destroy', [$peca, $foto]) }}" onsubmit="return confirm('Remover esta foto?')" class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition">
                                    @csrf @method('DELETE')
                                    <button class="w-6 h-6 rounded-full bg-black/60 text-white text-xs">✕</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-slate-400 mb-6">Nenhuma foto ainda. Adicione abaixo.</p>
                @endif

                {{-- Adicionar novas fotos --}}
                <form method="POST" action="{{ route('acervo.update', $peca) }}" enctype="multipart/form-data" class="flex flex-wrap items-center gap-3">
                    @csrf @method('PUT')
                    {{-- Reenvia os dados obrigatórios para a validação do update passar --}}
                    <input type="hidden" name="codigo" value="{{ $peca->codigo }}">
                    <input type="hidden" name="nome" value="{{ $peca->nome }}">
                    <input type="hidden" name="categoria" value="{{ $peca->categoria }}">
                    <input type="hidden" name="valor_locacao" value="{{ $peca->valor_locacao }}">
                    <label class="inline-flex items-center gap-2 cursor-pointer bg-white border border-slate-300 rounded-xl px-4 py-3 text-sm font-bold text-slate-700 hover:border-[#fbbf24]">
                        <i class="fas fa-camera text-[#fbbf24]"></i> Adicionar fotos
                        <input type="file" name="fotos[]" accept="image/*" multiple class="hidden" onchange="this.form.submit()">
                    </label>
                    <span class="text-xs text-slate-400">As fotos são adicionadas na hora. A 1ª vira capa se ainda não houver uma.</span>
                </form>
            </div>

            {{-- ===== DADOS DA PEÇA ===== --}}
            <div class="bg-white shadow-sm sm:rounded-[2rem] border border-slate-100 p-8">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">✏️ Dados da Peça</h3>
                <form method="POST" action="{{ route('acervo.update', $peca) }}" class="space-y-6">
                    @csrf @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Código *</label>
                            <input type="text" name="codigo" value="{{ old('codigo', $peca->codigo) }}" required class="w-full p-3 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none uppercase">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-slate-700 mb-2">Nome ou Modelo *</label>
                            <input type="text" name="nome" value="{{ old('nome', $peca->nome) }}" required class="w-full p-3 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-slate-700 mb-2">Categoria *</label>
                            <select name="categoria" required class="w-full p-3 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none">
                                @foreach(\App\Models\Acervo::CATEGORIAS as $val => $label)
                                    <option value="{{ $val }}" @selected($peca->categoria === $val)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Tamanho</label>
                            <input type="text" name="tamanho" value="{{ old('tamanho', $peca->tamanho) }}" class="w-full p-3 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Cor</label>
                            <input type="text" name="cor" value="{{ old('cor', $peca->cor) }}" class="w-full p-3 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-slate-700 mb-2">Valor de Locação (R$) *</label>
                            <input type="number" step="0.01" name="valor_locacao" value="{{ old('valor_locacao', $peca->valor_locacao) }}" required class="w-full p-3 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-slate-700 mb-2">Status</label>
                            <select name="status" class="w-full p-3 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none">
                                @foreach(['disponivel'=>'Disponível','reservada'=>'Reservada','alugada'=>'Alugada','em_prova'=>'Em prova','em_ajuste'=>'Em ajuste','lavanderia'=>'Lavanderia','manutencao'=>'Manutenção','indisponivel'=>'Indisponível'] as $val => $label)
                                    <option value="{{ $val }}" @selected($peca->status === $val)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Observações</label>
                        <textarea name="observacoes" rows="2" class="w-full p-3 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none">{{ old('observacoes', $peca->observacoes) }}</textarea>
                    </div>
                    <div class="flex justify-between items-center pt-2">
                        <button type="submit" class="bg-slate-900 text-[#fbbf24] px-8 py-4 rounded-2xl font-black text-sm uppercase tracking-widest hover:bg-slate-800 transition shadow-xl">Salvar alterações</button>
                    </div>
                </form>

                {{-- Apagar peça --}}
                <form method="POST" action="{{ route('acervo.destroy', $peca) }}" onsubmit="return confirm('Apagar a peça {{ $peca->codigo }} e todas as fotos? Esta ação não pode ser desfeita.')" class="mt-6 pt-6 border-t border-slate-100">
                    @csrf @method('DELETE')
                    <button class="text-sm font-bold text-rose-500 hover:text-rose-600">🗑️ Apagar esta peça</button>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
