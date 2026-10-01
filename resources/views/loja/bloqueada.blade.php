<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ \App\Models\Setting::nomeSistema() }} — Acesso Suspenso</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#0a0f1d] min-h-screen flex items-center justify-center p-6">
    <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-10 text-center">
        <div class="w-16 h-16 mx-auto rounded-full bg-amber-100 flex items-center justify-center mb-6">
            <span class="text-3xl">🔒</span>
        </div>

        <h1 class="text-2xl font-black text-slate-800">ATELIÊ<span class="text-[#fbbf24]">PRO</span></h1>

        @php $status = $loja?->status; @endphp

        @if($status === \App\Models\Loja::STATUS_INADIMPLENTE)
            <p class="mt-4 text-lg font-bold text-slate-700">Assinatura pendente</p>
            <p class="mt-2 text-sm text-slate-500">
                A assinatura da loja <strong>{{ $loja?->nome_fantasia }}</strong> está em aberto.
                Regularize o pagamento para reativar o acesso ao sistema.
            </p>
        @elseif($status === \App\Models\Loja::STATUS_BLOQUEADO)
            <p class="mt-4 text-lg font-bold text-slate-700">Acesso bloqueado</p>
            <p class="mt-2 text-sm text-slate-500">
                O acesso da loja <strong>{{ $loja?->nome_fantasia }}</strong> foi suspenso.
                Entre em contato com o suporte para mais informações.
            </p>
        @else
            <p class="mt-4 text-lg font-bold text-slate-700">Acesso indisponível</p>
            <p class="mt-2 text-sm text-slate-500">
                Sua conta ainda não está vinculada a uma loja ativa.
                Entre em contato com o suporte.
            </p>
        @endif

        @php
            $waSuporte = preg_replace('/\D/', '', \App\Models\Setting::get('saas_whatsapp_suporte', '5511957866836'));
            $msgSuporte = urlencode('Olá! Sou da loja ' . ($loja?->nome_fantasia ?? '') . ' e preciso de ajuda com o acesso ao ' . \App\Models\Setting::nomeSistema() . '.');
        @endphp
        <div class="mt-8 space-y-3">
            <a href="https://wa.me/{{ $waSuporte }}?text={{ $msgSuporte }}" target="_blank" class="block w-full bg-[#fbbf24] hover:bg-yellow-500 text-slate-900 font-black py-3 rounded-xl text-sm uppercase tracking-wide transition">
                <i class="fab fa-whatsapp mr-1"></i> Falar com o Suporte
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-xs text-slate-400 hover:text-slate-600 font-bold uppercase tracking-widest">
                    Sair
                </button>
            </form>
        </div>
    </div>
</body>
</html>
