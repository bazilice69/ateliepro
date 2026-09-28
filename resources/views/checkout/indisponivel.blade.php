<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Assinatura - AteliêPro</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen p-4">
    <div class="bg-white rounded-3xl p-10 shadow-xl border border-slate-100 max-w-md w-full text-center">
        <div class="text-4xl mb-4">🛠️</div>
        <h2 class="text-xl font-black text-slate-800">Pagamento em configuração</h2>
        <p class="text-sm text-slate-500 mt-2">
            O plano <strong>{{ $plano->nome }}</strong> (R$ {{ number_format($plano->preco, 2, ',', '.') }})
            está selecionado, mas o pagamento online ainda não foi ativado pelo administrador.
        </p>
        <p class="text-xs text-slate-400 mt-4">Entre em contato com o suporte para concluir a assinatura.</p>
        <a href="{{ url('/') }}" class="inline-block mt-6 bg-slate-900 text-[#fbbf24] px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest">Voltar</a>
    </div>
</body>
</html>
