<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $marca = \App\Models\Setting::nomeSistema();
        $nomeLoja = $loja->nome_fantasia ?? $marca;
        // Mês/ano em português SEM depender de locale/intl (evita erro no servidor).
        $meses = [1=>'Janeiro',2=>'Fevereiro',3=>'Março',4=>'Abril',5=>'Maio',6=>'Junho',7=>'Julho',8=>'Agosto',9=>'Setembro',10=>'Outubro',11=>'Novembro',12=>'Dezembro'];
        $mesAno = $meses[(int) now()->format('n')].' de '.now()->format('Y');
        $dataHoje = now()->format('d/m/Y');
    @endphp
    <title>Catálogo — {{ $nomeLoja }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root { --ouro: #c9a24b; }
        body { font-family: 'Inter', sans-serif; background:#f1f5f9; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .serif { font-family: 'Cormorant Garamond', serif; }
        .text-ouro { color: var(--ouro); }
        .bg-ouro { background: var(--ouro); }
        /* Cada "página" do catálogo = uma folha A4 */
        .pagina { width: 210mm; min-height: 297mm; margin: 0 auto 10mm; background:#fff; box-shadow: 0 10px 40px rgba(0,0,0,.12); overflow:hidden; }
        @media print {
            body { background:#fff; }
            .no-print { display:none !important; }
            .pagina { box-shadow:none; margin:0; width:auto; min-height:auto; page-break-after: always; }
            @page { size: A4; margin: 0; }
        }
    </style>
</head>
<body class="py-8">

    <!-- Barra de ações (não imprime) -->
    <div class="no-print max-w-3xl mx-auto mb-6 flex items-center justify-between px-4">
        <a href="{{ route('acervo.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-900 uppercase">← Voltar ao acervo</a>
        <div class="flex items-center gap-3">
            <span class="text-xs text-slate-400">{{ $pecas->count() }} peça(s)</span>
            <button onclick="window.print()" class="bg-slate-900 text-amber-400 px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest">🖨️ Salvar / Imprimir PDF</button>
        </div>
    </div>

    @if($pecas->isEmpty())
        <div class="no-print max-w-3xl mx-auto bg-white rounded-2xl p-10 text-center text-slate-500">
            Nenhuma peça disponível para o catálogo. Cadastre peças (com foto) ou use <a href="{{ route('acervo.catalogo', ['todas'=>1]) }}" class="text-ouro font-bold underline">incluir todas</a>.
        </div>
    @else

    <!-- ===== CAPA estilo revista ===== -->
    <div class="pagina relative flex flex-col justify-between text-white"
         style="background: linear-gradient(135deg,#0b0b0d 0%, #1c1a14 60%, #2a2416 100%);">
        <!-- brilho dourado -->
        <div style="position:absolute; top:-120px; right:-120px; width:420px; height:420px; border-radius:9999px; background: radial-gradient(circle, rgba(201,162,75,.35), transparent 70%);"></div>

        <div class="p-16 relative">
            @if($loja && $loja->logo)
                <img src="{{ asset('storage/'.$loja->logo) }}" alt="{{ $nomeLoja }}" style="max-height:110px" class="object-contain mb-10">
            @else
                <div class="w-20 h-20 rounded-full border-2 border-ouro flex items-center justify-center text-ouro serif text-4xl font-bold mb-10">B</div>
            @endif
            <p class="text-ouro text-sm font-bold uppercase tracking-[0.5em] mb-6">Coleção</p>
            <h1 class="serif text-7xl font-bold leading-[0.95]">{{ $nomeLoja }}</h1>
            <p class="serif text-3xl text-white/70 mt-4 italic">Catálogo de peças</p>
        </div>

        <div class="p-16 relative">
            <div class="h-px bg-white/20 mb-6"></div>
            <div class="flex justify-between items-end">
                <div>
                    @if($categoriaFiltro)
                        <p class="text-ouro text-xs font-bold uppercase tracking-widest">Seleção: {{ \App\Models\Acervo::CATEGORIAS[$categoriaFiltro] ?? $categoriaFiltro }}</p>
                    @endif
                    <p class="text-white/50 text-sm">{{ $pecas->count() }} peças · {{ $mesAno }}</p>
                </div>
                @if($loja && ($loja->telefone || $loja->cidade))
                    <div class="text-right text-white/60 text-xs">
                        @if($loja->telefone)<p>{{ $loja->telefone }}</p>@endif
                        @if($loja->cidade)<p>{{ $loja->cidade }}@if($loja->estado)/{{ $loja->estado }}@endif</p>@endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- ===== PÁGINAS DE PEÇAS ===== -->
    @foreach($pecas->chunk(4) as $grupo)
        <div class="pagina p-12">
            <div class="flex items-center justify-between mb-8 pb-4 border-b border-slate-200">
                <span class="serif text-2xl font-bold text-slate-800">{{ $nomeLoja }}</span>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Catálogo · {{ $dataHoje }}</span>
            </div>

            <div class="grid grid-cols-2 gap-8">
                @foreach($grupo as $peca)
                    <div class="flex flex-col">
                        <div class="aspect-[3/4] bg-slate-100 rounded-xl overflow-hidden mb-3">
                            @if($peca->capa_url)
                                <img src="{{ $peca->capa_url }}" alt="{{ $peca->nome }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-300 text-5xl">👗</div>
                            @endif
                        </div>
                        <p class="text-[10px] font-bold text-ouro uppercase tracking-widest">{{ $peca->categoria }}</p>
                        <p class="serif text-xl font-bold text-slate-900 leading-tight">{{ $peca->nome }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">Ref. {{ $peca->codigo }}@if($peca->tamanho) · Tam {{ $peca->tamanho }}@endif@if($peca->cor) · {{ $peca->cor }}@endif</p>
                        <p class="text-lg font-black text-slate-900 mt-1">R$ {{ number_format((float) $peca->valor_locacao, 2, ',', '.') }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach

    <!-- ===== CONTRACAPA ===== -->
    <div class="pagina relative flex flex-col items-center justify-center text-white text-center p-16"
         style="background: linear-gradient(135deg,#1c1a14, #0b0b0d);">
        @if($loja && $loja->logo)
            <img src="{{ asset('storage/'.$loja->logo) }}" alt="{{ $nomeLoja }}" style="max-height:90px" class="object-contain mb-8">
        @endif
        <h2 class="serif text-5xl font-bold">{{ $nomeLoja }}</h2>
        <p class="text-ouro uppercase tracking-[0.4em] text-xs mt-4">Agende sua visita</p>
        <div class="mt-8 text-white/70 space-y-1 text-sm">
            @if($loja && $loja->telefone)<p>📞 {{ $loja->telefone }}</p>@endif
            @if($loja && $loja->endereco)<p>📍 {{ $loja->endereco }}@if($loja->cidade) — {{ $loja->cidade }}@if($loja->estado)/{{ $loja->estado }}@endif @endif</p>@endif
        </div>
        <p class="absolute bottom-10 text-white/30 text-[10px] uppercase tracking-widest">Catálogo gerado em {{ $dataHoje }}</p>
    </div>

    @endif

</body>
</html>
