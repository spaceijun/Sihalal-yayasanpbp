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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'kawulohalal' => [
        // API key (Bearer token) dari dashboard KawalAku Gateway
        'api_key'  => env('KAWULOHALAL_API_KEY'),
        // Base URL lokal KawalAku Gateway (kawalakugateway Laravel project)
        'base_url' => env('KAWULOHALAL_BASE_URL', 'http://kawalakugateway.test'),
    ],

    'wa_gateway' => [
        // Node.js Baileys server URL (untuk Socket.IO & QR code scan)
        'url' => env('WA_GATEWAY_URL', 'http://localhost:3000'),
    ],

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
    ],

    'app' => [
        'name' => env('APP_NAME', 'Laravel'),
    ],

    'anthropic' => [
        'api_key' => env('ANTHROPIC_API_KEY'),
    ],

    'oss' => [
        'tarif_per_paket_oss' => env('TARIF_OSS_PER_PAKET', 0),
    ],

    'sihalal' => [
        'tarif_per_paket_sihalal' => env('TARIF_SIHALAL_PER_PAKET', 0),
    ],

];
