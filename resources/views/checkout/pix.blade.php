<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pagamento da Assinatura - AteliêPro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 font-sans flex items-center justify-center min-h-screen p-4">

    <div class="bg-white rounded-3xl p-8 shadow-xl border border-slate-100 max-w-md w-full text-center">
        
        <div class="text-2xl font-black text-slate-900 tracking-tighter flex items-center justify-center mb-8">
            <i class="fas fa-cut mr-2 text-amber-500"></i> Ateliê<span class="text-emerald-600">Pro</span>
        </div>

        <h2 class="text-xl font-black text-slate-800 uppercase tracking-tighter mb-2">Quase lá!</h2>
        <p class="text-sm text-slate-500 font-medium mb-6">Escaneie o QR Code abaixo para liberar seu acesso.</p>

        <!-- Imagem do QR Code gerada pelo Mercado Pago -->
        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 inline-block mb-6 shadow-inner">
            <img src="data:image/jpeg;base64,{{ $qrCodeBase64 }}" alt="QR Code PIX" class="w-48 h-48 mx-auto">
        </div>

        <div class="text-3xl font-black text-slate-900 mb-6">
            R$ {{ number_format($valorPlano, 2, ',', '.') }}
        </div>

        <div class="text-left mb-6">
            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">PIX Copia e Cola</label>
            <div class="flex gap-2">
                <input type="text" id="pixCopiaECola" value="{{ $copiaECola }}" readonly class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold text-slate-500 text-xs focus:outline-none">
                <button onclick="copiarPix()" class="bg-slate-900 hover:bg-slate-800 text-white px-4 py-3 rounded-xl font-black transition">
                    <i class="fas fa-copy"></i>
                </button>
            </div>
        </div>

        <p class="text-xs text-slate-400 mb-6">Seu acesso será liberado automaticamente em alguns segundos após o pagamento.</p>

        <!-- Este botão no futuro vai ficar consultando se já foi pago -->
        <button class="w-full bg-emerald-500 text-white font-black py-4 rounded-xl transition uppercase tracking-widest text-xs shadow-lg opacity-50 cursor-not-allowed">
            <i class="fas fa-spinner fa-spin mr-2"></i> Aguardando Pagamento...
        </button>

    </div>

    <script>
        function copiarPix() {
            var copyText = document.getElementById("pixCopiaECola");
            copyText.select();
            copyText.setSelectionRange(0, 99999); // Para mobile
            navigator.clipboard.writeText(copyText.value);
            alert("Chave Copiada!");
        }
    </script>
</body>
</html>