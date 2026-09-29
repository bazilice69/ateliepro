<?php

namespace App\Http\Controllers;

use App\Models\Assinatura;
use App\Models\Loja;
use App\Models\Plano;
use App\Services\LicenseService;
use App\Support\MercadoPago;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Checkout de assinatura do SaaS via Mercado Pago (PIX).
 *
 * Fluxo: escolha do plano -> cria assinatura PENDENTE -> gera pagamento PIX
 * (guardando transacao_mp_id) -> exibe QR Code. A confirmação NÃO depende do
 * navegador: é o webhook que ativa a loja quando o pagamento é aprovado.
 */
class CheckoutController extends Controller
{
    public function __construct(private LicenseService $licenseService)
    {
    }

    /**
     * Tela de escolha de plano para a loja logada (usada no fim do trial ou
     * quando o admin quer assinar/renovar).
     */
    public function escolher(Request $request)
    {
        $planos = Plano::ativos()->get();
        $loja = $request->user()?->loja;

        return view('checkout.escolher', compact('planos', 'loja'));
    }

    /**
     * Inicia o checkout de um plano (identificado pelo slug).
     */
    public function plano(Request $request, string $slug)
    {
        $plano = Plano::ativos()->where('slug', $slug)->firstOrFail();

        // Loja do usuário logado (fluxo normal) — se não houver, mostra aviso.
        $loja = $request->user()?->loja;
        if (!$loja) {
            return redirect()->route('login')
                ->with('status', 'Faça login com a conta da sua loja para assinar um plano.');
        }

        if (!MercadoPago::configurado()) {
            return view('checkout.indisponivel', compact('plano', 'loja'));
        }

        // Cria a assinatura pendente e gera o pagamento PIX.
        $assinatura = $this->licenseService->criarAssinaturaPendente($loja, $plano);

        $resposta = MercadoPago::client()
            ->withHeaders(['X-Idempotency-Key' => (string) Str::uuid()])
            ->post('/v1/payments', [
                'transaction_amount' => (float) $plano->preco,
                'description' => "AteliêPro - Plano {$plano->nome} - {$loja->nome_fantasia}",
                'payment_method_id' => 'pix',
                'notification_url' => route('webhook.mercadopago'),
                'external_reference' => (string) $assinatura->id, // liga o pagamento à assinatura
                'payer' => [
                    'email' => $loja->email_responsavel,
                ],
            ]);

        $pagamento = $resposta->json();

        if (!$resposta->successful() || !isset($pagamento['point_of_interaction'])) {
            $assinatura->update(['status' => Assinatura::STATUS_CANCELADA]);
            return view('checkout.erro', ['mensagem' => 'Não foi possível gerar o pagamento. Tente novamente.']);
        }

        // Guarda o ID da transação na assinatura (idempotência/rastreio).
        $assinatura->update(['transacao_mp_id' => (string) ($pagamento['id'] ?? '')]);

        $dados = [
            'plano' => $plano,
            'loja' => $loja,
            'assinatura' => $assinatura,
            'qrCodeBase64' => $pagamento['point_of_interaction']['transaction_data']['qr_code_base64'] ?? null,
            'copiaECola' => $pagamento['point_of_interaction']['transaction_data']['qr_code'] ?? null,
            'idTransacao' => $pagamento['id'] ?? null,
        ];

        return view('checkout.pix', $dados);
    }

    /**
     * Tela consultada pelo cliente para saber se o pagamento já caiu.
     */
    public function status(Assinatura $assinatura)
    {
        return response()->json(['status' => $assinatura->status]);
    }
}
