<?php

namespace App\Services;

use App\Models\Contribution;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CyberSourceService
{
    private $profileId;
    private $accessKey;
    private $secretKey;
    private $testMode;
    private $gatewayUrl;
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->profileId = config('services.cybersource.profile_id');
        $this->accessKey = config('services.cybersource.access_key');
        $this->secretKey = config('services.cybersource.secret_key');
        $this->testMode = config('services.cybersource.test_mode', true);
        $this->gatewayUrl = $this->testMode
            ? config('services.cybersource.test_url')
            : config('services.cybersource.live_url');

        $this->walletService = $walletService;
    }

    /**
     * Validate CyberSource configuration
     */
    public function validateConfiguration(): array
    {
        $errors = [];

        if (empty($this->profileId)) {
            $errors[] = 'CyberSource Profile ID is not configured';
        }

        if (empty($this->accessKey)) {
            $errors[] = 'CyberSource Access Key is not configured';
        }

        if (empty($this->secretKey)) {
            $errors[] = 'CyberSource Secret Key is not configured';
        }

        if (empty($this->gatewayUrl)) {
            $errors[] = 'CyberSource Gateway URL is not configured';
        }

        return $errors;
    }

    /**
     * Generate payment data for CyberSource Hosted Checkout
     */
    public function generatePaymentData(Contribution $contribution): array
    {
        $transactionUuid = Str::uuid()->toString();
        $signedDateTime = Carbon::now('UTC')->format('Y-m-d\TH:i:s\Z');

        // Update contribution with CyberSource data
        $contribution->update([
            'cybersource_transaction_uuid' => $transactionUuid,
            'cybersource_reference_number' => $contribution->id,
            'cybersource_signed_date_time' => $signedDateTime,
        ]);

        // Prepare request fields
        $fields = [
            'access_key' => $this->accessKey,
            'profile_id' => $this->profileId,
            'transaction_uuid' => $transactionUuid,
            'signed_date_time' => $signedDateTime,
            'locale' => 'en-us',
            'transaction_type' => 'sale',
            'reference_number' => $contribution->id,
            'amount' => number_format($contribution->amount, 2, '.', ''),
            'currency' => strtolower($contribution->currency),

            // Customer billing information
            'bill_to_forename' => $contribution->bill_to_forename ?? '',
            'bill_to_surname' => $contribution->bill_to_surname ?? '',
            'bill_to_email' => $contribution->email,
            'bill_to_phone' => $contribution->phone ?? '',
            'bill_to_address_line1' => $contribution->bill_to_address_line1 ?? '',
            'bill_to_address_city' => $contribution->bill_to_address_city ?? '',
            'bill_to_address_state' => $contribution->bill_to_address_state ?? '',
            'bill_to_address_postal_code' => $contribution->bill_to_address_postal_code ?? '',
            'bill_to_address_country' => $contribution->bill_to_address_country ?? 'KE',


            // Merchant defined data
            'merchant_defined_data1' => $contribution->donationLink->code,
            'merchant_defined_data2' => $contribution->donation_type,

            // Payment method
            'payment_method' => 'card',
        ];

        // Define signed and unsigned fields
        $signedFieldNames = [
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
            'bill_to_forename',
            'bill_to_surname',
            'bill_to_email',
            'bill_to_phone',
            'bill_to_address_line1',
            'bill_to_address_city',
            'bill_to_address_state',
            'bill_to_address_postal_code',
            'bill_to_address_country',
            'merchant_defined_data1',
            'merchant_defined_data2',
            'payment_method'
        ];

        $unsignedFieldNames = [];

        $fields['signed_field_names'] = implode(',', $signedFieldNames);
        $fields['unsigned_field_names'] = implode(',', $unsignedFieldNames);

        // Generate signature
        $signature = $this->generateSignature($fields, $signedFieldNames);
        $fields['signature'] = $signature;

        // Store signature data in contribution
        $contribution->update([
            'cybersource_signed_field_names' => $fields['signed_field_names'],
            'cybersource_signature' => $signature,
        ]);

        return [
            'gateway_url' => $this->gatewayUrl,
            'fields' => $fields
        ];
    }

    /**
     * Generate HMAC-SHA256 signature
     */
    private function generateSignature(array $fields, array $signedFieldNames): string
    {
        $signedFieldsString = '';
        foreach ($signedFieldNames as $fieldName) {
            $signedFieldsString .= $fieldName . '=' . ($fields[$fieldName] ?? '') . ',';
        }

        // Remove trailing comma
        $signedFieldsString = rtrim($signedFieldsString, ',');

        Log::info('CyberSource signature data', [
            'signed_fields_string' => $signedFieldsString,
            'secret_key_length' => strlen($this->secretKey)
        ]);

        return base64_encode(hash_hmac('sha256', $signedFieldsString, $this->secretKey, true));
    }

    /**
     * Verify response signature
     */
    public function verifyResponseSignature(array $responseData): bool
    {
        if (!isset($responseData['signature']) || !isset($responseData['signed_field_names'])) {
            Log::warning('CyberSource response missing signature or signed_field_names');
            return false;
        }

        $signedFieldNames = explode(',', $responseData['signed_field_names']);
        $expectedSignature = $this->generateSignature($responseData, $signedFieldNames);

        $isValid = hash_equals($expectedSignature, $responseData['signature']);

        Log::info('CyberSource signature verification', [
            'expected' => $expectedSignature,
            'received' => $responseData['signature'],
            'is_valid' => $isValid
        ]);

        return $isValid;
    }

    /**
     * Process callback response from CyberSource
     */
    public function processCallback(array $responseData): array
    {
        // Verify signature first
        if (!$this->verifyResponseSignature($responseData)) {
            Log::error('CyberSource signature verification failed', [
                'received_signature' => $responseData['signature'] ?? 'missing',
                'signed_field_names' => $responseData['signed_field_names'] ?? 'missing'
            ]);
            throw new \Exception('Invalid signature from CyberSource - possible security threat');
        }

        // Extract response data
        $referenceNumber = $responseData['req_reference_number'] ?? null;
        $transactionUuid = $responseData['req_transaction_uuid'] ?? null;
        $decision = $responseData['decision'] ?? null;
        $reasonCode = $responseData['reason_code'] ?? null;
        $authCode = $responseData['auth_code'] ?? null;
        $requestId = $responseData['request_id'] ?? null;
        $transactionId = $responseData['transaction_id'] ?? null;

        // Extract currency and amount from callback
        $callbackCurrency = strtoupper($responseData['req_currency'] ?? '');
        $callbackAmount = $responseData['req_amount'] ?? $responseData['auth_amount'] ?? null;

        // Validate required fields
        if (!$referenceNumber || !$transactionUuid || !$decision) {
            throw new \Exception('Missing required fields in CyberSource response');
        }

        // Find the contribution
        $contribution = Contribution::where('id', $referenceNumber)
            ->where('cybersource_transaction_uuid', $transactionUuid)
            ->first();

        if (!$contribution) {
            throw new \Exception("Contribution not found for reference: {$referenceNumber}, UUID: {$transactionUuid}");
        }

        // Verify currency matches (security check)
        if ($callbackCurrency && $contribution->currency !== $callbackCurrency) {
            Log::warning('Currency mismatch between contribution and callback', [
                'contribution_id' => $contribution->id,
                'contribution_currency' => $contribution->currency,
                'callback_currency' => $callbackCurrency,
            ]);

            // Update contribution currency if callback has valid currency
            if (in_array($callbackCurrency, ['KES', 'USD'])) {
                Log::info('Updating contribution currency from callback', [
                    'contribution_id' => $contribution->id,
                    'old_currency' => $contribution->currency,
                    'new_currency' => $callbackCurrency,
                ]);
                $contribution->currency = $callbackCurrency;
            }
        }

        // Verify amount matches (allow small discrepancies due to formatting)
        if ($callbackAmount && abs($contribution->amount - floatval($callbackAmount)) > 0.01) {
            Log::warning('Amount mismatch between contribution and callback', [
                'contribution_id' => $contribution->id,
                'contribution_amount' => $contribution->amount,
                'callback_amount' => $callbackAmount,
                'difference' => abs($contribution->amount - floatval($callbackAmount)),
            ]);
        }

        // Check if already processed (idempotency)
        if ($contribution->cybersource_request_id === $requestId && $requestId) {
            Log::info('CyberSource callback already processed', [
                'contribution_id' => $contribution->id,
                'request_id' => $requestId
            ]);

            return [
                'success' => $contribution->payment_status === Contribution::STATUS_COMPLETED,
                'decision' => $contribution->cybersource_decision,
                'reason_code' => $contribution->cybersource_reason_code,
                'contribution' => $contribution,
                'already_processed' => true
            ];
        }

        // Calculate platform fee before marking contribution as completed
        if (!$contribution->hasPlatformFeeCalculated()) {
            $contribution->refresh();
            $contribution->calculatePlatformFee();
            $contribution->save();

            Log::info('Platform fee calculated for CyberSource contribution', [
                'contribution_id' => $contribution->id,
                'currency' => $contribution->currency,
                'amount' => $contribution->amount,
                'platform_fee' => $contribution->platform_fee,
                'net_amount' => $contribution->net_amount,
            ]);
        }

        // Update contribution with response data
        $contribution->update([
            'cybersource_request_id' => $requestId,
            'cybersource_transaction_id' => $transactionId,
            'cybersource_auth_code' => $authCode,
            'cybersource_decision' => $decision,
            'cybersource_reason_code' => $reasonCode,
            'payment_response' => $responseData,
            'payment_status' => $this->mapDecisionToStatus($decision),
            'platform_fee' => $contribution->platform_fee,
            'net_amount' => $contribution->net_amount,
            'platform_fee_percentage' => $contribution->platform_fee_percentage,
            'processed_at' => in_array($decision, ['ACCEPT', 'REVIEW']) ? now() : null,
        ]);

        // Create transaction record
        $contribution->transactions()->create([
            'transaction_id' => $transactionId ?: "pending-{$contribution->id}",
            'gateway_transaction_id' => $transactionId,
            'cybersource_transaction_uuid' => $transactionUuid,
            'cybersource_reference_number' => $referenceNumber,
            'cybersource_auth_code' => $authCode,
            'cybersource_decision' => $decision,
            'cybersource_reason_code' => $reasonCode,
            'gateway' => Transaction::GATEWAY_CYBERSOURCE,
            'type' => Transaction::TYPE_PAYMENT,
            'status' => $this->mapDecisionToTransactionStatus($decision),
            'amount' => $contribution->amount,
            'currency' => $contribution->currency,
            'gateway_response' => $responseData,
            'processed_at' => now(),
            'notes' => $this->getTransactionNotes($decision, $reasonCode),
        ]);

        // Refresh contribution to get latest wallet_credited status
        $contribution->refresh();

        // Credit wallet only if not already credited and payment is successful
        if (!$contribution->wallet_credited && $decision === 'ACCEPT') {
            try {
                $this->walletService->creditFromDonation($contribution);

                Log::info('Wallet credited for CyberSource contribution', [
                    'contribution_id' => $contribution->id,
                    'currency' => $contribution->currency,
                    'net_amount_original' => $contribution->getNetAmount(),
                    'net_amount_kes' => $contribution->getNetAmountInKes(),
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to credit wallet for CyberSource contribution', [
                    'contribution_id' => $contribution->id,
                    'error' => $e->getMessage(),
                ]);
                // Don't throw exception as payment was successful
            }
        } elseif ($contribution->wallet_credited) {
            Log::info('Wallet already credited for CyberSource contribution via callback', [
                'contribution_id' => $contribution->id,
            ]);
        }

        // Log the transaction result with currency information
        Log::info('CyberSource callback processed with currency handling', [
            'contribution_id' => $contribution->id,
            'decision' => $decision,
            'reason_code' => $reasonCode,
            'transaction_id' => $transactionId,
            'auth_code' => $authCode,
            'currency' => $contribution->currency,
            'amount' => $contribution->amount,
            'platform_fee' => $contribution->platform_fee,
            'net_amount' => $contribution->net_amount,
            'net_amount_kes' => $decision === 'ACCEPT' ? $contribution->getNetAmountInKes() : null,
        ]);

        return [
            'success' => $decision === 'ACCEPT',
            'decision' => $decision,
            'reason_code' => $reasonCode,
            'contribution' => $contribution,
            'already_processed' => false
        ];
    }

    /**
     * Generate transaction notes based on decision and reason code
     */
    private function getTransactionNotes(string $decision, ?string $reasonCode): ?string
    {
        if ($decision === 'ACCEPT') {
            return 'Payment successfully authorized and captured';
        }

        if ($decision === 'REVIEW') {
            return 'Payment under review for security verification';
        }

        if ($decision === 'CANCEL') {
            return 'Payment cancelled by customer';
        }

        // For declines, provide detailed reason
        $declineReasons = [
            '102' => 'Invalid field data provided',
            '200' => 'Declined due to risk factors',
            '201' => 'Issuing bank inquiry required',
            '202' => 'Expired credit card',
            '203' => 'General decline by issuing bank',
            '204' => 'Insufficient funds',
            '205' => 'Stolen or lost card',
            '207' => 'Issuing bank unavailable',
            '208' => 'Inactive card or not authorized for CNP',
            '210' => 'Credit limit exceeded',
            '211' => 'Invalid card verification number',
            '221' => 'Customer matched negative file',
            '230' => 'Risk decline by processor',
            '231' => 'Invalid CVN',
            '232' => 'Invalid card type',
            '233' => 'General decline by processor',
            '234' => 'Merchant configuration problem',
            '236' => 'Processor system failure',
            '240' => 'Card type mismatch',
            '475' => 'Cardholder enrolled for payer authentication',
            '476' => 'Payer authentication failed',
        ];

        $reason = $declineReasons[$reasonCode] ?? "Unknown decline reason (code: {$reasonCode})";
        return "Payment declined: {$reason}";
    }

    /**
     * Map CyberSource decision to contribution status
     */
    private function mapDecisionToStatus(string $decision): string
    {
        return match ($decision) {
            'ACCEPT' => Contribution::STATUS_COMPLETED,
            'DECLINE' => Contribution::STATUS_FAILED,
            'CANCEL' => Contribution::STATUS_CANCELLED,
            'ERROR' => Contribution::STATUS_FAILED,
            'REVIEW' => Contribution::STATUS_PROCESSING,
            default => Contribution::STATUS_FAILED,
        };
    }

    /**
     * Map CyberSource decision to transaction status
     */
    private function mapDecisionToTransactionStatus(string $decision): string
    {
        return match ($decision) {
            'ACCEPT' => Transaction::STATUS_COMPLETED,
            'DECLINE' => Transaction::STATUS_DECLINED,
            'CANCEL' => Transaction::STATUS_CANCELLED,
            'ERROR' => Transaction::STATUS_FAILED,
            'REVIEW' => Transaction::STATUS_REVIEW,
            default => Transaction::STATUS_FAILED,
        };
    }
}
