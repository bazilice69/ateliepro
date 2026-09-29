<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Garante que apenas o super_admin (dono do SaaS) acesse o painel /admin.
 * Qualquer outro papel recebe 403.
 */
class EnsureSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->isSuperAdmin()) {
            abort(403, 'Acesso restrito ao administrador do sistema.');
        }

        return $next($request);
    }
}
