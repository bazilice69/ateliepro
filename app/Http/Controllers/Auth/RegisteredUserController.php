<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Loja;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * Cadastro = onboarding de uma nova LOJA no SaaS. Cria a loja e o seu
     * primeiro usuário (admin_loja) de forma atômica.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nome_fantasia' => ['required', 'string', 'max:255'],
            'cnpj_cpf' => ['required', 'string', 'max:20', 'unique:lojas,cnpj_cpf'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = DB::transaction(function () use ($request) {
            $loja = Loja::create([
                'chave_licenca' => (string) Str::uuid(),
                'nome_fantasia' => $request->nome_fantasia,
                'cnpj_cpf' => $request->cnpj_cpf,
                'email_responsavel' => $request->email,
                'telefone' => $request->telefone,
                'status' => Loja::STATUS_ATIVO,
            ]);

            return User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'loja_id' => $loja->id,
                'role' => User::ROLE_ADMIN_LOJA,
            ]);
        });

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
