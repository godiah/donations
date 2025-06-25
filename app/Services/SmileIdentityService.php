<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SmileIdentityService
{
    private string $baseUrl;
    private string $partnerId;
    private string $apiKey;
    private string $environment;
    private string $callbackUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.smile_identity.base_url');
        $this->partnerId = config('services.smile_identity.partner_id');
        $this->apiKey = config('services.smile_identity.api_key');
        $this->environment = config('services.smile_identity.environment', 'sandbox');
        $this->callbackUrl = config('services.smile_identity.callback_url');
    }

    /**
     * Perform Basic KYC verification (Synchronous for real-time verification)
     *
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function performBasicKyc(array $data): array
    {
        try {
            $timestamp = Carbon::now()->toISOString();
            $jobId = $this->generateJobId();
            $userId = $this->generateUserId($data);

            $payload = [
                'source_sdk' => 'rest_api',
                'source_sdk_version' => '2.0.0',
                'partner_id' => $this->partnerId,
                'timestamp' => $timestamp,
                'country' => $data['country'] ?? 'KE',
                'id_type' => $this->mapIdType($data['id_type']),
                'id_number' => $data['id_number'],
                'first_name' => $data['first_name'],
                'middle_name' => $data['middle_name'] ?? '',
                'last_name' => $data['last_name'],
                'phone_number' => $data['phone_number'] ?? '',
                'partner_params' => [
                    'job_id' => $jobId,
                    'user_id' => $userId,
                ]
            ];

            // Generate signature for authentication
            $payload['signature'] = $this->generateSignature($timestamp);

            // Use synchronous endpoint for real-time verification
            $endpoint = $this->baseUrl . 'verify';

            Log::info('Smile Identity KYC Request', [
                'endpoint' => $endpoint,
                'payload' => array_merge($payload, ['signature' => '[HIDDEN]'])
            ]);

            $response = Http::timeout(30)->post($endpoint, $payload);

            if (!$response->successful()) {
                $errorData = $response->json();
                Log::error('Smile Identity API request failed', [
                    'status' => $response->status(),
                    'response' => $errorData
                ]);

                throw new Exception('Smile Identity API request failed: ' .
                    ($errorData['ResultText'] ?? $response->body()));
            }

            $responseData = $response->json();

            Log::info('Smile Identity KYC Response', [
                'job_id' => $jobId,
                'response' => $responseData
            ]);

            return [
                'job_id' => $jobId,
                'user_id' => $userId,
                'response' => $responseData,
                'verified' => $this->evaluateVerificationResult($responseData)
            ];
        } catch (Exception $e) {
            Log::error('Smile Identity KYC Error', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);

            throw $e;
        }
    }

    /**
     * Evaluate if verification result is successful
     *
     * @param array $responseData
     * @return bool
     */
    private function evaluateVerificationResult(array $responseData): bool
    {
        // Check overall result code
        $resultCode = $responseData['ResultCode'] ?? null;

        // 1020 = Exact Match, 1021 = Partial Match (both considered verified)
        if (in_array($resultCode, ['1020', '1021'])) {
            return true;
        }

        // 1022 = No Match, 1013 = Invalid ID (not verified)
        if (in_array($resultCode, ['1022', '1013'])) {
            return false;
        }

        // For other cases, check individual field matches
        $actions = $responseData['Actions'] ?? [];

        // Require ID verification to be successful
        if (($actions['Verify_ID_Number'] ?? '') !== 'Verified') {
            return false;
        }

        // Check name matching - require at least partial match
        $nameMatch = $actions['Names'] ?? '';
        return in_array($nameMatch, ['Exact Match', 'Partial Match']);
    }

    /**
     * Process KYC callback response (for async operations)
     *
     * @param array $callbackData
     * @return array
     */
    public function processKycCallback(array $callbackData): array
    {
        $verificationResult = [
            'verified' => $this->evaluateVerificationResult($callbackData),
            'result_code' => $callbackData['ResultCode'] ?? null,
            'result_text' => $callbackData['ResultText'] ?? null,
            'smile_job_id' => $callbackData['SmileJobID'] ?? null,
            'partner_params' => $callbackData['PartnerParams'] ?? [],
            'actions' => $callbackData['Actions'] ?? [],
            'raw_response' => $callbackData
        ];

        return $verificationResult;
    }

    /**
     * Generate unique job ID
     *
     * @return string
     */
    private function generateJobId(): string
    {
        return 'kyc_' . uniqid() . '_' . time();
    }

    /**
     * Generate user ID based on provided data
     *
     * @param array $data
     * @return string
     */
    private function generateUserId(array $data): string
    {
        // Create a consistent user ID based on ID number and names
        $identifier = $data['id_number'] . '_' .
            strtolower($data['first_name']) . '_' .
            strtolower($data['last_name']);

        return 'user_' . hash('sha256', $identifier);
    }

    /**
     * Map internal ID types to Smile Identity ID types
     *
     * @param string $idType
     * @return string
     */
    private function mapIdType(string $idType): string
    {
        $mapping = [
            'NATIONAL_ID' => 'NATIONAL_ID',
            'PASSPORT' => 'PASSPORT',
            'ALIEN_ID' => 'ALIEN_CARD',
            'DRIVERS_LICENSE' => 'DRIVERS_LICENSE',
        ];

        return $mapping[$idType] ?? 'NATIONAL_ID';
    }

    /**
     * Generate signature for API authentication
     * Based on Smile Identity's signature requirements
     *
     * @param string $timestamp
     * @return string
     */
    private function generateSignature(string $timestamp): string
    {
        // Smile Identity signature format: timestamp + partner_id + "sid_request"
        $signatureString = $timestamp . $this->partnerId . "sid_request";

        // Generate HMAC-SHA256 signature using API key
        return hash_hmac('sha256', $signatureString, $this->apiKey);
    }

    /**
     * Check if service is properly configured
     *
     * @return bool
     */
    public function isConfigured(): bool
    {
        return !empty($this->baseUrl) &&
            !empty($this->partnerId) &&
            !empty($this->apiKey);
    }

    /**
     * Get detailed verification result for UI display
     *
     * @param array $responseData
     * @return array
     */
    public function getVerificationDetails(array $responseData): array
    {
        $actions = $responseData['Actions'] ?? [];

        return [
            'overall_result' => $responseData['ResultText'] ?? 'Unknown',
            'overall_code' => $responseData['ResultCode'] ?? 'Unknown',
            'id_verified' => ($actions['Verify_ID_Number'] ?? '') === 'Verified',
            'name_match' => $actions['Names'] ?? 'Not Checked',
            'dob_match' => $actions['DOB'] ?? 'Not Provided',
            'gender_match' => $actions['Gender'] ?? 'Not Provided',
            'phone_match' => $actions['Phone_Number'] ?? 'Not Provided',
            'smile_job_id' => $responseData['SmileJobID'] ?? null,
        ];
    }
}
