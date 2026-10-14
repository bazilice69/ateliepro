<?php

namespace App\Http\Controllers;

use App\Services\DemoService;
use Illuminate\Http\Request;

/**
 * Botão "Ver com dados de exemplo" do dashboard: popula a loja do usuário com
 * dados fictícios (marcados is_demo) e permite removê-los depois.
 */
class DemoController extends Controller
{
    public function __construct(private DemoService $demo)
    {
    }

    public function popular(Request $request)
    {
        $loja = $request->user()->loja;
        if (!$loja) {
            return back()->with('error', 'Nenhuma loja associada à sua conta.');
        }

        $this->demo->popular($loja);

        return redirect()->route('dashboard')
            ->with('success', 'Pronto! Preenchemos sua loja com dados de exemplo para você explorar. '
                . 'Quando quiser, clique em "Limpar dados de exemplo" para remover tudo. ✨');
    }

    public function limpar(Request $request)
    {
        $loja = $request->user()->loja;
        if (!$loja) {
            return back()->with('error', 'Nenhuma loja associada à sua conta.');
        }

        $this->demo->limpar($loja);

        return redirect()->route('dashboard')
            ->with('success', 'Dados de exemplo removidos. Sua loja está limpa para os dados reais. 🧹');
    }
}
