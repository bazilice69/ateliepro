<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Garante que a rota só seja acessada pelo dono da loja (admin_loja) ou pelo
 * super_admin. Usado, por exemplo, na gestão de funcionários.
 */
class EnsureAdminLoja
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !($user->isAdminLoja() || $user->isSuperAdmin())) {
            abort(403, 'Apenas o administrador da loja pode acessar esta área.');
        }

        return $next($request);
    }
}
