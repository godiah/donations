<?php

namespace App\Services;

use App\Models\Contribution;
use App\Models\Transaction;
use DateTime;
use DateTimeZone;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CyberSourceService
{
    private $merchantId;
    private $accessKey;
    private $secretKey;
    private $environment;
    private $profileId;
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->merchantId = config('services.cybersource.merchant_id');
        $this->accessKey = config('services.cybersource.access_key');
        $this->secretKey = config('services.cybersource.secret_key');
        $this->environment = config('services.cybersource.environment', 'test');
        $this->profileId = config('services.cybersource.profile_id');
        $this->walletService = $walletService;

        // Log::info('Server time check', [
        //     'server_time' => time(),
        //     'formatted_time' => gmdate('Y-m-d\TH:i:s\Z', time()),
        //     'timezone' => date_default_timezone_get()
        // ]);
    }

    /**
     * Generate CyberSource Secure Acceptance payment form data
     */
    public function generatePaymentFormData(Contribution $contribution): array
    {
        $requestId = $this->generateRequestId();
        $timestamp = gmdate('Y-m-d\TH:i:s\Z', time());

        $contribution->update(['cybersource_request_id' => $requestId]);

        $successUrl = env('CYBERSOURCE_SUCCESS_URL');
        $cancelUrl = env('CYBERSOURCE_CANCEL_URL');
        $errorUrl = env('CYBERSOURCE_ERROR_URL');

        // CRITICAL: Build params in the EXACT order they'll appear in signed_field_names
        $params = [];

        // First, define the field order (this will be our signed_field_names)
        $fieldOrder = [
            'access_key',
            'profile_id',
            'transaction_uuid',
            'signed_field_names',
            'unsigned_field_names',
            'signed_date_time',
            'locale',
            'transaction_type',
            'reference_number',
            'amount',
            'currency',
            'payment_method',
            'bill_to_forename',
            'bill_to_surname',
            'bill_to_email',
            'bill_to_address_line1',
            'bill_to_address_city',
            'bill_to_address_country',
            'bill_to_address_postal_code',
            'override_custom_receipt_page',
            'override_custom_cancel_page',
            'override_custom_error_page'
        ];

        // Add phone field if present
        if (!empty($contribution->phone)) {
            $fieldOrder[] = 'bill_to_phone';
        }

        // Now build params in the exact order
        $params['access_key'] = $this->accessKey;
        $params['profile_id'] = $this->profileId;
        $params['transaction_uuid'] = $requestId;
        $params['signed_field_names'] = implode(',', $fieldOrder);
        $params['unsigned_field_names'] = 'card_type,card_number,card_expiry_date';
        $params['signed_date_time'] = $timestamp;
        $params['locale'] = 'en';
        $params['transaction_type'] = 'sale';
        $params['reference_number'] = strval($contribution->id);
        $params['amount'] = number_format($contribution->amount, 2, '.', '');
        $params['currency'] = strtoupper($contribution->currency);
        $params['payment_method'] = 'card';
        $params['bill_to_forename'] = 'Donor';
        $params['bill_to_surname'] = 'Anonymous';
        $params['bill_to_email'] = $contribution->email;
        $params['bill_to_address_line1'] = '123 Test Street';
        $params['bill_to_address_city'] = 'Nairobi';
        $params['bill_to_address_country'] = 'KE';
        $params['bill_to_address_postal_code'] = '00100';
        $params['override_custom_receipt_page'] = $successUrl;
        $params['override_custom_cancel_page'] = $cancelUrl;
        $params['override_custom_error_page'] = $errorUrl;

        // Add phone if present
        if (!empty($contribution->phone)) {
            $params['bill_to_phone'] = $contribution->phone;
        }

        // Generate signature AFTER all fields are set
        $params['signature'] = $this->generateSignature($params);

        Log::info('Final CyberSource params with correct order', [
            'field_order' => $fieldOrder,
            'signed_fields' => $params['signed_field_names'],
            'signature' => $params['signature'],
            'timestamp' => $params['signed_date_time']
        ]);

        return [
            'action_url' => $this->getActionUrl(),
            'params' => $params,
        ];
    }

    /**
     * Verify webhook signature and process response
     */
    public function processWebhookResponse(array $data): array
    {
        try {
            // Verify signature
            if (!$this->verifySignature($data)) {
                throw new \Exception('Invalid signature');
            }

            $contribution = Contribution::where('cybersource_request_id', $data['req_transaction_uuid'] ?? null)->first();

            if (!$contribution) {
                throw new \Exception('Contribution not found');
            }

            // Map CyberSource decision to our status
            $status = $this->mapDecisionToStatus($data['decision'] ?? '');

            // Update contribution
            $contribution->update([
                'payment_status' => $status,
                'cybersource_transaction_id' => $data['transaction_id'] ?? null,
                'payment_response' => $data,
                'processed_at' => now(),
            ]);

            // Create transaction record
            $transaction = $contribution->transactions()->create([
                'transaction_id' => Str::uuid(),
                'gateway_transaction_id' => $data['transaction_id'] ?? null,
                'gateway' => Transaction::GATEWAY_CYBERSOURCE,
                'type' => Transaction::TYPE_PAYMENT,
                'status' => $status,
                'amount' => $contribution->amount,
                'currency' => $contribution->currency,
                'gateway_response' => $data,
                'processed_at' => now(),
            ]);

            // Credit wallet if payment was successful
            $walletTransaction = null;
            if ($status === Contribution::STATUS_COMPLETED) {
                try {
                    $walletTransaction = $this->walletService->creditFromDonation($contribution);

                    Log::info('Wallet credited from CyberSource payment', [
                        'contribution_id' => $contribution->id,
                        'wallet_transaction_id' => $walletTransaction->id,
                        'amount' => $contribution->amount,
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to credit wallet from CyberSource payment', [
                        'contribution_id' => $contribution->id,
                        'error' => $e->getMessage(),
                    ]);

                    // Don't fail the webhook processing if wallet credit fails
                    // This can be retried later or handled manually
                }
            }

            Log::info('CyberSource webhook processed successfully', [
                'contribution_id' => $contribution->id,
                'transaction_id' => $transaction->id,
                'wallet_transaction_id' => $walletTransaction?->id,
                'status' => $status,
                'cybersource_transaction_id' => $data['transaction_id'] ?? null,
            ]);

            return [
                'success' => true,
                'contribution' => $contribution,
                'transaction' => $transaction,
                'wallet_transaction' => $walletTransaction,
                'status' => $status,
            ];
        } catch (\Exception $e) {
            Log::error('CyberSource webhook processing failed', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Generate unique request ID
     */
    private function generateRequestId(): string
    {
        return 'REQ_' . time() . '_' . Str::random(10);
    }

    /**
     * Validate configuration before processing
     */
    public function validateConfiguration(): array
    {
        $errors = [];

        if (empty($this->merchantId)) {
            $errors[] = 'Merchant ID is required';
        }

        if (empty($this->accessKey)) {
            $errors[] = 'Access Key is required';
        }

        if (empty($this->secretKey)) {
            $errors[] = 'Secret Key is required';
        }

        if (empty($this->profileId)) {
            $errors[] = 'Profile ID is required';
        }

        // Check if secret key looks valid (should be hex string)
        if (!empty($this->secretKey) && !ctype_xdigit($this->secretKey)) {
            $errors[] = 'Secret Key appears to be invalid format';
        }

        return $errors;
    }

    /**
     * Generate HMAC signature for secure acceptance
     */
    private function generateSignature(array $params): string
    {
        $signedFieldNames = explode(',', $params['signed_field_names']);
        $dataToSign = [];

        // CRITICAL: Process fields in the EXACT order they appear in signed_field_names
        foreach ($signedFieldNames as $field) {
            $field = trim($field);

            // Only exclude signature itself
            if ($field === 'signature') {
                continue;
            }

            if (isset($params[$field])) {
                $dataToSign[] = $field . '=' . $params[$field];
            } else {
                Log::warning('Missing field in params', ['field' => $field]);
            }
        }

        $dataString = implode(',', $dataToSign);

        Log::info('Signature generation fixed', [
            'signed_field_names_order' => $signedFieldNames,
            'data_string' => $dataString,
            'data_string_length' => strlen($dataString)
        ]);

        $secretKey = hex2bin($this->secretKey);
        return base64_encode(hash_hmac('sha256', $dataString, $secretKey, true));
    }

    /**
     * Verify signature from CyberSource response
     */
    private function verifySignature(array $data): bool
    {
        if (!isset($data['signature']) || !isset($data['signed_field_names'])) {
            return false;
        }

        $signedFieldNames = explode(',', $data['signed_field_names']);
        $dataToSign = [];

        foreach ($signedFieldNames as $field) {
            if (isset($data[$field])) {
                $dataToSign[] = $field . '=' . $data[$field];
            }
        }

        $dataString = implode(',', $dataToSign);
        $expectedSignature = base64_encode(hash_hmac('sha256', $dataString, $this->secretKey, true));

        return hash_equals($expectedSignature, $data['signature']);
    }

    /**
     * Get CyberSource action URL based on environment
     */
    private function getActionUrl(): string
    {
        if ($this->environment === 'production') {
            return 'https://secureacceptance.cybersource.com/pay';
        }

        return 'https://testsecureacceptance.cybersource.com/pay';
    }

    /**
     * Map CyberSource decision to our payment status
     */
    private function mapDecisionToStatus(string $decision): string
    {
        return match (strtoupper($decision)) {
            'ACCEPT' => Contribution::STATUS_COMPLETED,
            'DECLINE' => Contribution::STATUS_FAILED,
            'REVIEW' => Contribution::STATUS_PROCESSING,
            'ERROR' => Contribution::STATUS_FAILED,
            default => Contribution::STATUS_FAILED,
        };
    }

    /**
     * Get return URLs for CyberSource
     */
    public function getReturnUrls(): array
    {
        return [
            'success_url' => route('donation.success'),
            'cancel_url' => route('donation.cancel'),
            'error_url' => route('donation.error'),
        ];
    }

    /**
     * 
     * 
     * Test configurations
     */

    public function testCredentials(): array
    {
        $testParams = [
            'access_key' => $this->accessKey,
            'profile_id' => $this->profileId,
            'transaction_uuid' => 'TEST_' . time(),
            'signed_date_time' => gmdate('Y-m-d\TH:i:s\Z'),
            'locale' => 'en',
            'transaction_type' => 'sale',
            'reference_number' => '12345',
            'amount' => '1.00',
            'currency' => 'USD',
            'signed_field_names' => 'access_key,profile_id,transaction_uuid,signed_date_time,locale,transaction_type,reference_number,amount,currency',
            'unsigned_field_names' => '',
        ];

        $signature = $this->generateSignature($testParams);

        return [
            'merchant_id' => $this->merchantId,
            'access_key' => $this->accessKey,
            'profile_id' => $this->profileId,
            'secret_key_length' => strlen($this->secretKey),
            'secret_key_valid_hex' => ctype_xdigit($this->secretKey),
            'environment' => $this->environment,
            'action_url' => $this->getActionUrl(),
            'test_signature' => $signature,
            'validation_errors' => $this->validateConfiguration(),
        ];
    }


    public function verifyKeysAndTimestamp(): array
    {
        $issues = [];
        $warnings = [];

        Log::info('=== CyberSource Key & Timestamp Verification ===');

        // 1. VERIFY ACCESS KEY FORMAT
        $accessKeyLength = strlen($this->accessKey);
        $accessKeyPattern = '/^[a-f0-9]{32}$/'; // Should be 32 hex characters

        Log::info('Access Key Verification', [
            'length' => $accessKeyLength,
            'expected_length' => 32,
            'first_10_chars' => substr($this->accessKey, 0, 10),
            'last_10_chars' => substr($this->accessKey, -10),
            'contains_spaces' => strpos($this->accessKey, ' ') !== false,
            'contains_newlines' => strpos($this->accessKey, "\n") !== false || strpos($this->accessKey, "\r") !== false,
            'is_hex_format' => preg_match($accessKeyPattern, $this->accessKey)
        ]);

        if ($accessKeyLength !== 32) {
            $issues[] = "Access Key length is {$accessKeyLength}, should be 32 characters";
        }

        if (!preg_match($accessKeyPattern, $this->accessKey)) {
            $issues[] = "Access Key format is invalid (should be 32 hex characters)";
        }

        if (strpos($this->accessKey, ' ') !== false || strpos($this->accessKey, "\n") !== false) {
            $issues[] = "Access Key contains spaces or newlines (word-wrapped)";
        }

        // 2. VERIFY SECRET KEY FORMAT
        $secretKeyLength = strlen($this->secretKey);
        $secretKeyPattern = '/^[a-f0-9]+$/'; // Should be hex characters only

        Log::info('Secret Key Verification', [
            'length' => $secretKeyLength,
            'expected_length' => 256,
            'first_20_chars' => substr($this->secretKey, 0, 20),
            'last_20_chars' => substr($this->secretKey, -20),
            'contains_spaces' => strpos($this->secretKey, ' ') !== false,
            'contains_newlines' => strpos($this->secretKey, "\n") !== false || strpos($this->secretKey, "\r") !== false,
            'is_hex_format' => preg_match($secretKeyPattern, $this->secretKey)
        ]);

        if ($secretKeyLength !== 256) {
            $issues[] = "Secret Key length is {$secretKeyLength}, should be 256 characters";
        }

        if (!preg_match($secretKeyPattern, $this->secretKey)) {
            $issues[] = "Secret Key format is invalid (should be 256 hex characters)";
        }

        if (strpos($this->secretKey, ' ') !== false || strpos($this->secretKey, "\n") !== false) {
            $issues[] = "Secret Key contains spaces or newlines (word-wrapped)";
        }

        // 3. VERIFY TIMESTAMP SYNCHRONIZATION
        $serverTime = time();
        $serverTimeFormatted = gmdate('Y-m-d\TH:i:s\Z', $serverTime);
        $currentUtcTime = new DateTime('now', new DateTimeZone('UTC'));

        // Check if server time is within 5 minutes of actual UTC time
        $actualUtcTime = new DateTime('now', new DateTimeZone('UTC'));
        $timeDifference = abs($serverTime - $actualUtcTime->getTimestamp());

        Log::info('Timestamp Verification', [
            'server_timestamp' => $serverTime,
            'server_formatted' => $serverTimeFormatted,
            'server_timezone' => date_default_timezone_get(),
            'actual_utc_time' => $actualUtcTime->format('Y-m-d\TH:i:s\Z'),
            'time_difference_seconds' => $timeDifference,
            'is_synchronized' => $timeDifference < 300, // Within 5 minutes
            'php_version' => PHP_VERSION,
            'system_time' => exec('date') // Unix/Linux systems only
        ]);

        if ($timeDifference > 300) { // More than 5 minutes off
            $issues[] = "Server time is {$timeDifference} seconds off from UTC (should be within 5 minutes)";
        }

        if ($timeDifference > 60) { // More than 1 minute off
            $warnings[] = "Server time is {$timeDifference} seconds off from UTC (consider NTP sync)";
        }

        // 4. TEST KEY VALIDATION WITH CYBERSOURCE
        $testSignature = $this->generateTestSignature();

        Log::info('Test Signature Generation', [
            'test_data' => 'test_field=test_value',
            'generated_signature' => $testSignature,
            'secret_key_usable' => !empty($testSignature)
        ]);

        // 5. VERIFY ENVIRONMENT CONFIGURATION
        Log::info('Environment Configuration', [
            'merchant_id' => $this->merchantId,
            'profile_id' => $this->profileId,
            'environment' => $this->environment,
            'action_url' => $this->getActionUrl(),
            'config_loaded_correctly' => !empty($this->merchantId) && !empty($this->profileId)
        ]);

        if (empty($this->merchantId) || empty($this->accessKey) || empty($this->secretKey) || empty($this->profileId)) {
            $issues[] = "One or more configuration values are empty/missing";
        }

        // 6. SUMMARY
        $result = [
            'status' => empty($issues) ? 'PASS' : 'FAIL',
            'issues' => $issues,
            'warnings' => $warnings,
            'verification_details' => [
                'access_key_length' => $accessKeyLength,
                'secret_key_length' => $secretKeyLength,
                'time_sync_status' => $timeDifference < 300 ? 'SYNCHRONIZED' : 'OUT_OF_SYNC',
                'time_difference_seconds' => $timeDifference
            ]
        ];

        Log::info('=== Verification Summary ===', $result);

        return $result;
    }

    /**
     * Generate a test signature to verify secret key is working
     */
    private function generateTestSignature(): string
    {
        try {
            $testData = 'test_field=test_value';
            $secretKey = hex2bin($this->secretKey);

            if ($secretKey === false) {
                Log::error('Secret key hex2bin conversion failed');
                return '';
            }

            return base64_encode(hash_hmac('sha256', $testData, $secretKey, true));
        } catch (Exception $e) {
            Log::error('Test signature generation failed', ['error' => $e->getMessage()]);
            return '';
        }
    }

    /**
     * Method to clean keys if they're word-wrapped
     */
    public function cleanKeys(): array
    {
        $originalAccessKey = $this->accessKey;
        $originalSecretKey = $this->secretKey;

        // Remove all whitespace, newlines, carriage returns
        $cleanedAccessKey = preg_replace('/\s+/', '', $this->accessKey);
        $cleanedSecretKey = preg_replace('/\s+/', '', $this->secretKey);

        Log::info('Key Cleaning Results', [
            'access_key_changed' => $originalAccessKey !== $cleanedAccessKey,
            'secret_key_changed' => $originalSecretKey !== $cleanedSecretKey,
            'access_key_before_length' => strlen($originalAccessKey),
            'access_key_after_length' => strlen($cleanedAccessKey),
            'secret_key_before_length' => strlen($originalSecretKey),
            'secret_key_after_length' => strlen($cleanedSecretKey)
        ]);

        return [
            'access_key_original' => $originalAccessKey,
            'access_key_cleaned' => $cleanedAccessKey,
            'secret_key_original' => $originalSecretKey,
            'secret_key_cleaned' => $cleanedSecretKey,
            'keys_were_dirty' => ($originalAccessKey !== $cleanedAccessKey) || ($originalSecretKey !== $cleanedSecretKey)
        ];
    }

    /**
     * Advanced timestamp verification with multiple time sources
     */
    public function verifyTimestampAdvanced(): array
    {
        $results = [];

        // 1. System time
        $systemTime = time();
        $results['system_time'] = [
            'timestamp' => $systemTime,
            'formatted' => gmdate('Y-m-d\TH:i:s\Z', $systemTime),
            'timezone' => date_default_timezone_get()
        ];

        // 2. PHP DateTime UTC
        $phpUtc = new DateTime('now', new DateTimeZone('UTC'));
        $results['php_utc'] = [
            'timestamp' => $phpUtc->getTimestamp(),
            'formatted' => $phpUtc->format('Y-m-d\TH:i:s\Z')
        ];

        // 3. Check if we can get system time via shell
        if (function_exists('exec')) {
            $systemDate = exec('date -u +"%Y-%m-%dT%H:%M:%SZ" 2>/dev/null');
            $results['shell_utc'] = [
                'available' => !empty($systemDate),
                'formatted' => $systemDate ?: 'N/A'
            ];
        }

        // 4. Calculate differences
        $differences = [];
        if (isset($results['php_utc'])) {
            $differences['system_vs_php'] = abs($systemTime - $phpUtc->getTimestamp());
        }

        $results['differences'] = $differences;
        $results['synchronized'] = max($differences) < 60; // Within 1 minute

        Log::info('Advanced Timestamp Verification', $results);

        return $results;
    }
}
