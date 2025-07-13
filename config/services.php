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

    'cybersource' => [
        'profile_id' => env('CYBERSOURCE_PROFILE_ID'),
        'access_key' => env('CYBERSOURCE_ACCESS_KEY'),
        'secret_key' => env('CYBERSOURCE_SECRET_KEY'),
        'test_mode' => env('CYBERSOURCE_TEST_MODE', true),
        'test_url' => env('CYBERSOURCE_TEST_URL', 'https://testsecureacceptance.cybersource.com/pay'),
        'live_url' => env('CYBERSOURCE_LIVE_URL', 'https://secureacceptance.cybersource.com/pay'),
    ],
];
