<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Etiqueta — {{ $peca->codigo }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <style>
        body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        @media print {
            .no-print { display: none !important; }
            @page { margin: 10mm; }
        }
        /* Etiqueta compacta ~ 9x5cm */
        .etiqueta {
            width: 9cm;
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
        }
    </style>
</head>
<body class="bg-slate-100 p-6 font-sans">

    @php
        $marca = \App\Models\Setting::nomeSistema();
        $nomeLoja = $loja->nome_fantasia ?? $marca;
    @endphp

    <div class="max-w-xl mx-auto">
        <!-- Barra de ações (não imprime) -->
        <div class="no-print flex items-center justify-between mb-4">
            <a href="{{ route('acervo.show', $peca) }}" class="text-xs font-bold text-slate-500 hover:text-slate-900 uppercase">← Voltar</a>
            <button onclick="window.print()" class="bg-slate-900 text-amber-400 px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest">
                🖨️ Imprimir etiqueta
            </button>
        </div>

        <!-- Etiqueta -->
        <div class="etiqueta bg-white mx-auto p-4 flex gap-4 items-center">
            <!-- QR Code -->
            <div class="shrink-0 text-center">
                <div id="qrcode" class="w-[2.6cm] h-[2.6cm] flex items-center justify-center"></div>
                <p class="text-[7px] text-slate-400 mt-1 uppercase tracking-wider">Escaneie</p>
            </div>
            <!-- Dados -->
            <div class="flex-1 min-w-0">
                <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest truncate">{{ $nomeLoja }}</p>
                <p class="text-2xl font-black text-slate-900 leading-none mt-0.5">{{ $peca->codigo }}</p>
                <p class="text-[11px] font-semibold text-slate-700 leading-tight mt-1 line-clamp-2">{{ $peca->nome }}</p>
                <div class="flex flex-wrap gap-x-3 gap-y-0.5 mt-2 text-[9px] text-slate-500 font-medium">
                    <span><b class="text-slate-700">Cat:</b> {{ $peca->categoria }}</span>
                    @if($peca->tamanho)<span><b class="text-slate-700">Tam:</b> {{ $peca->tamanho }}</span>@endif
                    @if($peca->cor)<span><b class="text-slate-700">Cor:</b> {{ $peca->cor }}</span>@endif
                </div>
                <p class="text-sm font-black text-slate-900 mt-1">R$ {{ number_format((float) $peca->valor_locacao, 2, ',', '.') }}</p>
            </div>
        </div>

        <p class="no-print text-center text-xs text-slate-400 mt-4">
            Dica: ao escanear o QR Code, abre a ficha completa desta peça no sistema. 🔗
        </p>
    </div>

    <script>
        // Gera o QR Code apontando para a ficha da peça.
        new QRCode(document.getElementById('qrcode'), {
            text: @json($urlFicha),
            width: 98,
            height: 98,
            correctLevel: QRCode.CorrectLevel.M
        });
    </script>
</body>
</html>
