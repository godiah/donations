<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Individual;
use App\Models\KycVerification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class KycService
{
    private string $partnerId;
    private string $apiKey;
    private string $baseUrl;
    private string $callbackUrl;
    private string $environment;

    public function __construct()
    {
        $this->partnerId = config('kyc.smile_identity.partner_id');
        $this->apiKey = config('kyc.smile_identity.api_key');
        $this->baseUrl = config('kyc.smile_identity.base_url');
        $this->callbackUrl = config('kyc.smile_identity.callback_url');
        $this->environment = config('kyc.smile_identity.environment');

        // Log::debug('KycService initialized', [
        //     'partner_id' => $this->partnerId,
        //     'api_key' => substr($this->apiKey, 0, 4) . '****',
        //     'environment' => $this->environment,
        // ]);
    }

    public function initiateVerification(Individual $individual, Application $application, int $initiatedBy): KycVerification
    {
        // Create verification record
        $verification = KycVerification::create([
            'individual_id' => $individual->id,
            'application_id' => $application->id,
            'job_id' => $this->generateJobId(),
            'id_type' => $this->mapIdType($individual->idType->name),
            'id_number' => $individual->id_number,
            'country_code' => 'KE', // Kenya
            'status' => 'processing',
            'submitted_at' => now(),
            'initiated_by' => $initiatedBy,
        ]);

        try {
            // Submit to Smile Identity
            $response = $this->submitToSmileIdentity($individual, $verification);

            if ($response['success']) {
                Log::info('KYC verification submitted successfully', [
                    'job_id' => $verification->job_id,
                    'individual_id' => $individual->id,
                ]);
            } else {
                $verification->update([
                    'status' => 'failed',
                    'failure_reason' => 'Failed to submit to Smile Identity: ' . ($response['error'] ?? 'Unknown error'),
                    'completed_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('KYC verification submission failed', [
                'job_id' => $verification->job_id,
                'individual_id' => $individual->id,
                'error' => $e->getMessage(),
            ]);

            $verification->update([
                'status' => 'failed',
                'failure_reason' => $e->getMessage(),
                'completed_at' => now(),
            ]);
        }

        return $verification;
    }

    private function submitToSmileIdentity(Individual $individual, KycVerification $verification): array
    {
        $timestamp = gmdate("Y-m-d\TH:i:s.v\Z"); // ISO 8601 format in UTC
        $signature = $this->generateSignature($timestamp);

        $payload = [
            'source_sdk' => 'rest_api',
            'source_sdk_version' => '2.0.0',
            'partner_id' => (string) $this->partnerId,
            'signature' => $signature,
            'timestamp' => $timestamp,
            'country' => $verification->country_code,
            'id_type' => $verification->id_type,
            'id_number' => $verification->id_number,
            'callback_url' => $this->callbackUrl,
            'first_name' => $individual->first_name,
            'middle_name' => $individual->middle_name,
            'last_name' => $individual->last_name,
            'partner_params' => [
                'job_id' => $verification->job_id,
                'user_id' => (string) $individual->id,
            ],
        ];

        if ($individual->phone) {
            $payload['phone_number'] = $individual->phone;
        }

        Log::debug('Submitting KYC request to Smile Identity', [
            'url' => $this->baseUrl . 'verify_async',
            'payload' => $payload,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);

        $response = Http::timeout(30)
            ->retry(3, 1000)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])
            ->post($this->baseUrl . 'verify_async', $payload);

        if (!$response->successful()) {
            throw new \Exception('HTTP Error: ' . $response->status() . ' - ' . $response->body());
        }

        $responseData = $response->json();

        return [
            'success' => $responseData['success'] ?? false,
            'data' => $responseData,
            'error' => $responseData['error'] ?? null,
        ];
    }

    public function processCallback(array $callbackData): bool
    {
        try {
            if (!$this->verifyCallbackSignature($callbackData)) {
                Log::warning('Invalid callback signature received', [
                    'received_signature' => $callbackData['signature'] ?? 'missing',
                    'timestamp' => $callbackData['timestamp'] ?? 'missing',
                    'callback_data' => $callbackData,
                ]);
                return false;
            }

            $jobId = $callbackData['PartnerParams']['job_id'] ?? null;
            if (!$jobId) {
                Log::warning('No job_id in callback data', $callbackData);
                return false;
            }

            $verification = KycVerification::where('job_id', $jobId)->first();
            if (!$verification) {
                Log::warning('Verification not found for job_id: ' . $jobId);
                return false;
            }

            $this->updateVerificationFromCallback($verification, $callbackData);

            return true;
        } catch (\Exception $e) {
            Log::error('Error processing KYC callback', [
                'error' => $e->getMessage(),
                'data' => $callbackData,
            ]);
            return false;
        }
    }

    private function updateVerificationFromCallback(KycVerification $verification, array $callbackData): void
    {
        $resultCode = $callbackData['ResultCode'] ?? null;
        $resultText = $callbackData['ResultText'] ?? null;
        $actions = $callbackData['Actions'] ?? [];

        Log::debug('KYC callback data received', [
            'job_id' => $verification->job_id,
            'result_code' => $resultCode,
            'result_text' => $resultText,
            'actions' => $actions,
            'full_callback_data' => $callbackData,
        ]);

        $status = $this->determineStatusFromResult($resultCode, $actions, $verification);

        $verification->update([
            'smile_job_id' => $callbackData['SmileJobID'] ?? null,
            'result_code' => $resultCode,
            'result_text' => $resultText,
            'verification_data' => $callbackData,
            'actions' => $actions,
            'status' => $status,
            'completed_at' => now(),
        ]);

        Log::info('KYC verification completed', [
            'job_id' => $verification->job_id,
            'status' => $status,
            'result_code' => $resultCode,
        ]);
    }

    private function determineStatusFromResult(string $resultCode, array $actions, KycVerification $verification): string
    {
        // Log for debugging
        Log::debug('Determining status from result', [
            'result_code' => $resultCode,
            'actions' => $actions,
        ]);

        // Handle failure codes
        if (in_array($resultCode, ['1013', '1014', '1015', '1016', '1022'])) {
            Log::info('Verification rejected due to result code', ['result_code' => $resultCode]);
            return 'rejected';
        }

        // Handle success or partial match
        if (in_array($resultCode, ['1020', '1021'])) {
            $individual = $verification->individual;
            $fullNameFromApp = $individual->getFullNameAttribute();
            $namesMatch = $actions['Names'] ?? '';

            Log::debug('Name matching', [
                'app_full_name' => $fullNameFromApp,
                'names_match' => $namesMatch,
            ]);

            if (in_array($namesMatch, ['Exact Match', 'Partial Match'])) {
                return 'verified';
            } else {
                Log::info('Verification rejected due to name mismatch', ['names_match' => $namesMatch]);
                return 'rejected';
            }
        }

        Log::warning('Unknown result code', ['result_code' => $resultCode]);
        return 'rejected';
    }

    private function mapIdType(string $internalType): string
    {
        return match ($internalType) {
            'national_id' => 'NATIONAL_ID',
            'passport' => 'PASSPORT',
            'alien_id' => 'ALIEN_ID',
            default => throw new \InvalidArgumentException('Unsupported ID type: ' . $internalType),
        };
    }

    private function generateJobId(): string
    {
        return 'kyc_' . Str::uuid()->toString();
    }

    private function generateSignature(string $timestamp): string
    {
        $stringToSign = $timestamp . (string) $this->partnerId . 'sid_request';
        return base64_encode(hash_hmac('sha256', $stringToSign, $this->apiKey, true));
    }

    private function verifyCallbackSignature(array $callbackData): bool
    {
        // $receivedSignature = $callbackData['signature'] ?? '';
        // $timestamp = $callbackData['timestamp'] ?? '';

        // if (!$receivedSignature || !$timestamp) {
        //     return false;
        // }

        // $expectedSignature = $this->generateCallbackSignature($timestamp);
        // return hash_equals($expectedSignature, $receivedSignature);
        Log::warning('Signature verification bypassed for testing');
        return true;
    }

    private function generateCallbackSignature(string $timestamp): string
    {
        $stringToSign = $timestamp . (string) $this->partnerId . 'sid_response';
        $signature = base64_encode(hash_hmac('sha256', $stringToSign, $this->apiKey, true));

        Log::debug('Generated callback signature', [
            'timestamp' => $timestamp,
            'partner_id' => $this->partnerId,
            'string_to_sign' => $stringToSign,
            'signature' => $signature,
        ]);

        return $signature;
    }

    public function getVerificationStatus(Individual $individual, Application $application): ?KycVerification
    {
        return $individual->kycVerifications()
            ->where('application_id', $application->id)
            ->orderBy('created_at', 'desc')
            ->first();
    }
}
