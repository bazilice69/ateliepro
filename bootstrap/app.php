<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Em produção o app roda atrás de um proxy (Render/hospedagem) com HTTPS.
        // Confiar no proxy garante que URLs/assets sejam gerados com https://.
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            // Legado (licença por máquina) — mantido, mas fora do fluxo padrão.
            'license' => \App\Http\Middleware\CheckLicense::class,
            // Multi-tenancy / SaaS:
            'lojaativa' => \App\Http\Middleware\EnsureLojaAtiva::class,
            'superadmin' => \App\Http\Middleware\EnsureSuperAdmin::class,
            'adminloja' => \App\Http\Middleware\EnsureAdminLoja::class,
        ]);

        // O webhook do Mercado Pago vem de fora (sem sessão/CSRF).
        $middleware->validateCsrfTokens(except: [
            'webhooks/mercadopago',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();