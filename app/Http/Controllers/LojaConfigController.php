<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Configurações da própria loja (dados + identidade visual/logo).
 * Só o admin da loja (middleware 'adminloja'). Cada admin edita a SUA loja.
 */
class LojaConfigController extends Controller
{
    public function edit(Request $request)
    {
        $loja = $request->user()->loja;
        return view('loja.config', compact('loja'));
    }

    public function update(Request $request)
    {
        $loja = $request->user()->loja;

        $dados = $request->validate([
            'nome_fantasia' => ['required', 'string', 'max:150'],
            'razao_social' => ['nullable', 'string', 'max:150'],
            'cnpj_cpf' => ['nullable', 'string', 'max:20'],
            'inscricao_estadual' => ['nullable', 'string', 'max:30'],
            'email_responsavel' => ['nullable', 'email', 'max:150'],
            'telefone' => ['nullable', 'string', 'max:30'],
            'endereco' => ['nullable', 'string', 'max:200'],
            'cidade' => ['nullable', 'string', 'max:100'],
            'estado' => ['nullable', 'string', 'max:2'],
            'cep' => ['nullable', 'string', 'max:12'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
        ]);

        // Upload do logo (substitui o anterior).
        if ($request->hasFile('logo')) {
            if ($loja->logo) {
                Storage::disk('public')->delete($loja->logo);
            }
            $dados['logo'] = $request->file('logo')->store("lojas/{$loja->id}", 'public');
        } else {
            unset($dados['logo']);
        }

        $loja->update($dados);

        return back()->with('success', 'Configurações da loja atualizadas!');
    }

    public function removerLogo(Request $request)
    {
        $loja = $request->user()->loja;
        if ($loja->logo) {
            Storage::disk('public')->delete($loja->logo);
            $loja->update(['logo' => null]);
        }
        return back()->with('success', 'Logo removido.');
    }
}
