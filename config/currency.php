<?php

return [
    'default_currency' => 'KES',

    'supported_currencies' => [
        'KES' => [
            'name' => 'Kenyan Shilling',
            'symbol' => 'KSh',
            'decimal_places' => 2,
        ],
        'USD' => [
            'name' => 'US Dollar',
            'symbol' => '$',
            'decimal_places' => 2,
        ],
    ],

    'exchange_rates' => [
        // Base currency is KES
        'USD' => [
            'to_kes' => 135.0, // 1 USD = 135 KES (update this regularly)
            'from_kes' => 0.0074, // 1 KES = 0.0074 USD
            'last_updated' => '2025-07-08',
        ],
    ],

    // Cache exchange rates for this duration
    'cache_duration_hours' => 6,

    // API settings for real-time rates (if implemented)
    'api' => [
        'enabled' => false,
        'provider' => 'exchangerate-api', // or 'fixer', 'currencylayer', etc.
        'api_key' => env('EXCHANGE_RATE_API_KEY'),
        'base_url' => 'https://api.exchangerate-api.com/v4/latest/',
        'timeout' => 10,
    ],
];
