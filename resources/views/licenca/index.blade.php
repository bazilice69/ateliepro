<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AteliêPro - Licenciamento Expirado</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#0f172a] font-sans flex items-center justify-center h-screen m-0">

    <div class="bg-white rounded-[3rem] p-10 max-w-lg w-full shadow-2xl border border-slate-800 text-center relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-amber-400 to-amber-600"></div>
        
        <div class="w-20 h-20 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-6 text-amber-500 text-3xl shadow-inner">
            <i class="fas fa-lock"></i>
        </div>

        <h1 class="text-3xl font-black text-slate-900 tracking-tighter uppercase mb-2">Ateliê<span class="text-[#fbbf24]">Pro</span></h1>
        <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mb-6">Acesso Expirado ou Não Ativado</p>

        <!-- CAIXA COM O ID ÚNICO DA MÁQUINA DO CLIENTE -->
        <div class="mb-6 p-4 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-mono text-slate-700">
            <span class="block text-[10px] text-slate-400 uppercase tracking-widest mb-1 font-bold">ID Identificador da Máquina</span>
            <strong class="text-slate-900 text-sm tracking-wider">{{ $machineId }}</strong>
        </div>

        <p class="text-sm text-slate-600 mb-6 leading-relaxed">
            Informe o ID acima ao suporte para gerar sua chave exclusiva de desbloqueio para este computador.
        </p>

        @if($errors->any())
            <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-600 rounded-2xl text-xs font-bold uppercase tracking-wider">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('licenca.ativar') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <input type="text" name="chave_licenca" placeholder="EX: ATELIEPRO-{{ $machineId }}-MENSAL-XXXX" class="w-full p-4 bg-slate-50 border border-slate-200 rounded-2xl text-center font-black text-slate-800 tracking-wider uppercase focus:ring-2 focus:ring-[#fbbf24] focus:outline-none text-xs" required>
            </div>

            <button type="submit" class="w-full bg-slate-900 text-[#fbbf24] py-4 rounded-2xl font-black text-sm uppercase tracking-wider hover:bg-slate-800 transition shadow-xl cursor-pointer">
                ATIVAR SISTEMA AGORA
            </button>
        </form>

        <div class="mt-8 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-t border-slate-100 pt-4">
            Suporte Técnico • Controle Offline Seguro
        </div>
    </div>

</body>
</html>