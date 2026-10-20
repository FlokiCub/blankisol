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

    'whatsapp' => [
        'provider' => env('WHATSAPP_PROVIDER', 'log'), // 'log', 'meta', 'local', 'http', 'ultramsg', 'wati', 'gupshup', 'twilio'
        'local' => [
            // En local: http://127.0.0.1:3333
            // En producción: URL pública del gateway (ej: https://tu-dominio.com:3333 o https://api.tu-dominio.com/whatsapp)
            // Si el gateway corre en el mismo servidor que Laravel, usa la URL interna del contenedor/servidor
            'base_url' => env('WHATSAPP_LOCAL_URL', 'http://127.0.0.1:3333'),
            'api_key' => env('WHATSAPP_LOCAL_KEY', 'scgi_local_key_2026'),
        ],
        'meta' => [
            'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID', ''),
            'access_token' => env('WHATSAPP_ACCESS_TOKEN', ''),
            'business_account_id' => env('WHATSAPP_BUSINESS_ACCOUNT_ID', ''),
            'app_secret' => env('WHATSAPP_APP_SECRET', ''),
            'api_version' => env('WHATSAPP_API_VERSION', 'v20.0'),
            'base_url' => env('WHATSAPP_BASE_URL', 'https://graph.facebook.com'),
        ],
        'http' => [
            // Proveedores HTTP API: 'ultramsg', 'wati', 'gupshup', 'twilio', 'custom'
            'provider'         => env('WHATSAPP_HTTP_PROVIDER', 'ultramsg'),
            'base_url'         => env('WHATSAPP_HTTP_BASE_URL', 'https://api.ultramsg.com'),
            'instance_id'      => env('WHATSAPP_HTTP_INSTANCE_ID', ''),
            'token'            => env('WHATSAPP_HTTP_TOKEN', ''),
            // Headers extra (ej: Authorization para Twilio/Gupshup)
            'headers'          => [],
            // Template personalizado para proveedores 'custom'
            // Usa placeholders: {phone}, {text}, {token}, {instance_id}, {template_name}, {param_0}, {param_1}...
            'payload_template' => [],
        ],
        'webhook_verify_token' => env('WHATSAPP_WEBHOOK_VERIFY_TOKEN', 'scgi_secure_wa_webhook_token_2026'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI', env('APP_URL', 'http://localhost') . '/shop/auth/google/callback'),
    ],

];
