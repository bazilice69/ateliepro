<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\License;
use Carbon\Carbon;

class CheckLicense
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Busca a licença ativa cadastrada no banco local
        $license = License::where('ativo', true)->first();

        // Se NÃO houver licença ativa ou se a data de expiração já passou (e não for vitalício)
        if (!$license || ($license->tipo_plano != 'vitalicio' && Carbon::now()->gt($license->data_expiracao))) {
            
            // Se já estiver na rota de licença, deixa passar para não dar loop infinito
            if ($request->routeIs('licenca.tela') || $request->routeIs('licenca.ativar')) {
                return $next($request);
            }
            
            // Bloqueia e redireciona para a tela de ativação
            return redirect()->route('licenca.tela');
        }

        return $next($request);
    }
}