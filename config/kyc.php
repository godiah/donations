<?php

/*
    |--------------------------------------------------------------------------
    | Smile Identity Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Smile Identity API
    |
    */
return [
    'smile_identity' => [
        'partner_id' => env('SMILE_IDENTITY_PARTNER_ID', '546'),
        'api_key' => env('SMILE_IDENTITY_API_KEY'),
        'auth_token' => env('SMILE_IDENTITY_AUTH_TOKEN'),
        'environment' => env('SMILE_IDENTITY_ENVIRONMENT', 'sandbox'),
        'base_url' => env('SMILE_IDENTITY_ENVIRONMENT', 'sandbox') === 'production'
            ? 'https://api.smileidentity.com/v1/'
            : 'https://testapi.smileidentity.com/v1/',
        'callback_url' => env('SMILE_IDENTITY_CALLBACK_URL', env('NGROK_URL') . '/api/kyc/callback'),
    ],
];
