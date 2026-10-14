<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $marca = \App\Models\Setting::nomeSistema();
        $nomeLoja = $loja->nome_fantasia ?? $marca;
        $meses = [1=>'janeiro',2=>'fevereiro',3=>'março',4=>'abril',5=>'maio',6=>'junho',7=>'julho',8=>'agosto',9=>'setembro',10=>'outubro',11=>'novembro',12=>'dezembro'];
        $dataExt = now()->format('d').' de '.$meses[(int)now()->format('n')].' de '.now()->format('Y');
        // Validade do orçamento: 15 dias
        $validade = now()->copy()->addDays(15)->format('d/m/Y');
    @endphp
    <title>Orçamento #{{ $orcamento->id }} — {{ $nomeLoja }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .serif { font-family: 'Cormorant Garamond', serif; }
        @media print { .no-print { display:none !important; } @page { margin: 1.5cm; } }
    </style>
</head>
<body class="bg-slate-100 p-6">

    <div class="no-print max-w-2xl mx-auto mb-4 flex justify-between items-center">
        <a href="{{ route('orcamentos.show', $orcamento) }}" class="text-xs font-bold text-slate-500 hover:text-slate-900 uppercase">← Voltar</a>
        <button onclick="window.print()" class="bg-slate-900 text-amber-400 px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest">🖨️ Imprimir / Salvar PDF</button>
    </div>

    <div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-lg overflow-hidden">
        <!-- Cabeçalho -->
        <div class="bg-slate-900 text-white p-8 flex items-center justify-between">
            <div class="flex items-center gap-4">
                @if($loja && $loja->logo)
                    <img src="{{ asset('storage/'.$loja->logo) }}" alt="{{ $nomeLoja }}" style="max-height:64px" class="object-contain">
                @else
                    <div class="w-14 h-14 rounded-full border-2 flex items-center justify-center serif text-2xl font-bold" style="border-color:#c9a24b;color:#c9a24b">B</div>
                @endif
                <div>
                    <p class="serif text-2xl font-bold">{{ $nomeLoja }}</p>
                    @if($loja && $loja->telefone)<p class="text-xs text-white/60">{{ $loja->telefone }}</p>@endif
                </div>
            </div>
            <div class="text-right">
                <p class="text-[10px] uppercase tracking-widest" style="color:#c9a24b">Orçamento</p>
                <p class="text-xl font-black">#{{ str_pad($orcamento->id, 4, '0', STR_PAD_LEFT) }}</p>
            </div>
        </div>

        <div class="p-8 space-y-6">
            <!-- Cliente + data -->
            <div class="flex justify-between text-sm">
                <div>
                    <p class="text-[10px] uppercase tracking-widest text-slate-400">Cliente</p>
                    <p class="font-bold text-slate-800 text-lg">{{ $orcamento->cliente?->nome ?? '—' }}</p>
                    @if($orcamento->cliente?->telefone)<p class="text-slate-500">{{ $orcamento->cliente->telefone }}</p>@endif
                </div>
                <div class="text-right">
                    <p class="text-[10px] uppercase tracking-widest text-slate-400">Data</p>
                    <p class="font-semibold text-slate-700">{{ $dataExt }}</p>
                </div>
            </div>

            <!-- Item do orçamento -->
            <div class="border border-slate-200 rounded-xl overflow-hidden">
                <div class="bg-slate-50 px-5 py-3 text-[10px] font-black uppercase tracking-widest text-slate-400 flex justify-between">
                    <span>Descrição</span><span>Valor</span>
                </div>
                <div class="px-5 py-5 flex justify-between items-start gap-4">
                    <div>
                        <p class="font-bold text-slate-800">{{ $orcamento->descricao ?: $orcamento->modoLabel() }}</p>
                        @if($orcamento->acervo)
                            <p class="text-sm text-slate-500 mt-0.5">Peça: {{ $orcamento->acervo->codigo }} · {{ $orcamento->acervo->nome }}</p>
                        @endif
                    </div>
                    <p class="font-bold text-slate-800 whitespace-nowrap">R$ {{ number_format((float)$orcamento->valor_final, 2, ',', '.') }}</p>
                </div>
            </div>

            <!-- Total -->
            <div class="bg-slate-900 rounded-xl p-6 text-center">
                <p class="text-[10px] uppercase tracking-widest" style="color:#c9a24b">Valor total do orçamento</p>
                <p class="text-4xl font-black text-white mt-1">R$ {{ number_format((float)$orcamento->valor_final, 2, ',', '.') }}</p>
            </div>

            <p class="text-xs text-slate-400 text-center">Orçamento válido até <strong>{{ $validade }}</strong>. Valores sujeitos a confirmação.</p>

            <!-- Rodapé -->
            <div class="pt-6 border-t border-slate-100 text-center text-xs text-slate-400">
                <p class="serif text-base text-slate-600">{{ $nomeLoja }}</p>
                @if($loja && ($loja->endereco || $loja->cidade))
                    <p>{{ $loja->endereco }}{{ $loja->cidade ? ' — '.$loja->cidade : '' }}{{ $loja->estado ? '/'.$loja->estado : '' }}</p>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
