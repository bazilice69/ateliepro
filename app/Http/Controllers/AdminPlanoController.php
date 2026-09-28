<?php

namespace App\Http\Controllers;

use App\Models\Plano;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * CRUD de Planos do SaaS — apenas super_admin (rota sob middleware 'superadmin').
 */
class AdminPlanoController extends Controller
{
    public function index()
    {
        $planos = Plano::orderBy('ordem')->get();
        return view('admin.planos.index', compact('planos'));
    }

    public function create()
    {
        return view('admin.planos.form', ['plano' => new Plano(['meses' => 1, 'ativo' => true])]);
    }

    public function store(Request $request)
    {
        $dados = $this->validar($request);
        Plano::create($dados);

        return redirect()->route('admin.planos.index')->with('success', 'Plano criado com sucesso!');
    }

    public function edit(Plano $plano)
    {
        return view('admin.planos.form', compact('plano'));
    }

    public function update(Request $request, Plano $plano)
    {
        $dados = $this->validar($request, $plano);
        $plano->update($dados);

        return redirect()->route('admin.planos.index')->with('success', 'Plano atualizado!');
    }

    public function destroy(Plano $plano)
    {
        $plano->delete();
        return redirect()->route('admin.planos.index')->with('success', 'Plano removido.');
    }

    private function validar(Request $request, ?Plano $plano = null): array
    {
        $slugRule = 'unique:planos,slug' . ($plano ? ',' . $plano->id : '');

        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:100', $slugRule],
            'preco' => ['required', 'numeric', 'min:0'],
            'meses' => ['required', 'integer', 'min:1'],
            'periodo_label' => ['nullable', 'string', 'max:20'],
            'recursos' => ['nullable', 'string'], // textarea: um recurso por linha
            'destaque' => ['nullable', 'boolean'],
            'ativo' => ['nullable', 'boolean'],
            'ordem' => ['nullable', 'integer', 'min:0'],
        ]);

        // slug automático a partir do nome, se não informado
        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['nome']);

        // recursos: converte o textarea (uma linha por recurso) em array
        $validated['recursos'] = collect(explode("\n", (string) $request->input('recursos')))
            ->map(fn ($l) => trim($l))
            ->filter()
            ->values()
            ->all();

        $validated['destaque'] = $request->boolean('destaque');
        $validated['ativo'] = $request->boolean('ativo');
        $validated['ordem'] = $validated['ordem'] ?? 0;

        return $validated;
    }
}
