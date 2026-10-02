<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Camada fina de acesso à API do Mercado Pago.
 *
 * A credencial (access token) é lida PRIMEIRO das configurações do painel
 * (tabela settings), com fallback para config/services.php (.env). Assim o
 * super_admin pode configurar tudo pela interface, sem editar arquivos.
 */
class MercadoPago
{
    public static function accessToken(): ?string
    {
        return Setting::get('mercadopago_access_token') ?: config('services.mercadopago.access_token');
    }

    public static function webhookSecret(): ?string
    {
        return Setting::get('mercadopago_webhook_secret') ?: config('services.mercadopago.webhook_secret');
    }

    public static function configurado(): bool
    {
        return !empty(self::accessToken());
    }

    /**
     * Cliente HTTP autenticado para a API do Mercado Pago.
     */
    public static function client()
    {
        return Http::withToken(self::accessToken())
            ->baseUrl('https://api.mercadopago.com')
            ->acceptJson();
    }

    /**
     * Consulta um pagamento pelo ID (usado pelo webhook para confirmar).
     */
    public static function buscarPagamento(string $paymentId): ?array
    {
        $resposta = self::client()->get("/v1/payments/{$paymentId}");

        return $resposta->successful() ? $resposta->json() : null;
    }

    /**
     * Cria uma PREFERÊNCIA de Checkout Pro.
     *
     * Diferente do pagamento PIX direto, a preferência gera uma tela hospedada
     * pelo Mercado Pago onde o cliente ESCOLHE como pagar: cartão de crédito
     * (com PARCELAMENTO), PIX ou boleto. Retorna o array da preferência (com
     * `init_point` = URL para redirecionar o cliente) ou null em caso de erro.
     *
     * @param  array  $payload  Corpo da preferência (items, back_urls, etc.).
     */
    public static function criarPreferencia(array $payload): ?array
    {
        $resposta = self::client()->post('/checkout/preferences', $payload);

        if (!$resposta->successful()) {
            // Registra o erro real do Mercado Pago para diagnóstico.
            Log::warning('MP preferencia falhou', [
                'status' => $resposta->status(),
                'body'   => $resposta->body(),
            ]);
            return null;
        }

        return $resposta->json();
    }
}
