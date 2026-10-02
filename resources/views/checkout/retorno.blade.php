<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Confirmação do Pagamento - {{ \App\Models\Setting::nomeSistema() }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 font-sans flex items-center justify-center min-h-screen p-4">

    <div class="bg-white rounded-[2rem] p-8 shadow-xl border border-slate-100 max-w-md w-full text-center">

        <div class="text-2xl font-black text-slate-900 tracking-tighter flex items-center justify-center mb-6">
            <i class="fas fa-cut mr-2 text-amber-500"></i> {{ \App\Models\Setting::nomeSistema() }}
        </div>

        <p class="text-[10px] uppercase tracking-widest text-slate-400 font-black">{{ $loja->nome_fantasia ?? '' }}</p>

        {{-- Já pago? --}}
        @if($assinatura->status === \App\Models\Assinatura::STATUS_PAGA)
            <div class="w-16 h-16 mx-auto rounded-2xl bg-emerald-500 text-white flex items-center justify-center text-2xl my-5">
                <i class="fas fa-check"></i>
            </div>
            <h2 class="text-2xl font-black text-slate-800">Pagamento confirmado! 🎉</h2>
            <p class="text-sm text-slate-500 mt-2">Seu acesso está liberado.</p>
            <a href="{{ route('dashboard') }}" class="mt-6 inline-block bg-slate-900 text-[#fbbf24] px-8 py-3 rounded-xl font-black text-xs uppercase tracking-widest">Ir para o painel</a>
        @else
            <div id="box" class="w-16 h-16 mx-auto rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center text-2xl my-5">
                <i class="fas fa-spinner fa-spin"></i>
            </div>
            <h2 id="titulo" class="text-2xl font-black text-slate-800">Processando pagamento…</h2>
            <p id="msg" class="text-sm text-slate-500 mt-2">
                Assim que o pagamento for confirmado, seu acesso é liberado automaticamente.
                Isso pode levar alguns instantes.
            </p>
            <a href="{{ route('dashboard') }}" class="mt-6 inline-block text-slate-400 hover:text-slate-600 font-bold text-xs uppercase tracking-widest">Ir para o painel</a>
        @endif

        {{-- Selos de confiança --}}
        @include('checkout._selos-confianca')
    </div>

    @if($assinatura->status !== \App\Models\Assinatura::STATUS_PAGA)
    <script>
        // Verifica a cada 5s se o pagamento foi confirmado (webhook ativa a loja).
        const statusUrl = "{{ route('checkout.status', $assinatura) }}";
        const box = document.getElementById('box');
        const titulo = document.getElementById('titulo');
        const msg = document.getElementById('msg');
        const timer = setInterval(async () => {
            try {
                const r = await fetch(statusUrl);
                const data = await r.json();
                if (data.status === 'paga') {
                    clearInterval(timer);
                    box.className = 'w-16 h-16 mx-auto rounded-2xl bg-emerald-500 text-white flex items-center justify-center text-2xl my-5';
                    box.innerHTML = '<i class="fas fa-check"></i>';
                    titulo.textContent = 'Pagamento confirmado! 🎉';
                    msg.textContent = 'Redirecionando para o painel…';
                    setTimeout(() => window.location.href = "{{ route('dashboard') }}", 1500);
                }
            } catch (e) {}
        }, 5000);
    </script>
    @endif
</body>
</html>
