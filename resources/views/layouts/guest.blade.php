<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ \App\Models\Setting::nomeSistema() }} - Acesso</title>

        <!-- Atalho mágico do Tailwind para garantir o design -->
        <script src="https://cdn.tailwindcss.com"></script>
        
        <!-- Ícones -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- Alpine.js (interatividade). CSS/JS via CDN acima; não usamos Vite em produção. -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&display=swap" rel="stylesheet">
        <style>.serif{font-family:'Cormorant Garamond',serif;}</style>
    </head>
    <body class="font-sans text-slate-900 antialiased bg-[#0b0b0d]">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative">
            
            <!-- Imagem de fundo sutil e elegante (alta resolução) -->
            <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1594552072238-b8a33785b261?q=80&w=2400&auto=format&fit=crop')] bg-cover bg-center opacity-25"></div>
            <!-- escurecimento para contraste -->
            <div class="absolute inset-0 bg-gradient-to-b from-[#0b0b0d]/70 via-[#0b0b0d]/60 to-[#0b0b0d]/80"></div>
            <!-- brilho dourado -->
            <div class="absolute inset-0 pointer-events-none"><div class="absolute top-1/4 left-1/2 -translate-x-1/2 w-[500px] h-[500px] rounded-full blur-[150px] opacity-20" style="background:radial-gradient(circle,#c9a24b,transparent 70%);"></div></div>
            
            <!-- Cartão Flutuante onde vai entrar o Formulário -->
            <div class="relative z-10 w-full sm:max-w-md mt-6 px-10 py-12 bg-white shadow-[0_20px_60px_rgba(0,0,0,0.6)] overflow-hidden sm:rounded-[2.5rem] border-t-4 border-[#c9a24b]">
                
                <!-- Logo e Cabeçalho do Cartão -->
                <div class="flex flex-col items-center justify-center mb-8">
                    <a href="/" class="flex flex-col items-center gap-2 hover:scale-105 transition-transform">
                        <span class="w-14 h-14 rounded-full border-2 border-[#c9a24b] flex items-center justify-center text-[#c9a24b] serif text-3xl font-bold">B</span>
                        <span class="serif text-3xl font-bold tracking-wide text-slate-900">{{ \App\Models\Setting::nomeSistema() }}</span>
                    </a>
                    <div class="h-[2px] w-12 bg-[#c9a24b] rounded-full my-3"></div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.25em]">Painel de Acesso</p>
                </div>

                <!-- O Laravel injeta os campos de E-mail e Senha automaticamente aqui dentro -->
                <div class="w-full">
                    {{ $slot }}
                </div>
                
            </div>
            
            <!-- Rodapé discreto -->
            <div class="relative z-10 mt-8 text-center">
                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">
                    &copy; {{ date('Y') }} {{ \App\Models\Setting::nomeSistema() }}.
                </p>
            </div>
            
        </div>
    </body>
</html>