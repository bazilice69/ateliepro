<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>{{ $contrato->titulo }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print { .no-print { display: none !important; } @page { margin: 2cm; } }
        body { -webkit-print-color-adjust: exact; }
    </style>
</head>
<body class="bg-white text-slate-800 p-10" onload="window.print()">
    <div class="max-w-3xl mx-auto">
        <pre class="whitespace-pre-wrap font-serif text-[15px] leading-relaxed text-slate-900">{{ $contrato->conteudo_final }}</pre>

        <button onclick="window.print()" class="no-print mt-10 bg-slate-900 text-amber-400 px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest">Imprimir / Salvar PDF</button>
    </div>
</body>
</html>
