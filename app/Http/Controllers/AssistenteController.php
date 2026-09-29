<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Services\AssistenteService;
use App\Support\IA;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Assistente de IA da loja: chat com contexto do negócio + gerador de mensagens
 * de WhatsApp. Histórico do chat mantido na sessão (por loja).
 */
class AssistenteController extends Controller
{
    public function __construct(private AssistenteService $assistente)
    {
    }

    public function index(Request $request)
    {
        $historico = session('assistente_historico', []);
        $iaAtiva = IA::configurada();
        $clientes = Cliente::orderBy('nome')->get();

        return view('assistente.index', compact('historico', 'iaAtiva', 'clientes'));
    }

    public function enviar(Request $request)
    {
        $request->validate(['mensagem' => ['required', 'string', 'max:1000']]);

        $loja = $request->user()->loja;
        $historico = session('assistente_historico', []);

        // Só as últimas trocas (12 mensagens = ~6 perguntas/respostas) vão como
        // contexto, para evitar um prompt gigante.
        $contextoHistorico = array_slice($historico, -12);

        $resposta = $this->assistente->responder($loja, $request->mensagem, $contextoHistorico);

        $historico[] = ['role' => 'user', 'content' => $request->mensagem];
        $historico[] = ['role' => 'assistant', 'content' => $resposta];
        session(['assistente_historico' => $historico]);

        return redirect()->route('assistente.index');
    }

    public function limpar(Request $request)
    {
        session()->forget('assistente_historico');
        return redirect()->route('assistente.index');
    }

    /**
     * Gera uma mensagem de WhatsApp por tipo (confirmação, cobrança, etc.),
     * personalizada com o nome do cliente. Usa IA se configurada; senão, cai
     * num template pronto.
     */
    public function gerarWhatsapp(Request $request)
    {
        $lojaId = $request->user()->loja_id;

        $request->validate([
            'tipo' => ['required', 'string'],
            'cliente_id' => ['nullable', Rule::exists('clientes', 'id')->where('loja_id', $lojaId)],
        ]);

        $loja = $request->user()->loja;
        $cliente = $request->cliente_id ? Cliente::find($request->cliente_id) : null;
        $nome = $cliente->nome ?? '{{cliente}}';

        $mensagem = $this->mensagemWhatsapp($request->tipo, $nome, $loja->nome_fantasia);

        return response()->json(['mensagem' => $mensagem]);
    }

    private function mensagemWhatsapp(string $tipo, string $nome, string $loja): string
    {
        // Templates base (usados diretamente ou como semente para a IA refinar).
        $templates = [
            'confirmacao' => "Olá, {$nome}! 😊 Passando para confirmar seu atendimento na {$loja}. Podemos manter o horário combinado? Qualquer coisa é só avisar!",
            'prova' => "Oi, {$nome}! ✨ Lembrete da sua prova aqui na {$loja}. Estamos ansiosas para te ver! Confirma que está tudo certo para o horário?",
            'retirada' => "Olá, {$nome}! 👗 Sua peça já está prontinha para retirada na {$loja}. Quando fica melhor para você passar aqui?",
            'devolucao' => "Oi, {$nome}! Passando para lembrar da devolução da peça alugada na {$loja}. Obrigada pela parceria! 💕",
            'pagamento' => "Olá, {$nome}! 💰 Lembrete amigável sobre o pagamento pendente do seu pedido na {$loja}. Qualquer dúvida, estou à disposição!",
            'pos_venda' => "Oi, {$nome}! 💖 Esperamos que tenha amado sua experiência com a {$loja}. Sua opinião é muito importante — como foi tudo?",
            'aniversario' => "Feliz aniversário, {$nome}! 🎉 A equipe da {$loja} deseja um dia maravilhoso. Preparamos um mimo especial para você!",
        ];

        $base = $templates[$tipo] ?? $templates['confirmacao'];

        // Se a IA estiver configurada, pede um refinamento mais natural.
        if (IA::configurada()) {
            try {
                $system = "Você escreve mensagens curtas e calorosas de WhatsApp para clientes de uma loja de noivas chamada {$loja}. Tom acolhedor, brasileiro, com no máximo 2 emojis. Não invente datas nem valores.";
                $prompt = "Reescreva de forma natural esta mensagem do tipo '{$tipo}' para a cliente {$nome}: \"{$base}\"";
                return IA::conversar($system, [['role' => 'user', 'content' => $prompt]]);
            } catch (\Throwable $e) {
                return $base; // fallback silencioso
            }
        }

        return $base;
    }
}
