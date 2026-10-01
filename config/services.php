<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'mercadopago' => [
        // Fallback via .env; em produção o super_admin configura pelo painel
        // (tabela settings) — ver App\Support\MercadoPago.
        'access_token' => env('MERCADOPAGO_ACCESS_TOKEN'),
        'public_key' => env('MERCADOPAGO_PUBLIC_KEY'),
        'webhook_secret' => env('MERCADOPAGO_WEBHOOK_SECRET'),
    ],

    // Gateways de pagamento PREPARADOS (ainda não processam; aparecem como
    // selos de confiança no checkout). Ativar conforme as credenciais chegarem.
    'pagbank' => [
        'token' => env('PAGBANK_TOKEN'),
    ],
    'stripe' => [
        'secret' => env('STRIPE_SECRET'),
        'public_key' => env('STRIPE_PUBLIC_KEY'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],
    'asaas' => [
        'api_key' => env('ASAAS_API_KEY'),
    ],
    'pagarme' => [
        'api_key' => env('PAGARME_API_KEY'),
    ],

    'ia' => [
        // Provedor: openai | anthropic | gemini. Chave configurável pelo painel
        // (tabela settings) ou aqui via .env — ver App\Support\IA.
        'provedor' => env('IA_PROVEDOR', 'openai'),
        'api_key' => env('IA_API_KEY'),
        'modelo' => env('IA_MODELO'),
    ],

];
