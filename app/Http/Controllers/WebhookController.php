<?php

namespace App\Http\Controllers;

use App\Models\Assinatura;
use App\Services\LicenseService;
use App\Support\MercadoPago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Recebe as notificações (webhook) do Mercado Pago.
 *
 * NUNCA confia no navegador: a ativação da loja só acontece aqui, após
 * CONSULTAR o pagamento na API do MP e confirmar que está "approved".
 * A rota é isenta de CSRF (ver bootstrap/app.php) e não exige auth.
 */
class WebhookController extends Controller
{
    public function __construct(private LicenseService $licenseService)
    {
    }

    public function mercadopago(Request $request)
    {
        // O MP envia o id do pagamento em data.id (query ou corpo).
        $paymentId = $request->input('data.id') ?? $request->query('data_id') ?? $request->input('id');
        $type = $request->input('type') ?? $request->query('type');

        // Só nos interessam notificações de pagamento.
        if ($type && $type !== 'payment') {
            return response()->json(['ignored' => true]);
        }

        if (!$paymentId) {
            return response()->json(['error' => 'sem payment id'], 400);
        }

        // Consulta o pagamento real na API (fonte da verdade).
        $pagamento = MercadoPago::buscarPagamento((string) $paymentId);

        if (!$pagamento) {
            Log::warning('Webhook MP: pagamento não encontrado', ['payment_id' => $paymentId]);
            return response()->json(['error' => 'pagamento nao encontrado'], 404);
        }

        $status = $pagamento['status'] ?? null;
        $assinaturaId = $pagamento['external_reference'] ?? null;

        if ($status !== 'approved') {
            // pending, rejected, etc. — apenas registra e responde 200.
            return response()->json(['status' => $status]);
        }

        // Localiza a assinatura pela referência externa (ou pelo transacao_mp_id).
        $assinatura = null;
        if ($assinaturaId) {
            $assinatura = Assinatura::withoutGlobalScope('loja')->find($assinaturaId);
        }
        if (!$assinatura) {
            $assinatura = Assinatura::withoutGlobalScope('loja')
                ->where('transacao_mp_id', (string) $paymentId)->first();
        }

        if (!$assinatura) {
            Log::warning('Webhook MP: assinatura não encontrada', ['payment_id' => $paymentId, 'ref' => $assinaturaId]);
            return response()->json(['error' => 'assinatura nao encontrada'], 404);
        }

        // Idempotência: se já está paga, não faz de novo.
        if ($assinatura->status === Assinatura::STATUS_PAGA) {
            return response()->json(['status' => 'ja_processado']);
        }

        // Confirma: ativa a loja, gera a licença e estende o vencimento.
        $this->licenseService->confirmarPagamento($assinatura, (string) $paymentId);

        return response()->json(['status' => 'ativado']);
    }
}
