<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

/**
 * Gestão de usuários (funcionários) DA PRÓPRIA LOJA, feita pelo admin_loja.
 *
 * Todas as operações são estritamente escopadas à loja do usuário logado —
 * um admin nunca gerencia usuários de outra loja (403/404).
 */
class FuncionarioController extends Controller
{
    public function index()
    {
        $lojaId = Auth::user()->loja_id;

        $funcionarios = User::where('loja_id', $lojaId)
            ->orderByRaw("CASE WHEN role = 'admin_loja' THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->get();

        return view('funcionarios.index', compact('funcionarios'));
    }

    public function create()
    {
        return view('funcionarios.create');
    }

    public function store(Request $request)
    {
        $lojaId = Auth::user()->loja_id;

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in([User::ROLE_ADMIN_LOJA, User::ROLE_FUNCIONARIO])],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'loja_id' => $lojaId, // sempre a loja do admin logado
        ]);

        return redirect()->route('funcionarios.index')->with('success', 'Funcionário cadastrado com sucesso!');
    }

    public function edit(User $user)
    {
        $this->autorizarMesmaLoja($user);
        return view('funcionarios.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $this->autorizarMesmaLoja($user);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in([User::ROLE_ADMIN_LOJA, User::ROLE_FUNCIONARIO])],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return redirect()->route('funcionarios.index')->with('success', 'Funcionário atualizado!');
    }

    public function destroy(User $user)
    {
        $this->autorizarMesmaLoja($user);

        // Não permite excluir a si mesmo.
        if ($user->id === Auth::id()) {
            return back()->withErrors(['erro' => 'Você não pode excluir o seu próprio usuário.']);
        }

        $user->delete();

        return redirect()->route('funcionarios.index')->with('success', 'Funcionário removido.');
    }

    /**
     * Garante que o usuário-alvo pertence à mesma loja do admin logado.
     */
    private function autorizarMesmaLoja(User $user): void
    {
        if ((int) $user->loja_id !== (int) Auth::user()->loja_id) {
            abort(404);
        }
    }
}
