<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-slate-800 uppercase tracking-tighter truncate">{{ $encomenda->titulo }}</h2>
            <a href="{{ route('encomendas.index') }}" class="text-slate-400 hover:text-slate-600 font-bold text-sm uppercase tracking-widest transition">&larr; Encomendas</a>
        </div>
    </x-slot>

    @php
        // Mapa: rótulo da ficha da Andreia => coluna no banco (tabela medidas)
        $camposMedida = [
            'Alto Busto' => 'alto_busto', 'Busto' => 'busto_torax', 'Tórax' => 'torax_medida',
            'Cintura' => 'cintura', 'Altura Corpo' => 'altura_corpo', 'Centro Frente' => 'centro_frente',
            'Centro Costas' => 'centro_costas', 'Altura do Busto' => 'altura_busto', 'Distância de Busto' => 'distancia_busto',
            'Cava a Cava' => 'cava_a_cava', 'Ombro a Ombro' => 'ombro_a_ombro', 'Ombro' => 'ombro',
            'Lateral' => 'lateral', 'Diagonais' => 'diagonais', 'Circ. Cava' => 'circ_cava',
            'Quadril' => 'quadril', 'Altura do Quadril' => 'altura_quadril', 'Quadril Alto' => 'quadril_alto',
            'Quadril Baixo' => 'quadril_baixo', 'Altura Q. Alto' => 'altura_q_alto', 'Altura Q. Baixo' => 'altura_q_baixo',
            'Comp. Saia' => 'comp_saia', 'Comp. Cauda' => 'comp_cauda', 'Decotes' => 'decotes',
            'Braço' => 'braco', 'Comp. Manga' => 'manga', 'Cotovelo Dobrado' => 'cotovelo_dobrado',
            'Altura do Cotovelo' => 'altura_cotovelo', 'Punho' => 'punho', 'Mão' => 'mao', 'Cabeça da Manga' => 'cabeca_manga',
        ];
    @endphp

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl font-medium text-sm">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-700 px-6 py-4 rounded-2xl font-medium text-sm">
                    <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <!-- CABEÇALHO -->
            <div class="bg-slate-900 rounded-[2.5rem] p-8 shadow-2xl relative overflow-hidden">
                <div class="absolute -right-8 -bottom-8 text-slate-800 opacity-50 pointer-events-none"><i class="fas fa-scissors text-9xl"></i></div>
                <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <span class="bg-[#fbbf24] text-slate-900 text-[10px] font-black uppercase tracking-widest px-2 py-1 rounded-md">ENC-{{ str_pad($encomenda->id, 5, '0', STR_PAD_LEFT) }}</span>
                            <span class="bg-slate-800 text-slate-300 text-[10px] font-black uppercase tracking-widest px-2 py-1 rounded-md">{{ ucfirst($encomenda->tipo) }}</span>
                            @if($encomenda->estaAtrasada())
                                <span class="bg-rose-500 text-white text-[10px] font-black uppercase tracking-widest px-2 py-1 rounded-md animate-pulse">Atrasada</span>
                            @endif
                        </div>
                        <h1 class="text-2xl font-black text-white tracking-tight">{{ $encomenda->titulo }}</h1>
                        <p class="text-slate-400 font-medium text-sm mt-1"><i class="fas fa-user mr-1"></i> {{ $encomenda->cliente->nome ?? '—' }}</p>
                    </div>
                    <div class="grid grid-cols-3 gap-4 text-center">
                        <div class="bg-slate-800/60 rounded-2xl px-4 py-3">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Prova</p>
                            <p class="text-white font-black text-sm">{{ $encomenda->data_prova ? $encomenda->data_prova->format('d/m/y') : '—' }}</p>
                        </div>
                        <div class="bg-slate-800/60 rounded-2xl px-4 py-3">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Entrega</p>
                            <p class="font-black text-sm {{ $encomenda->estaAtrasada() ? 'text-rose-400' : 'text-white' }}">{{ $encomenda->data_entrega ? $encomenda->data_entrega->format('d/m/y') : '—' }}</p>
                        </div>
                        <div class="bg-slate-800/60 rounded-2xl px-4 py-3">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Saldo</p>
                            <p class="text-[#fbbf24] font-black text-sm">R$ {{ number_format($encomenda->saldo(), 2, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- LINHA DO TEMPO DE PRODUÇÃO -->
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-black text-slate-800 tracking-tighter">Produção</h3>
                    <span class="px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-wide
                        @if($encomenda->etapa === 'entregue') bg-emerald-100 text-emerald-700
                        @elseif($encomenda->etapa === 'cancelada') bg-rose-100 text-rose-700
                        @else bg-amber-100 text-amber-800 @endif">
                        {{ $encomenda->etapaLabel() }}
                    </span>
                </div>

                @php $idxAtual = $encomenda->etapaIndice(); @endphp
                <div class="flex flex-wrap gap-2 mb-6">
                    @foreach(\App\Models\Encomenda::ETAPAS_PRODUCAO as $i => $etapaKey)
                        <div class="flex items-center">
                            <div class="flex flex-col items-center">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-black
                                    {{ $idxAtual >= $i && $idxAtual !== -1 ? 'bg-slate-900 text-[#fbbf24]' : 'bg-slate-100 text-slate-400' }}">
                                    {{ $i + 1 }}
                                </div>
                                <span class="text-[9px] font-bold uppercase tracking-wide mt-1 {{ $idxAtual >= $i && $idxAtual !== -1 ? 'text-slate-700' : 'text-slate-300' }}">{{ \App\Models\Encomenda::ETAPAS[$etapaKey] }}</span>
                            </div>
                            @if(!$loop->last)<div class="w-6 h-0.5 mx-1 mt-[-16px] {{ $idxAtual > $i && $idxAtual !== -1 ? 'bg-slate-900' : 'bg-slate-100' }}"></div>@endif
                        </div>
                    @endforeach
                </div>

                <!-- Alterar etapa -->
                <form action="{{ route('encomendas.etapa', $encomenda) }}" method="POST" class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-end border-t border-slate-100 pt-6">
                    @csrf @method('PUT')
                    <div class="flex-1">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Atualizar etapa</label>
                        <select name="etapa" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none">
                            @foreach($etapas as $valor => $rotulo)
                                <option value="{{ $valor }}" @selected($encomenda->etapa === $valor)>{{ $rotulo }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="bg-slate-900 text-[#fbbf24] px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition">Salvar Etapa</button>
                </form>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- MEDIDAS DA CLIENTE -->
                <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-black text-slate-800 tracking-tighter"><i class="fas fa-ruler-combined text-slate-300 mr-2"></i>Medidas</h3>
                        @if($encomenda->cliente)
                            <a href="{{ route('clientes.show', $encomenda->cliente->id) }}" class="text-[11px] font-bold text-slate-400 hover:text-slate-600 uppercase tracking-widest">Ver ficha &rarr;</a>
                        @endif
                    </div>

                    <!-- Escolher/atualizar qual ficha de medidas usar -->
                    <form action="{{ route('encomendas.update', $encomenda) }}" method="POST" class="mb-6">
                        @csrf @method('PUT')
                        <input type="hidden" name="titulo" value="{{ $encomenda->titulo }}">
                        <input type="hidden" name="tipo" value="{{ $encomenda->tipo }}">
                        <div class="flex gap-3 items-end">
                            <div class="flex-1">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Ficha de medidas usada</label>
                                <select name="medida_id" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none">
                                    <option value="">Nenhuma vinculada</option>
                                    @foreach($medidas as $m)
                                        <option value="{{ $m->id }}" @selected($encomenda->medida_id == $m->id)>{{ \Carbon\Carbon::parse($m->data_medicao)->format('d/m/Y') }} — {{ $m->responsavel_medicao ?? 'Ateliê' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-3 rounded-xl font-black text-xs uppercase tracking-widest transition">Vincular</button>
                        </div>
                    </form>

                    @if($encomenda->medida)
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-x-4 gap-y-3">
                            @foreach($camposMedida as $rotulo => $coluna)
                                @if(!empty($encomenda->medida->{$coluna}))
                                    <div class="bg-slate-50 rounded-lg px-3 py-2 border border-slate-100">
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-wide leading-tight">{{ $rotulo }}</p>
                                        <p class="font-black text-slate-800 text-sm">{{ $encomenda->medida->{$coluna} }}</p>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        @if(collect($camposMedida)->every(fn($col) => empty($encomenda->medida->{$col})))
                            <p class="text-slate-400 text-sm italic">A ficha vinculada não tem medidas preenchidas.</p>
                        @endif
                    @else
                        <p class="text-slate-400 text-sm italic">Nenhuma ficha de medidas vinculada. Selecione uma acima ou cadastre na ficha da cliente.</p>
                    @endif
                </div>

                <!-- TECIDOS / CROQUI / DESCRIÇÃO -->
                <div class="space-y-6">
                    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8">
                        <h3 class="text-lg font-black text-slate-800 tracking-tighter mb-4"><i class="fas fa-image text-slate-300 mr-2"></i>Croqui</h3>
                        @if($encomenda->croqui_path)
                            <img src="{{ asset('storage/'.$encomenda->croqui_path) }}" alt="Croqui" class="w-full rounded-2xl border border-slate-100">
                        @else
                            <p class="text-slate-400 text-sm italic">Nenhum croqui enviado ainda.</p>
                        @endif
                    </div>

                    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8 space-y-4">
                        <div>
                            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Tecidos</h4>
                            <p class="text-slate-700 text-sm whitespace-pre-line">{{ $encomenda->tecidos ?: '—' }}</p>
                        </div>
                        <div>
                            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Descrição</h4>
                            <p class="text-slate-700 text-sm whitespace-pre-line">{{ $encomenda->descricao ?: '—' }}</p>
                        </div>
                        <div>
                            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Observações</h4>
                            <p class="text-slate-700 text-sm whitespace-pre-line">{{ $encomenda->observacoes ?: '—' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- EDITAR DADOS DA ENCOMENDA -->
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8" x-data="{ aberto: false }">
                <button type="button" @click="aberto = !aberto" class="w-full flex items-center justify-between text-left">
                    <h3 class="text-lg font-black text-slate-800 tracking-tighter"><i class="fas fa-pen text-slate-300 mr-2"></i>Editar dados</h3>
                    <i class="fas" :class="aberto ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                </button>
                <form action="{{ route('encomendas.update', $encomenda) }}" method="POST" enctype="multipart/form-data" x-show="aberto" x-cloak class="mt-6 space-y-6">
                    @csrf @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Peça / Modelo *</label>
                            <input type="text" name="titulo" value="{{ $encomenda->titulo }}" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Tipo *</label>
                            <select name="tipo" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none">
                                @foreach(\App\Models\Encomenda::TIPOS as $valor => $rotulo)
                                    <option value="{{ $valor }}" @selected($encomenda->tipo === $valor)>{{ $rotulo }}</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" name="medida_id" value="{{ $encomenda->medida_id }}">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Data de Prova</label>
                            <input type="date" name="data_prova" value="{{ optional($encomenda->data_prova)->format('Y-m-d') }}" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Data de Entrega</label>
                            <input type="date" name="data_entrega" value="{{ optional($encomenda->data_entrega)->format('Y-m-d') }}" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Valor Total (R$)</label>
                            <input type="number" step="0.01" min="0" name="valor" value="{{ $encomenda->valor }}" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Sinal / Entrada (R$)</label>
                            <input type="number" step="0.01" min="0" name="sinal" value="{{ $encomenda->sinal }}" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Tecidos</label>
                            <textarea name="tecidos" rows="2" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none">{{ $encomenda->tecidos }}</textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Descrição</label>
                            <textarea name="descricao" rows="2" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none">{{ $encomenda->descricao }}</textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Observações</label>
                            <textarea name="observacoes" rows="2" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#fbbf24] focus:outline-none">{{ $encomenda->observacoes }}</textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Trocar croqui</label>
                            <input type="file" name="croqui" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:text-xs file:font-black file:uppercase file:tracking-widest file:bg-slate-900 file:text-[#fbbf24] hover:file:bg-slate-800 file:cursor-pointer">
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="bg-slate-900 text-[#fbbf24] px-8 py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 shadow-xl transition">Salvar Alterações</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
