<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Assinatura - {{ \App\Models\Setting::nomeSistema() }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen p-4">
    @php
        $waSuporte = preg_replace('/\D/', '', \App\Models\Setting::get('saas_whatsapp_suporte', '5511957866836'));
        $msg = urlencode('Olá! Quero assinar o plano ' . $plano->nome . ' do ' . \App\Models\Setting::nomeSistema() . '.');
    @endphp
    <div class="bg-white rounded-[2rem] p-8 md:p-10 shadow-xl border border-slate-100 max-w-lg w-full text-center">

        <div class="w-16 h-16 mx-auto rounded-2xl bg-slate-900 text-[#fbbf24] flex items-center justify-center text-2xl mb-5">
            <i class="fas fa-lock"></i>
        </div>

        <h2 class="text-2xl font-black text-slate-800 tracking-tight">Finalize sua assinatura</h2>
        <p class="text-sm text-slate-500 mt-2">
            Plano <strong>{{ $plano->nome }}</strong> —
            <strong class="text-slate-800">R$ {{ number_format($plano->preco, 2, ',', '.') }}</strong>
        </p>

        {{-- CTA principal: concluir com o suporte (WhatsApp) --}}
        <a href="https://wa.me/{{ $waSuporte }}?text={{ $msg }}" target="_blank"
           class="mt-6 inline-flex items-center justify-center gap-2 w-full bg-emerald-500 hover:bg-emerald-400 text-white px-6 py-4 rounded-2xl font-black text-sm uppercase tracking-widest transition shadow-lg">
            <i class="fab fa-whatsapp text-lg"></i> Concluir pelo WhatsApp
        </a>
        <p class="text-xs text-slate-400 mt-3">Pagamento online automático em breve. Por enquanto, nossa equipe conclui sua assinatura com você em minutos.</p>

        {{-- Selos de confiança --}}
        @include('checkout._selos-confianca')

        <a href="{{ url('/') }}" class="inline-block mt-8 text-slate-400 hover:text-slate-600 font-bold text-xs uppercase tracking-widest">&larr; Voltar</a>
    </div>
</body>
</html>
