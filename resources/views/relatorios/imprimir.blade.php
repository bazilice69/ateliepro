<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Relatório - {{ $loja->nome_fantasia ?? 'AteliêPro' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print { .no-print { display: none !important; } @page { margin: 1.5cm; } }
        body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    </style>
</head>
<body class="bg-white text-slate-800 p-8" onload="window.print()">

    <div class="max-w-4xl mx-auto">
        <!-- Cabeçalho -->
        <div class="flex justify-between items-start border-b-2 border-slate-900 pb-4 mb-6">
            <div>
                <h1 class="text-2xl font-black tracking-tighter">ATELIÊ<span class="text-amber-500">PRO</span></h1>
                <p class="text-sm text-slate-500">{{ $loja->nome_fantasia ?? '' }}</p>
            </div>
            <div class="text-right text-sm">
                <p class="font-black uppercase tracking-widest text-xs text-slate-400">Relatório</p>
                <p class="text-slate-600">{{ $inicio->format('d/m/Y') }} a {{ $fim->format('d/m/Y') }}</p>
                <p class="text-slate-400 text-xs">Emitido em {{ now()->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        <!-- Resumo financeiro -->
        <h2 class="font-black uppercase text-sm tracking-wider mb-3">Resumo Financeiro</h2>
        <div class="grid grid-cols-4 gap-3 mb-8 text-center">
            <div class="border border-slate-200 rounded-lg p-3">
                <p class="text-[10px] uppercase text-slate-400 font-bold">Receitas</p>
                <p class="font-black text-emerald-600">R$ {{ number_format($financeiro['receitas'], 2, ',', '.') }}</p>
            </div>
            <div class="border border-slate-200 rounded-lg p-3">
                <p class="text-[10px] uppercase text-slate-400 font-bold">Despesas</p>
                <p class="font-black text-rose-500">R$ {{ number_format($financeiro['despesas'], 2, ',', '.') }}</p>
            </div>
            <div class="border border-slate-900 bg-slate-900 text-white rounded-lg p-3">
                <p class="text-[10px] uppercase text-amber-400 font-bold">Saldo</p>
                <p class="font-black">R$ {{ number_format($financeiro['saldo'], 2, ',', '.') }}</p>
            </div>
            <div class="border border-slate-200 rounded-lg p-3">
                <p class="text-[10px] uppercase text-slate-400 font-bold">Em atraso</p>
                <p class="font-black text-amber-600">R$ {{ number_format($financeiro['emAtraso'], 2, ',', '.') }}</p>
            </div>
        </div>

        <!-- Operacional -->
        <h2 class="font-black uppercase text-sm tracking-wider mb-3">Operacional</h2>
        <p class="text-sm mb-6">Locações no período: <strong>{{ $operacional['locacoes'] }}</strong> · Novos clientes: <strong>{{ $operacional['novos_clientes'] }}</strong></p>

        <!-- Lançamentos -->
        <h2 class="font-black uppercase text-sm tracking-wider mb-3">Lançamentos</h2>
        <table class="w-full text-left text-xs border-collapse">
            <thead>
                <tr class="border-b-2 border-slate-300 text-slate-500 uppercase">
                    <th class="py-2">Data</th><th class="py-2">Descrição</th><th class="py-2">Categoria</th><th class="py-2">Status</th><th class="py-2 text-right">Valor</th>
                </tr>
            </thead>
            <tbody>
                @forelse($lancamentos as $l)
                    <tr class="border-b border-slate-100">
                        <td class="py-1.5">{{ optional($l->data_vencimento)->format('d/m/Y') }}</td>
                        <td class="py-1.5">{{ $l->descricao }}</td>
                        <td class="py-1.5">{{ $l->categoria->nome ?? '—' }}</td>
                        <td class="py-1.5">{{ $l->status }}</td>
                        <td class="py-1.5 text-right font-bold {{ $l->tipo === 'Receita' ? 'text-emerald-600' : 'text-rose-500' }}">{{ $l->tipo === 'Receita' ? '+' : '−' }} R$ {{ number_format((float)$l->valor_final, 2, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-4 text-center text-slate-400">Sem lançamentos no período.</td></tr>
                @endforelse
            </tbody>
        </table>

        <button onclick="window.print()" class="no-print mt-8 bg-slate-900 text-amber-400 px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest">Imprimir / Salvar PDF</button>
    </div>
</body>
</html>
