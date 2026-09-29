<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\AccessLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();

        // Registra o rastro de acesso (última entrada + contador).
        $user->forceFill([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
            'total_acessos' => (int) $user->total_acessos + 1,
        ])->save();

        // Log de acesso (com snapshot de nome/loja para a área de Atividade).
        AccessLog::create([
            'user_id' => $user->id,
            'loja_id' => $user->loja_id,
            'user_nome' => $user->name,
            'loja_nome' => $user->loja?->nome_fantasia,
            'evento' => 'login',
            'ip' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ]);

        // Redirecionamento por papel: super_admin vai para o painel do SaaS.
        if ($user->isSuperAdmin()) {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
