<?php

namespace App\Http\Controllers;

use App\Models\Assinatura;
use App\Models\Loja;
use App\Models\Plano;
use App\Models\Setting;
use App\Services\LicenseService;
use App\Support\MercadoPago;
use App\Support\PaymentGateways;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Checkout de assinatura do SaaS via Mercado Pago (Checkout Pro).
 *
 * Fluxo: escolha do plano -> cria assinatura PENDENTE -> cria uma PREFERÊNCIA
 * de Checkout Pro -> redireciona o cliente para a tela do Mercado Pago, onde
 * ele ESCOLHE como pagar: cartão de crédito (com PARCELAMENTO), PIX ou boleto.
 * A confirmação NÃO depende do navegador: é o webhook que ativa a loja quando
 * o pagamento é aprovado. O retorno (back_urls) apenas mostra o status.
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

        // Preparado para múltiplos gateways: só processa online se o gateway
        // ativo estiver com credenciais prontas. Senão, mostra a tela de
        // conclusão assistida (com selos de confiança e opções preparadas).
        if (!PaymentGateways::processando()) {
            return view('checkout.indisponivel', compact('plano', 'loja'));
        }

        // Cria a assinatura pendente e gera a PREFERÊNCIA de Checkout Pro.
        $assinatura = $this->licenseService->criarAssinaturaPendente($loja, $plano);

        $urlRetorno = route('checkout.retorno', $assinatura);
        $urlWebhook = route('webhook.mercadopago');
        $ehHttps = str_starts_with($urlRetorno, 'https://');

        $payload = [
            'items' => [[
                'title'       => Setting::nomeSistema() . " - Plano {$plano->nome}",
                'description' => "Assinatura {$plano->nome} - {$loja->nome_fantasia}",
                'quantity'    => 1,
                'currency_id' => 'BRL',
                'unit_price'  => (float) $plano->preco,
            ]],
            'payer' => [
                'email' => $loja->email_responsavel,
            ],
            // Liga o pagamento à assinatura (o webhook usa isto para confirmar).
            'external_reference' => (string) $assinatura->id,
            // Para onde o Mercado Pago devolve o cliente após pagar.
            'back_urls' => [
                'success' => $urlRetorno,
                'pending' => $urlRetorno,
                'failure' => $urlRetorno,
            ],
            // Métodos de pagamento: cartão (com parcelamento), PIX e boleto.
            // Nada é excluído -> o cliente vê todas as opções e pode parcelar.
            'payment_methods' => [
                'installments' => 12, // até 12x no cartão de crédito
            ],
            'statement_descriptor' => Str::limit(Setting::nomeSistema(), 22, ''),
        ];

        // O Mercado Pago EXIGE HTTPS para notification_url e auto_return.
        // Em HTTP (ainda sem SSL), enviá-los faz a preferência ser recusada,
        // então só os incluímos quando o site já está em HTTPS.
        if ($ehHttps) {
            $payload['notification_url'] = $urlWebhook;
            $payload['auto_return'] = 'approved';
        }

        $preferencia = MercadoPago::criarPreferencia($payload);

        if (!$preferencia || empty($preferencia['init_point'])) {
            $assinatura->update(['status' => Assinatura::STATUS_CANCELADA]);
            return view('checkout.erro', ['mensagem' => 'Não foi possível iniciar o pagamento. Tente novamente.']);
        }

        // Guarda o id da preferência para rastreio.
        $assinatura->update(['transacao_mp_id' => (string) ($preferencia['id'] ?? '')]);

        // Redireciona o cliente para a tela do Mercado Pago (cartão/PIX/boleto).
        return redirect()->away($preferencia['init_point']);
    }

    /**
     * Página de retorno do Checkout Pro (back_urls). Apenas informa o status —
     * a ativação real acontece no webhook. Faz polling para avisar quando o
     * pagamento for confirmado.
     */
    public function retorno(Request $request, Assinatura $assinatura)
    {
        return view('checkout.retorno', [
            'assinatura' => $assinatura,
            'loja'       => $assinatura->loja,
            // status informado pelo MP na querystring (collection_status/status).
            'statusMp'   => $request->query('status') ?? $request->query('collection_status'),
        ]);
    }

    /**
     * Tela consultada pelo cliente (polling) para saber se o pagamento já caiu.
     */
    public function status(Assinatura $assinatura)
    {
        return response()->json(['status' => $assinatura->status]);
    }
}
