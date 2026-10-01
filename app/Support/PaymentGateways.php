<?php

namespace App\Support;

use App\Models\Setting;

/**
 * Registro central dos meios de pagamento do SaaS.
 *
 * O AteliêPro é preparado para MÚLTIPLOS gateways. Hoje o Mercado Pago (PIX)
 * é o que processa de verdade; os demais ficam "preparados" (aparecem como
 * opções de confiança no checkout e podem ser ativados quando as credenciais
 * forem cadastradas em /admin/config).
 *
 * Esta classe NÃO processa pagamento — ela só descreve os gateways para a
 * interface (selos, logos, status) e diz qual está ativo. O processamento
 * continua no App\Support\MercadoPago (e, no futuro, em implementações irmãs).
 */
class PaymentGateways
{
    /**
     * Catálogo de gateways suportados/preparados.
     *
     * icone: classe Font Awesome (brands quando houver) para exibir no selo.
     * cor:   classe Tailwind de cor para o ícone/selo.
     */
    public const CATALOGO = [
        'mercadopago' => [
            'nome'    => 'Mercado Pago',
            'icone'   => 'fas fa-wallet',
            'cor'     => 'text-sky-500',
            'metodos' => ['Pix', 'Cartão de crédito', 'Boleto'],
        ],
        'pagbank' => [
            'nome'    => 'PagBank (PagSeguro)',
            'icone'   => 'fas fa-shield-halved',
            'cor'     => 'text-emerald-500',
            'metodos' => ['Pix', 'Cartão de crédito', 'Boleto'],
        ],
        'stripe' => [
            'nome'    => 'Stripe',
            'icone'   => 'fab fa-stripe-s',
            'cor'     => 'text-indigo-500',
            'metodos' => ['Cartão de crédito internacional'],
        ],
        'asaas' => [
            'nome'    => 'Asaas',
            'icone'   => 'fas fa-building-columns',
            'cor'     => 'text-blue-600',
            'metodos' => ['Pix', 'Cartão de crédito', 'Boleto'],
        ],
        'pagarme' => [
            'nome'    => 'Pagar.me',
            'icone'   => 'fas fa-credit-card',
            'cor'     => 'text-slate-700',
            'metodos' => ['Pix', 'Cartão de crédito'],
        ],
    ];

    /**
     * Qual gateway está ativo (processa de verdade). Configurável em
     * /admin/config pela chave `gateway_ativo`; padrão: mercadopago.
     */
    public static function ativo(): string
    {
        return Setting::get('gateway_ativo', 'mercadopago') ?: 'mercadopago';
    }

    /**
     * O gateway ativo está com credenciais prontas para processar?
     */
    public static function processando(): bool
    {
        return match (self::ativo()) {
            'mercadopago' => MercadoPago::configurado(),
            default       => false, // demais gateways: preparados, ainda não processam
        };
    }

    /**
     * Lista de gateways para exibição (nome, ícone, métodos e se está ativo),
     * com o gateway ativo primeiro.
     */
    public static function paraExibir(): array
    {
        $ativo = self::ativo();
        $lista = [];

        foreach (self::CATALOGO as $chave => $dados) {
            $lista[] = $dados + [
                'chave' => $chave,
                'ativo' => $chave === $ativo,
            ];
        }

        // Gateway ativo primeiro.
        usort($lista, fn ($a, $b) => ($b['ativo'] <=> $a['ativo']));

        return $lista;
    }

    /**
     * Bandeiras de cartão aceitas (para os selos de confiança).
     * icone Font Awesome brands.
     */
    public const BANDEIRAS = [
        ['nome' => 'Visa',       'icone' => 'fab fa-cc-visa'],
        ['nome' => 'Mastercard', 'icone' => 'fab fa-cc-mastercard'],
        ['nome' => 'Elo',        'icone' => 'fas fa-credit-card'],
        ['nome' => 'Amex',       'icone' => 'fab fa-cc-amex'],
        ['nome' => 'Pix',        'icone' => 'fas fa-qrcode'],
        ['nome' => 'Boleto',     'icone' => 'fas fa-barcode'],
    ];
}
