<?php

namespace App\Http\Controllers;

use App\Models\Depoimento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Depoimentos/elogios dos lojistas.
 *
 *  - store():   o lojista envia um elogio pelo widget (fica pendente).
 *  - index():   o super_admin modera (lista pendentes e aprovados).
 *  - aprovar()/reprovar()/destroy(): ações de moderação do super_admin.
 */
class DepoimentoController extends Controller
{
    /**
     * Página "Fale Conosco" (acessível pelo menu lateral da loja): canal único
     * para Problema / Sugestão / Dúvida (WhatsApp) e Elogio (salvo no banco).
     */
    public function faleConosco()
    {
        $waSuporte = preg_replace('/\D/', '', \App\Models\Setting::get('saas_whatsapp_suporte', '5511965578434'));

        return view('fale-conosco', compact('waSuporte'));
    }

    /**
     * Recebe o elogio enviado pelo lojista (via página Fale Conosco).
     * Fica PENDENTE até o super_admin aprovar.
     */
    public function store(Request $request)
    {
        $dados = $request->validate([
            'autor_nome' => ['required', 'string', 'max:120'],
            'loja_nome' => ['nullable', 'string', 'max:120'],
            'mensagem' => ['required', 'string', 'max:1000'],
            'link_loja' => ['nullable', 'string', 'max:255'],
        ]);

        $user = Auth::user();

        Depoimento::create([
            'loja_id' => $user?->loja_id,
            'autor_nome' => $dados['autor_nome'],
            // Se não informar o nome da loja, usa o da loja logada.
            'loja_nome' => $dados['loja_nome'] ?: ($user?->loja?->nome_fantasia),
            'mensagem' => $dados['mensagem'],
            'link_loja' => $dados['link_loja'] ?? null,
            'aprovado' => false,
        ]);

        return back()->with('success', 'Obrigado pelo carinho! 💛 Seu elogio foi enviado e, após uma rápida conferência, poderá aparecer na nossa página inicial — com o link da sua loja junto.');
    }

    /**
     * Painel de moderação (super_admin).
     */
    public function index()
    {
        $pendentes = Depoimento::where('aprovado', false)->latest()->get();
        $aprovados = Depoimento::where('aprovado', true)->latest()->get();

        return view('admin.depoimentos', compact('pendentes', 'aprovados'));
    }

    public function aprovar(Depoimento $depoimento)
    {
        $depoimento->update(['aprovado' => true]);

        return back()->with('success', 'Depoimento aprovado! Já aparece na página inicial.');
    }

    public function reprovar(Depoimento $depoimento)
    {
        $depoimento->update(['aprovado' => false]);

        return back()->with('success', 'Depoimento ocultado da página inicial.');
    }

    public function destroy(Depoimento $depoimento)
    {
        $depoimento->delete();

        return back()->with('success', 'Depoimento removido.');
    }
}
