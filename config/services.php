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

    'resend' => [
        'key' => env('RESEND_KEY'),
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

    /*
    |--------------------------------------------------------------------------
    | Africa's Talking Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Africa's Talking SMS API
    |
    */
    'africastalking' => [
        'username' => env('AFRICASTALKING_USERNAME', 'wigopaybulksms'),
        'key' => env('AFRICASTALKING_API_KEY', 'atsk_c4e1dbab5d5de9e222992316bee7bc26ffb5d5b2cadcb9fc9fd0a843fce4ab30ba3b35d2'),
        'sender_id' => env('AFRICASTALKING_SENDER_ID', 'wiGOPAY'),
        'whatsapp_number' => env('AFRICASTALKING_WHATSAPP_NUMBER', '+254745548093'), // Add your WhatsApp-enabled number
    ],

    /*
    |--------------------------------------------------------------------------
    | Smile Identity Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Smile Identity API
    |
    */
    'smile_identity' => [
        'base_url' => env('SMILE_IDENTITY_BASE_URL', 'https://testapi.smileidentity.com/v2/'),
        'partner_id' => env('SMILE_IDENTITY_PARTNER_ID'),
        'api_key' => env('SMILE_IDENTITY_API_KEY'),
        'environment' => env('SMILE_IDENTITY_ENVIRONMENT', 'sandbox'), // 'sandbox' or 'production'
        'callback_url' => env('NGROK_URL') . '/api/kyc/callback',
    ],
];
