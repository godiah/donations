<?php

return [
    /*
    |--------------------------------------------------------------------------
    | M-Pesa Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for M-Pesa payment integration
    |
    */

    'default_environment' => env('MPESA_ENVIRONMENT', 'sandbox'),

    /*
    |--------------------------------------------------------------------------
    | Sandbox Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for M-Pesa sandbox environment
    |
    */
    'sandbox' => [
        'consumer_key' => env('MPESA_SANDBOX_CONSUMER_KEY'),
        'consumer_secret' => env('MPESA_SANDBOX_CONSUMER_SECRET'),
        'business_short_code' => env('MPESA_SANDBOX_BUSINESS_SHORT_CODE', '174379'),
        'lipa_na_mpesa_passkey' => env('MPESA_SANDBOX_PASSKEY'),
        'base_url' => 'https://sandbox.safaricom.co.ke',
        'initiator_name' => env('MPESA_SANDBOX_INITIATOR_NAME'),
        'security_credential' => env('MPESA_SANDBOX_SECURITY_CREDENTIAL'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Production Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for M-Pesa production environment
    |
    */
    'production' => [
        'consumer_key' => env('MPESA_PRODUCTION_CONSUMER_KEY'),
        'consumer_secret' => env('MPESA_PRODUCTION_CONSUMER_SECRET'),
        'business_short_code' => env('MPESA_PRODUCTION_BUSINESS_SHORT_CODE'),
        'lipa_na_mpesa_passkey' => env('MPESA_PRODUCTION_PASSKEY'),
        'base_url' => 'https://api.safaricom.co.ke',
        'initiator_name' => env('MPESA_PRODUCTION_INITIATOR_NAME'),
        'security_credential' => env('MPESA_PRODUCTION_SECURITY_CREDENTIAL'),
    ],

    /*
    |--------------------------------------------------------------------------
    | API Endpoints
    |--------------------------------------------------------------------------
    |
    | M-Pesa API endpoints configuration
    |
    */
    'endpoints' => [
        'oauth' => '/oauth/v1/generate?grant_type=client_credentials',
        'stk_push' => '/mpesa/stkpush/v1/processrequest',
        'stk_push_query' => '/mpesa/stkpushquery/v1/query',
        'b2c' => '/mpesa/b2c/v1/paymentrequest',
        'account_balance' => '/mpesa/accountbalance/v1/query',
        'transaction_status' => '/mpesa/transactionstatus/v1/query',
        'reversal' => '/mpesa/reversal/v1/request',
    ],

    /*
    |--------------------------------------------------------------------------
    | Callback URLs
    |--------------------------------------------------------------------------
    |
    | URLs for M-Pesa callbacks and webhooks
    |
    */
    'callback_urls' => [
        'stk_push_callback' => env('MPESA_STK_PUSH_CALLBACK_URL'),
        'confirmation_url' => env('MPESA_CONFIRMATION_URL'),
        'validation_url' => env('MPESA_VALIDATION_URL'),
        'queue_timeout_url' => env('MPESA_QUEUE_TIMEOUT_URL'),
        'result_url' => env('MPESA_RESULT_URL'),
        'b2c_result_url' => env('MPESA_B2C_RESULT_URL'),
        'b2c_queue_timeout_url' => env('MPESA_B2C_QUEUE_TIMEOUT_URL'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Transaction Limits
    |--------------------------------------------------------------------------
    |
    | Configure transaction limits for M-Pesa payments
    |
    */
    'transaction_limits' => [
        'minimum_amount' => (float) env('MPESA_MIN_AMOUNT', 1),
        'maximum_amount' => (float) env('MPESA_MAX_AMOUNT', 150000), // KES 150,000
        'daily_limit' => (float) env('MPESA_DAILY_LIMIT', 300000), // KES 300,000
        'monthly_limit' => (float) env('MPESA_MONTHLY_LIMIT', 3000000), // KES 3,000,000
    ],

    /*
    |--------------------------------------------------------------------------
    | Timeout Settings
    |--------------------------------------------------------------------------
    |
    | Configure timeout settings for various operations
    |
    */
    'timeout_settings' => [
        'stk_push_timeout' => (int) env('MPESA_STK_PUSH_TIMEOUT', 300), // 5 minutes
        'status_check_interval' => (int) env('MPESA_STATUS_CHECK_INTERVAL', 5), // 5 seconds
        'max_status_checks' => (int) env('MPESA_MAX_STATUS_CHECKS', 60), // 5 minutes total
        'api_timeout' => (int) env('MPESA_API_TIMEOUT', 30), // 30 seconds
        'callback_timeout' => (int) env('MPESA_CALLBACK_TIMEOUT', 60), // 1 minute
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    |
    | Security-related configuration
    |
    */
    'security' => [
        'verify_callback_ip' => env('MPESA_VERIFY_CALLBACK_IP', false),
        'allowed_callback_ips' => env('MPESA_ALLOWED_CALLBACK_IPS', ''),
        'encrypt_sensitive_data' => env('MPESA_ENCRYPT_SENSITIVE_DATA', true),
        'log_sensitive_data' => env('MPESA_LOG_SENSITIVE_DATA', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Retry Settings
    |--------------------------------------------------------------------------
    |
    | Configure retry logic for failed requests
    |
    */
    'retry' => [
        'max_attempts' => (int) env('MPESA_MAX_RETRY_ATTEMPTS', 3),
        'delay_seconds' => (int) env('MPESA_RETRY_DELAY', 2),
        'backoff_multiplier' => (float) env('MPESA_RETRY_BACKOFF_MULTIPLIER', 2.0),
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Configure logging for M-Pesa operations
    |
    */
    'logging' => [
        'enabled' => env('MPESA_LOGGING_ENABLED', true),
        'level' => env('MPESA_LOG_LEVEL', 'info'),
        'channel' => env('MPESA_LOG_CHANNEL', 'daily'),
        'log_requests' => env('MPESA_LOG_REQUESTS', true),
        'log_responses' => env('MPESA_LOG_RESPONSES', true),
        'log_callbacks' => env('MPESA_LOG_CALLBACKS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Configure caching for M-Pesa access tokens and other data
    |
    */
    'cache' => [
        'enabled' => env('MPESA_CACHE_ENABLED', true),
        'ttl' => (int) env('MPESA_CACHE_TTL', 3300), // 55 minutes (tokens expire in 1 hour)
        'prefix' => env('MPESA_CACHE_PREFIX', 'mpesa'),
        'store' => env('MPESA_CACHE_STORE', 'redis'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Configure rate limiting to comply with M-Pesa API limits
    |
    */
    'rate_limiting' => [
        'enabled' => env('MPESA_RATE_LIMITING_ENABLED', true),
        'requests_per_minute' => (int) env('MPESA_REQUESTS_PER_MINUTE', 20),
        'requests_per_hour' => (int) env('MPESA_REQUESTS_PER_HOUR', 1000),
        'burst_limit' => (int) env('MPESA_BURST_LIMIT', 5),
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    |
    | Configure webhook handling and verification
    |
    */
    'webhooks' => [
        'verify_signature' => env('MPESA_VERIFY_WEBHOOK_SIGNATURE', true),
        'secret' => env('MPESA_WEBHOOK_SECRET'),
        'tolerance' => (int) env('MPESA_WEBHOOK_TOLERANCE', 300), // 5 minutes
        'auto_acknowledge' => env('MPESA_AUTO_ACKNOWLEDGE_WEBHOOKS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Flags
    |--------------------------------------------------------------------------
    |
    | Enable or disable specific M-Pesa features
    |
    */
    'features' => [
        'stk_push' => env('MPESA_FEATURE_STK_PUSH', true),
        'paybill' => env('MPESA_FEATURE_PAYBILL', true),
        'b2c' => env('MPESA_FEATURE_B2C', false),
        'account_balance' => env('MPESA_FEATURE_ACCOUNT_BALANCE', false),
        'transaction_status' => env('MPESA_FEATURE_TRANSACTION_STATUS', true),
        'reversal' => env('MPESA_FEATURE_REVERSAL', false),
        'auto_query_status' => env('MPESA_AUTO_QUERY_STATUS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation Rules
    |--------------------------------------------------------------------------
    |
    | Configure validation rules for M-Pesa transactions
    |
    */
    'validation' => [
        'phone_number' => [
            'regex' => '/^(254|0)[17][0-9]{8}$/',
            'length' => [9, 12],
        ],
        'amount' => [
            'decimal_places' => 2,
            'format' => 'numeric',
        ],
        'account_reference' => [
            'max_length' => 13,
            'allowed_characters' => 'alphanumeric',
        ],
        'transaction_desc' => [
            'max_length' => 13,
            'required' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Business Logic Configuration
    |--------------------------------------------------------------------------
    |
    | Configure business-specific M-Pesa settings
    |
    */
    'business' => [
        'auto_confirm_payments' => env('MPESA_AUTO_CONFIRM_PAYMENTS', true),
        'send_confirmation_sms' => env('MPESA_SEND_CONFIRMATION_SMS', false),
        'send_confirmation_email' => env('MPESA_SEND_CONFIRMATION_EMAIL', true),
        'refund_policy_days' => (int) env('MPESA_REFUND_POLICY_DAYS', 30),
        'settlement_account' => env('MPESA_SETTLEMENT_ACCOUNT'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Testing Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for testing M-Pesa integration
    |
    */
    'testing' => [
        'enabled' => env('MPESA_TESTING_ENABLED', false),
        'mock_responses' => env('MPESA_MOCK_RESPONSES', false),
        'test_phone_numbers' => [
            '254708374149', // Safaricom test number
            '254711232545', // Safaricom test number
        ],
        'test_amounts' => [
            1,
            10,
            100,
            1000, // Various test amounts
        ],
        'force_callback_simulation' => env('MPESA_FORCE_CALLBACK_SIMULATION', false),
    ],
];
