<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>AteliêPro - Acesso</title>

        <!-- Atalho mágico do Tailwind para garantir o design -->
        <script src="https://cdn.tailwindcss.com"></script>
        
        <!-- Ícones -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- Alpine.js (interatividade). CSS/JS via CDN acima; não usamos Vite em produção. -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </head>
    <body class="font-sans text-slate-900 antialiased bg-slate-900">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative">
            
            <!-- Imagem de fundo sutil e elegante -->
            <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1595777457583-95e059d581b8?q=80&w=2000&auto=format&fit=crop')] bg-cover bg-center opacity-20"></div>
            
            <!-- Cartão Branco Flutuante onde vai entrar o Formulário -->
            <div class="relative z-10 w-full sm:max-w-md mt-6 px-10 py-12 bg-white shadow-[0_20px_50px_rgba(0,0,0,0.5)] overflow-hidden sm:rounded-[2.5rem]">
                
                <!-- Logo e Cabeçalho do Cartão -->
                <div class="flex flex-col items-center justify-center mb-8">
                    <a href="/" class="text-4xl font-light tracking-tighter text-slate-900 mb-1 hover:scale-105 transition-transform">
                        Ateliê<span class="font-black text-[#fbbf24]">Pro</span>
                    </a>
                    <div class="h-1 w-12 bg-[#fbbf24] rounded-full mb-2"></div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Painel de Acesso</p>
                </div>

                <!-- O Laravel injeta os campos de E-mail e Senha automaticamente aqui dentro -->
                <div class="w-full">
                    {{ $slot }}
                </div>
                
            </div>
            
            <!-- Rodapé discreto -->
            <div class="relative z-10 mt-8 text-center">
                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">
                    &copy; {{ date('Y') }} AteliêPro.
                </p>
            </div>
            
        </div>
    </body>
</html>