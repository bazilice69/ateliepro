<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PagamentoController extends Controller
{
    public function gerarPix()
    {
        $valorPlano = 89.00; 
        
        // Aqui está o seu e-mail!
        $emailCliente = 'thgbazilicethg@gmail.com'; 
        
        $response = Http::withToken(env('MERCADOPAGO_ACCESS_TOKEN'))
            ->withHeaders([
                'X-Idempotency-Key' => Str::uuid()->toString()
            ])
            ->post('https://api.mercadopago.com/v1/payments', [
                'transaction_amount' => $valorPlano,
                'description' => 'Assinatura Mensal - AteliêPro',
                'payment_method_id' => 'pix',
                'payer' => [
                    'email' => $emailCliente,
                    'first_name' => 'Thiago Teste',
                ]
            ]);

        $pagamento = $response->json();

        if (isset($pagamento['error']) || !isset($pagamento['point_of_interaction'])) {
            return "Erro ao gerar PIX. Resposta do Mercado Pago: " . json_encode($pagamento);
        }

        $qrCodeBase64 = $pagamento['point_of_interaction']['transaction_data']['qr_code_base64'];
        $copiaECola = $pagamento['point_of_interaction']['transaction_data']['qr_code'];
        $idTransacao = $pagamento['id'];

        return view('checkout.pix', compact('qrCodeBase64', 'copiaECola', 'idTransacao', 'valorPlano'));
    }
}