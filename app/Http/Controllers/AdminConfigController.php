<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

/**
 * Configurações globais do SaaS (super_admin): identidade e Mercado Pago.
 */
class AdminConfigController extends Controller
{
    /**
     * Chaves de configuração gerenciadas nesta tela.
     */
    private array $chaves = [
        'saas_nome',
        'saas_email_suporte',
        'saas_whatsapp_suporte',
        'mercadopago_access_token',
        'mercadopago_public_key',
        'mercadopago_webhook_secret',
        'ia_provedor',
        'ia_api_key',
        'ia_modelo',
        'ia_nome_assistente',
    ];

    public function edit()
    {
        $config = [];
        foreach ($this->chaves as $chave) {
            $config[$chave] = Setting::get($chave);
        }

        return view('admin.config', compact('config'));
    }

    public function update(Request $request)
    {
        $dados = $request->validate([
            'saas_nome' => ['nullable', 'string', 'max:100'],
            'saas_email_suporte' => ['nullable', 'email', 'max:150'],
            'saas_whatsapp_suporte' => ['nullable', 'string', 'max:30'],
            'mercadopago_access_token' => ['nullable', 'string', 'max:255'],
            'mercadopago_public_key' => ['nullable', 'string', 'max:255'],
            'mercadopago_webhook_secret' => ['nullable', 'string', 'max:255'],
            'ia_provedor' => ['nullable', 'in:openai,anthropic,gemini'],
            'ia_api_key' => ['nullable', 'string', 'max:255'],
            'ia_modelo' => ['nullable', 'string', 'max:100'],
            'ia_nome_assistente' => ['nullable', 'string', 'max:40'],
        ]);

        foreach ($this->chaves as $chave) {
            // Não sobrescreve segredos com vazio (permite manter o valor atual
            // ao deixar o campo em branco).
            if (array_key_exists($chave, $dados) && $dados[$chave] !== null && $dados[$chave] !== '') {
                Setting::set($chave, $dados[$chave]);
            }
        }

        return back()->with('success', 'Configurações salvas com sucesso!');
    }
}
