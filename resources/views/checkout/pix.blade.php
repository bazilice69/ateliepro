<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pagamento da Assinatura - {{ \App\Models\Setting::nomeSistema() }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 font-sans flex items-center justify-center min-h-screen p-4">

    <div class="bg-white rounded-3xl p-8 shadow-xl border border-slate-100 max-w-md w-full text-center">

        <div class="text-2xl font-black text-slate-900 tracking-tighter flex items-center justify-center mb-6">
            <i class="fas fa-cut mr-2 text-amber-500"></i> {{ \App\Models\Setting::nomeSistema() }}
        </div>

        <p class="text-[10px] uppercase tracking-widest text-slate-400 font-black">Plano {{ $plano->nome }} — {{ $loja->nome_fantasia }}</p>
        <h2 class="text-xl font-black text-slate-800 uppercase tracking-tighter mt-1 mb-2">Quase lá!</h2>
        <p class="text-sm text-slate-500 font-medium mb-6">Escaneie o QR Code para liberar seu acesso.</p>

        @if($qrCodeBase64)
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 inline-block mb-6 shadow-inner">
                <img src="data:image/jpeg;base64,{{ $qrCodeBase64 }}" alt="QR Code PIX" class="w-48 h-48 mx-auto">
            </div>
        @endif

        <div class="text-3xl font-black text-slate-900 mb-6">
            R$ {{ number_format($plano->preco, 2, ',', '.') }}
        </div>

        @if($copiaECola)
            <div class="text-left mb-6">
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">PIX Copia e Cola</label>
                <div class="flex gap-2">
                    <input type="text" id="pixCopiaECola" value="{{ $copiaECola }}" readonly class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold text-slate-500 text-xs focus:outline-none">
                    <button onclick="copiarPix()" class="bg-slate-900 hover:bg-slate-800 text-white px-4 py-3 rounded-xl font-black transition">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
            </div>
        @endif

        <div id="statusBox" class="w-full bg-amber-50 text-amber-700 font-bold py-4 rounded-xl text-xs uppercase tracking-widest">
            <i class="fas fa-spinner fa-spin mr-2"></i> Aguardando pagamento...
        </div>

        <p class="text-xs text-slate-400 mt-4">Seu acesso é liberado automaticamente após a confirmação.</p>

        {{-- Selos de confiança --}}
        @include('checkout._selos-confianca')
    </div>

    <script>
        function copiarPix() {
            var t = document.getElementById("pixCopiaECola");
            t.select(); t.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(t.value);
            alert("Chave PIX copiada!");
        }

        // Verifica a cada 5s se o pagamento foi confirmado (webhook ativa a loja).
        const statusUrl = "{{ route('checkout.status', $assinatura) }}";
        const box = document.getElementById('statusBox');
        setInterval(async () => {
            try {
                const r = await fetch(statusUrl);
                const data = await r.json();
                if (data.status === 'paga') {
                    box.className = 'w-full bg-emerald-500 text-white font-bold py-4 rounded-xl text-xs uppercase tracking-widest';
                    box.innerHTML = '<i class="fas fa-check mr-2"></i> Pagamento confirmado! Redirecionando...';
                    setTimeout(() => window.location.href = "{{ route('dashboard') }}", 1500);
                }
            } catch (e) {}
        }, 5000);
    </script>
</body>
</html>
