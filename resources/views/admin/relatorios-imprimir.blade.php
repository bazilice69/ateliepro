<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Relatório do SaaS - AteliêPro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print { .no-print { display: none !important; } @page { margin: 1.5cm; } }
        body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    </style>
</head>
<body class="bg-white text-slate-800 p-8" onload="window.print()">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-start border-b-2 border-slate-900 pb-4 mb-6">
            <h1 class="text-2xl font-black tracking-tighter">ATELIÊ<span class="text-amber-500">PRO</span> <span class="text-sm font-medium text-slate-400">· Painel Master</span></h1>
            <div class="text-right text-sm text-slate-500">Emitido em {{ now()->format('d/m/Y H:i') }}</div>
        </div>

        <div class="grid grid-cols-4 gap-3 mb-8 text-center">
            <div class="border border-slate-900 bg-slate-900 text-white rounded-lg p-3"><p class="text-[10px] uppercase text-amber-400 font-bold">MRR</p><p class="font-black">R$ {{ number_format($mrr, 2, ',', '.') }}</p></div>
            <div class="border border-slate-200 rounded-lg p-3"><p class="text-[10px] uppercase text-slate-400 font-bold">Ativas</p><p class="font-black text-emerald-600">{{ $porStatus['ativo'] }}</p></div>
            <div class="border border-slate-200 rounded-lg p-3"><p class="text-[10px] uppercase text-slate-400 font-bold">Inadimplentes</p><p class="font-black text-amber-600">{{ $porStatus['inadimplente'] }}</p></div>
            <div class="border border-slate-200 rounded-lg p-3"><p class="text-[10px] uppercase text-slate-400 font-bold">Bloqueadas</p><p class="font-black text-rose-500">{{ $porStatus['bloqueado'] }}</p></div>
        </div>

        <h2 class="font-black uppercase text-sm tracking-wider mb-3">Lojas</h2>
        <table class="w-full text-left text-xs border-collapse">
            <thead><tr class="border-b-2 border-slate-300 text-slate-500 uppercase">
                <th class="py-2">Loja</th><th class="py-2">Plano</th><th class="py-2 text-right">Valor</th><th class="py-2">Status</th><th class="py-2">Vencimento</th>
            </tr></thead>
            <tbody>
                @foreach($lojas as $l)
                    <tr class="border-b border-slate-100">
                        <td class="py-1.5">{{ $l->nome_fantasia }}</td>
                        <td class="py-1.5">{{ $l->plano }}</td>
                        <td class="py-1.5 text-right font-bold">R$ {{ number_format($l->valorEfetivo(), 2, ',', '.') }}</td>
                        <td class="py-1.5">{{ $l->status }}</td>
                        <td class="py-1.5">{{ optional($l->data_vencimento)->format('d/m/Y') ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <button onclick="window.print()" class="no-print mt-8 bg-slate-900 text-amber-400 px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest">Imprimir / Salvar PDF</button>
    </div>
</body>
</html>
