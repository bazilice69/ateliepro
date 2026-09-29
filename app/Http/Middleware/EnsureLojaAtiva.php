<?php

namespace App\Http\Middleware;

use App\Models\Loja;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Verificação de acesso da LOJA (substitui a antiga licença-por-máquina).
 *
 * No modelo SaaS a assinatura pertence à LOJA, não ao computador. Este
 * middleware libera o acesso quando a loja do usuário está ATIVA e bloqueia
 * (redireciona para a tela de aviso) quando está inadimplente ou bloqueada.
 *
 * O super_admin é sempre liberado (não tem loja e gerencia o SaaS).
 */
class EnsureLojaAtiva
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // super_admin não depende de loja.
        if ($user && $user->isSuperAdmin()) {
            return $next($request);
        }

        $loja = $user?->loja;

        // Sem loja associada: manda para a tela de aviso.
        if (!$loja) {
            return redirect()->route('loja.bloqueada');
        }

        // Loja não-ativa (inadimplente/bloqueada): bloqueia o acesso ao sistema.
        if ($loja->status !== Loja::STATUS_ATIVO) {
            return redirect()->route('loja.bloqueada');
        }

        return $next($request);
    }
}
