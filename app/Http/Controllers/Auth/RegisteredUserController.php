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
            'telefone' => ['nullable', 'string', 'max:30'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            // Mensagens claras para os casos de "já cadastrado" (orientam o lojista).
            'cnpj_cpf.unique' => 'Este CNPJ/CPF já está cadastrado. Se a loja é sua, faça login em vez de criar outra conta.',
            'email.unique' => 'Este e-mail já está cadastrado. Faça login ou use a opção "Esqueci minha senha".',
            'email.lowercase' => 'Digite o e-mail todo em letras minúsculas.',
            'password.confirmed' => 'A confirmação de senha não confere.',
        ]);

        [$user, $loja] = DB::transaction(function () use ($request) {
            $loja = Loja::create([
                'chave_licenca' => (string) Str::uuid(),
                'nome_fantasia' => $request->nome_fantasia,
                'cnpj_cpf' => $request->cnpj_cpf,
                'email_responsavel' => $request->email,
                'telefone' => $request->telefone,
                'status' => Loja::STATUS_ATIVO,
                // Teste grátis de 7 dias — sem cobrança na entrada.
                'trial_termina_em' => now()->addDays(Loja::DIAS_TRIAL),
                'plano' => 'Trial',
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'loja_id' => $loja->id,
                'role' => User::ROLE_ADMIN_LOJA,
            ]);

            return [$user, $loja];
        });

        // Registra o novo cadastro para o super_admin ver no painel.
        \App\Models\AccessLog::create([
            'user_id' => $user->id,
            'loja_id' => $loja->id,
            'user_nome' => $user->name,
            'loja_nome' => $loja->nome_fantasia,
            'evento' => 'nova_loja',
            'ip' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ]);

        // Notifica o super_admin (só envia de fato se o e-mail estiver configurado).
        \App\Support\Notificador::novaLoja($loja);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
