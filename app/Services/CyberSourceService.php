<?php

namespace App\Services;

use App\Models\Contribution;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CyberSourceService
{
    private $merchantId;
    private $accessKey;
    private $secretKey;
    private $environment;
    private $profileId;

    public function __construct()
    {
        $this->merchantId = config('services.cybersource.merchant_id');
        $this->accessKey = config('services.cybersource.access_key');
        $this->secretKey = config('services.cybersource.secret_key');
        $this->environment = config('services.cybersource.environment', 'test');
        $this->profileId = config('services.cybersource.profile_id');
    }

    /**
     * Generate CyberSource Secure Acceptance payment form data
     */
    public function generatePaymentFormData(Contribution $contribution): array
    {
        $requestId = $this->generateRequestId();
        $timestamp = gmdate('Y-m-d\TH:i:s\Z');

        // Update contribution with request ID
        $contribution->update(['cybersource_request_id' => $requestId]);

        // Use ngrok or production URLs
        $successUrl = env('CYBERSOURCE_SUCCESS_URL', 'https://9030-102-213-251-139.ngrok-free.app/donate/success');
        $cancelUrl = env('CYBERSOURCE_CANCEL_URL', 'https://9030-102-213-251-139.ngrok-free.app/donate/cancel');
        $errorUrl = env('CYBERSOURCE_ERROR_URL', 'https://9030-102-213-251-139.ngrok-free.app/donate/error');

        // Basic required fields
        $params = [
            'access_key' => $this->accessKey,
            'profile_id' => $this->profileId,
            'transaction_uuid' => $requestId,
            'signed_date_time' => $timestamp,
            'locale' => 'en',
            'transaction_type' => 'sale',
            'reference_number' => strval($contribution->id),
            'amount' => number_format($contribution->amount, 2, '.', ''),
            'currency' => strtoupper($contribution->currency),
            'payment_method' => 'card',
            'bill_to_forename' => 'Donor',
            'bill_to_email' => $contribution->email,
            'override_custom_receipt_page' => $successUrl,
            'override_custom_cancel_page' => $cancelUrl,
            'override_custom_error_page' => $errorUrl,
        ];

        // Add phone if available
        if (!empty($contribution->phone)) {
            $params['bill_to_phone'] = $contribution->phone;
        }

        // Define signed fields
        $signedFields = array_keys($params);
        $signedFields = array_diff($signedFields, ['signed_field_names', 'unsigned_field_names', 'signature']);

        $params['signed_field_names'] = implode(',', $signedFields);
        $params['unsigned_field_names'] = 'card_type,card_number,card_expiry_date';

        // Generate signature
        $params['signature'] = $this->generateSignature($params);

        // Debug logging
        Log::info('CyberSource Payment Form Data', [
            'signed_fields' => $params['signed_field_names'],
            'unsigned_fields' => $params['unsigned_field_names'],
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'error_url' => $errorUrl,
            'signature' => $params['signature'],
        ]);

        return [
            'action_url' => $this->getActionUrl(),
            'params' => $params,
        ];
    }

    /**
     * Test method with minimal required fields only
     */
    public function generateMinimalPaymentFormData(Contribution $contribution): array
    {
        $requestId = $this->generateRequestId();
        $timestamp = gmdate('Y-m-d\TH:i:s\Z');

        // Update contribution with request ID
        $contribution->update(['cybersource_request_id' => $requestId]);

        $params = [
            'access_key' => $this->accessKey,
            'profile_id' => $this->profileId,
            'transaction_uuid' => $requestId,
            'signed_date_time' => $timestamp,
            'locale' => 'en',
            'transaction_type' => 'sale',
            'reference_number' => strval($contribution->id),
            'amount' => number_format($contribution->amount, 2, '.', ''),
            'currency' => strtoupper($contribution->currency),
            'payment_method' => 'card',
            'bill_to_email' => $contribution->email,
            'bill_to_phone' => $contribution->phone ?? '',

            // Updated signed field names to include billing fields
            'signed_field_names' => 'access_key,profile_id,transaction_uuid,signed_date_time,locale,transaction_type,reference_number,amount,currency,payment_method,bill_to_email,bill_to_phone',
            'unsigned_field_names' => 'card_type,card_number,card_expiry_date',
        ];

        // Generate signature AFTER all fields are set
        $params['signature'] = $this->generateSignature($params);

        // Enhanced debug logging
        Log::info('CyberSource Comprehensive Test', [
            'params' => $params,
            'signature_length' => strlen($params['signature']),
            'profile_id' => $this->profileId,
            'merchant_id' => $this->merchantId,
            'environment' => $this->environment,
            'timestamp' => $timestamp,
            'signed_fields_count' => count(explode(',', $params['signed_field_names']))
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
            $contribution->transactions()->create([
                'transaction_id' => Str::uuid(),
                'gateway_transaction_id' => $data['transaction_id'] ?? null,
                'gateway' => 'cybersource',
                'type' => 'payment',
                'status' => $status,
                'amount' => $contribution->amount,
                'currency' => $contribution->currency,
                'gateway_response' => $data,
                'processed_at' => now(),
            ]);

            Log::info('CyberSource webhook processed successfully', [
                'contribution_id' => $contribution->id,
                'status' => $status,
                'transaction_id' => $data['transaction_id'] ?? null,
            ]);

            return [
                'success' => true,
                'contribution' => $contribution,
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

        foreach ($signedFieldNames as $field) {
            $field = trim($field);

            if (in_array($field, ['signed_field_names', 'unsigned_field_names', 'signature'])) {
                continue;
            }

            if (isset($params[$field])) {
                $dataToSign[] = $field . '=' . $params[$field];
            }
        }

        $dataString = implode(',', $dataToSign);

        // Decode the hex secret key
        $secretKey = hex2bin($this->secretKey);

        // Debug logging
        Log::info('CyberSource Signature Debug', [
            'signed_field_names' => $params['signed_field_names'],
            'signed_fields_array' => $signedFieldNames,
            'data_to_sign' => $dataString,
            'signature_fields_count' => count($dataToSign),
            'secret_key_length' => strlen($this->secretKey),
            'secret_key_start' => substr($this->secretKey, 0, 10) . '...'
        ]);

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
}
