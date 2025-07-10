<?php

namespace App\Services;

use App\Models\Contribution;
use App\Models\Transaction;
use App\Models\PayoutMethod;
use App\Services\WalletService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MpesaService
{
    protected $config;
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->config = config('mpesa');
        $this->walletService = $walletService;
    }

    /**
     * Get the current environment configuration
     */
    protected function getEnvironmentConfig(): array
    {
        $environment = $this->config['default_environment'];
        return $this->config[$environment] ?? [];
    }

    /**
     * Get the base URL for the current environment
     */
    protected function getBaseUrl(): string
    {
        $envConfig = $this->getEnvironmentConfig();
        return $envConfig['base_url'] ?? '';
    }

    /**
     * Get OAuth URL
     */
    protected function getOAuthUrl(): string
    {
        return $this->getBaseUrl() . '/oauth/v1/generate?grant_type=client_credentials';
    }

    /**
     * Get STK Push URL
     */
    protected function getStkPushUrl(): string
    {
        return $this->getBaseUrl() . '/mpesa/stkpush/v1/processrequest';
    }

    /**
     * Get STK Push Query URL
     */
    protected function getStkPushQueryUrl(): string
    {
        return $this->getBaseUrl() . '/mpesa/stkpushquery/v1/query';
    }

    /**
     * Generate STK Push password
     */
    protected function generateStkPushPassword(string $timestamp): string
    {
        $envConfig = $this->getEnvironmentConfig();
        $businessShortCode = $envConfig['business_short_code'];
        $passkey = $envConfig['lipa_na_mpesa_passkey'];

        return base64_encode($businessShortCode . $passkey . $timestamp);
    }

    /**
     * Get authorization header for API calls
     */
    protected function getAuthorizationHeader(): string
    {
        $envConfig = $this->getEnvironmentConfig();
        $credentials = base64_encode($envConfig['consumer_key'] . ':' . $envConfig['consumer_secret']);
        return 'Basic ' . $credentials;
    }

    /**
     * Validate M-Pesa configuration
     */
    public function validateConfiguration(): array
    {
        $errors = [];
        $envConfig = $this->getEnvironmentConfig();

        if (empty($envConfig)) {
            $errors[] = 'M-Pesa environment configuration not found';
            return $errors;
        }

        $requiredFields = [
            'consumer_key' => 'Consumer key is required',
            'consumer_secret' => 'Consumer secret is required',
            'business_short_code' => 'Business short code is required',
            'lipa_na_mpesa_passkey' => 'Lipa Na M-Pesa passkey is required',
        ];

        foreach ($requiredFields as $field => $message) {
            if (empty($envConfig[$field])) {
                $errors[] = $message;
            }
        }

        // Validate callback URLs
        $callbackUrls = $this->config['callback_urls'] ?? [];
        if (empty($callbackUrls['stk_push_callback'])) {
            $errors[] = 'STK Push callback URL is required';
        }

        return $errors;
    }

    /**
     * Process STK Push payment
     */
    public function processStkPush(Contribution $contribution): array
    {
        try {
            $envConfig = $this->getEnvironmentConfig();

            // Validate configuration
            $configErrors = $this->validateConfiguration();
            if (!empty($configErrors)) {
                throw new \Exception('M-Pesa configuration errors: ' . implode(', ', $configErrors));
            }

            // Get access token
            $accessToken = $this->getAccessToken();
            if (!$accessToken) {
                throw new \Exception('Failed to get M-Pesa access token');
            }

            // Prepare STK Push request
            $timestamp = Carbon::now()->format('YmdHis');
            $password = $this->generateStkPushPassword($timestamp);

            // Format phone number
            $phoneNumber = $this->formatPhoneNumber($contribution->phone);

            // Validate transaction amount
            $this->validateTransactionAmount($contribution->amount);

            $requestData = [
                'BusinessShortCode' => $envConfig['business_short_code'],
                'Password' => $password,
                'Timestamp' => $timestamp,
                'TransactionType' => 'CustomerPayBillOnline',
                'Amount' => (int) $contribution->amount,
                'PartyA' => $phoneNumber,
                'PartyB' => $envConfig['business_short_code'],
                'PhoneNumber' => $phoneNumber,
                'CallBackURL' => $this->getCallbackUrl(),
                'AccountReference' => 'DON_' . $contribution->id,
                'TransactionDesc' => 'Donation payment for contribution #' . $contribution->id,
            ];

            // Make STK Push request
            $response = Http::timeout(30)->withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post($this->getStkPushUrl(), $requestData);

            $responseData = $response->json();

            Log::info('M-Pesa STK Push request sent', [
                'contribution_id' => $contribution->id,
                'request_data' => $this->sanitizeLogData($requestData),
                'response_data' => $responseData,
            ]);

            if ($response->successful() && isset($responseData['ResponseCode']) && $responseData['ResponseCode'] == '0') {
                // Create transaction record
                $transaction = $this->createTransaction($contribution, [
                    'mpesa_checkout_request_id' => $responseData['CheckoutRequestID'],
                    'mpesa_merchant_request_id' => $responseData['MerchantRequestID'],
                    'mpesa_phone_number' => $phoneNumber,
                    'mpesa_payment_type' => 'stk_push',
                    'status' => Transaction::STATUS_PENDING,
                    'gateway_response' => $responseData,
                ]);

                return [
                    'success' => true,
                    'message' => 'STK Push sent successfully. Please check your phone.',
                    'checkout_request_id' => $responseData['CheckoutRequestID'],
                    'merchant_request_id' => $responseData['MerchantRequestID'],
                    'transaction' => $transaction,
                ];
            } else {
                $errorMessage = $responseData['errorMessage'] ?? $responseData['CustomerMessage'] ?? 'STK Push failed';
                throw new \Exception($errorMessage);
            }
        } catch (\Exception $e) {
            Log::error('M-Pesa STK Push failed', [
                'contribution_id' => $contribution->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Handle M-Pesa callback
     */
    public function handleCallback(array $callbackData): array
    {
        try {
            Log::info('M-Pesa callback received', $callbackData);

            // Extract STK Push callback data
            if (!isset($callbackData['Body']['stkCallback'])) {
                throw new \Exception('Unknown callback type');
            }

            $stkCallback = $callbackData['Body']['stkCallback'];
            $checkoutRequestId = $stkCallback['CheckoutRequestID'];
            $resultCode = $stkCallback['ResultCode'];
            $resultDesc = $stkCallback['ResultDesc'];

            // Find transaction by checkout request ID
            $transaction = Transaction::where('mpesa_checkout_request_id', $checkoutRequestId)->first();

            if (!$transaction) {
                throw new \Exception('Transaction not found for checkout request ID: ' . $checkoutRequestId);
            }

            $contribution = $transaction->contribution;

            if ($resultCode == 0) {
                // Payment successful
                $callbackMetadata = $stkCallback['CallbackMetadata']['Item'] ?? [];
                $paymentData = $this->extractCallbackMetadata($callbackMetadata);

                // Update transaction with success details
                $transaction->update([
                    'status' => Transaction::STATUS_COMPLETED,
                    'mpesa_receipt_number' => $paymentData['receipt_number'],
                    'mpesa_amount' => $paymentData['amount'],
                    'mpesa_transaction_date' => $paymentData['transaction_date'],
                    'mpesa_phone_number' => $paymentData['phone_number'],
                    'gateway_response' => array_merge($transaction->gateway_response ?? [], $stkCallback),
                    'processed_at' => now(),
                ]);

                // Update contribution status
                $contribution->update([
                    'payment_status' => Contribution::STATUS_COMPLETED,
                    'processed_at' => now(),
                ]);

                // Refresh contribution to get latest wallet_credited status
                $contribution->refresh();

                // Credit wallet only if not already credited
                if (!$contribution->wallet_credited) {
                    $this->walletService->creditFromDonation($contribution);
                } else {
                    Log::info('Wallet already credited for contribution via callback', [
                        'contribution_id' => $contribution->id,
                    ]);
                }

                Log::info('M-Pesa STK Push payment completed', [
                    'contribution_id' => $contribution->id,
                    'transaction_id' => $transaction->id,
                    'receipt_number' => $paymentData['receipt_number'],
                    'amount' => $paymentData['amount'],
                ]);

                return [
                    'success' => true,
                    'transaction' => $transaction,
                    'contribution' => $contribution,
                ];
            } else {
                // Payment failed
                $transaction->update([
                    'status' => Transaction::STATUS_FAILED,
                    'gateway_response' => array_merge($transaction->gateway_response ?? [], $stkCallback),
                    'notes' => $resultDesc,
                    'processed_at' => now(),
                ]);

                $contribution->update([
                    'payment_status' => Contribution::STATUS_FAILED,
                    'processed_at' => now(),
                ]);

                Log::info('M-Pesa STK Push payment failed', [
                    'contribution_id' => $contribution->id,
                    'transaction_id' => $transaction->id,
                    'result_code' => $resultCode,
                    'result_desc' => $resultDesc,
                ]);

                return [
                    'success' => false,
                    'transaction' => $transaction,
                    'contribution' => $contribution,
                    'error' => $resultDesc,
                ];
            }
        } catch (\Exception $e) {
            Log::error('M-Pesa callback processing failed', [
                'error' => $e->getMessage(),
                'callback_data' => $callbackData,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Extract callback metadata into structured array
     */
    protected function extractCallbackMetadata(array $callbackMetadata): array
    {
        $data = [
            'amount' => null,
            'receipt_number' => null,
            'transaction_date' => null,
            'phone_number' => null,
        ];

        foreach ($callbackMetadata as $item) {
            switch ($item['Name']) {
                case 'Amount':
                    $data['amount'] = $item['Value'];
                    break;
                case 'MpesaReceiptNumber':
                    $data['receipt_number'] = $item['Value'];
                    break;
                case 'TransactionDate':
                    $data['transaction_date'] = Carbon::createFromFormat('YmdHis', $item['Value']);
                    break;
                case 'PhoneNumber':
                    $data['phone_number'] = $item['Value'];
                    break;
            }
        }

        return $data;
    }

    /**
     * Query STK Push status
     */
    public function queryStkPushStatus(string $checkoutRequestId): array
    {
        try {
            $envConfig = $this->getEnvironmentConfig();

            // Validate configuration
            $configErrors = $this->validateConfiguration();
            if (!empty($configErrors)) {
                throw new \Exception('M-Pesa configuration errors: ' . implode(', ', $configErrors));
            }

            $accessToken = $this->getAccessToken();
            if (!$accessToken) {
                throw new \Exception('Failed to get M-Pesa access token');
            }

            $timestamp = Carbon::now()->format('YmdHis');
            $password = $this->generateStkPushPassword($timestamp);

            $requestData = [
                'BusinessShortCode' => $envConfig['business_short_code'],
                'Password' => $password,
                'Timestamp' => $timestamp,
                'CheckoutRequestID' => $checkoutRequestId,
            ];

            $response = Http::timeout(30)->withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post($this->getStkPushQueryUrl(), $requestData);

            $responseData = $response->json();

            Log::info('M-Pesa STK Push query response', [
                'checkout_request_id' => $checkoutRequestId,
                'response' => $responseData,
            ]);

            return [
                'success' => $response->successful(),
                'data' => $responseData,
            ];
        } catch (\Exception $e) {
            Log::error('STK Push query failed', [
                'checkout_request_id' => $checkoutRequestId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Validate paybill account details
     */
    public function validatePaybillAccount(PayoutMethod $paybillMethod, string $accountNumber, string $accountName): bool
    {
        return $paybillMethod->validatePaybillAccount($accountNumber, $accountName);
    }

    /**
     * Get M-Pesa access token
     */
    protected function getAccessToken(): ?string
    {
        try {
            $response = Http::timeout(30)->withHeaders([
                'Authorization' => $this->getAuthorizationHeader(),
                'Content-Type' => 'application/json',
            ])->get($this->getOAuthUrl());

            if ($response->successful()) {
                $data = $response->json();
                return $data['access_token'] ?? null;
            }

            Log::error('Failed to get M-Pesa access token', [
                'response' => $response->json(),
                'status' => $response->status(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('M-Pesa access token request failed', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Create transaction record
     */
    protected function createTransaction(Contribution $contribution, array $data): Transaction
    {
        return $contribution->transactions()->create([
            'transaction_id' => Str::uuid(),
            'gateway' => Transaction::GATEWAY_MPESA,
            'type' => Transaction::TYPE_PAYMENT,
            'amount' => $contribution->amount,
            'currency' => $contribution->currency,
            'mpesa_checkout_request_id' => $data['mpesa_checkout_request_id'] ?? null,
            'mpesa_merchant_request_id' => $data['mpesa_merchant_request_id'] ?? null,
            'mpesa_phone_number' => $data['mpesa_phone_number'] ?? null,
            'mpesa_payment_type' => $data['mpesa_payment_type'] ?? null,
            'status' => $data['status'] ?? Transaction::STATUS_PENDING,
            'gateway_response' => $data['gateway_response'] ?? [],
            'notes' => $data['notes'] ?? null,
        ]);
    }

    /**
     * Format phone number to M-Pesa format
     */
    protected function formatPhoneNumber(string $phone): string
    {
        // Remove any non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Convert to 254 format
        if (str_starts_with($phone, '0')) {
            return '254' . substr($phone, 1);
        } elseif (str_starts_with($phone, '+254')) {
            return substr($phone, 1);
        } elseif (str_starts_with($phone, '254')) {
            return $phone;
        }

        // Assume it's a Kenyan number without country code
        return '254' . $phone;
    }

    /**
     * Get callback URL for M-Pesa
     */
    protected function getCallbackUrl(): string
    {
        return $this->config['callback_urls']['stk_push_callback'];
    }

    /**
     * Generate paybill payment instructions
     */
    protected function generatePaybillInstructions(Contribution $contribution, array $paybillDetails): array
    {
        return [
            'paybill_number' => $paybillDetails['paybill_number'],
            'account_number' => $paybillDetails['account_number'],
            'account_name' => $paybillDetails['account_name'],
            'amount' => $contribution->amount,
            'reference' => 'DON_' . $contribution->id,
            'steps' => [
                '1. Go to M-Pesa menu on your phone',
                '2. Select "Lipa na M-Pesa"',
                '3. Select "Pay Bill"',
                '4. Enter Business Number: ' . $paybillDetails['paybill_number'],
                '5. Enter Account Number: ' . $paybillDetails['account_number'],
                '6. Enter Amount: KES ' . number_format($contribution->amount, 2),
                '7. Enter your M-Pesa PIN',
                '8. Confirm payment details',
                '9. Wait for confirmation SMS',
                '10. Share the M-Pesa reference with us for verification',
            ],
            'important_notes' => [
                'Please ensure you enter the exact account number: ' . $paybillDetails['account_number'],
                'Amount must be exactly: KES ' . number_format($contribution->amount, 2),
                'Keep your M-Pesa confirmation message for reference',
                'Payment verification may take up to 24 hours',
            ],
        ];
    }

    /**
     * Validate transaction amount against configured limits
     */
    protected function validateTransactionAmount(float $amount): void
    {
        $limits = $this->config['transaction_limits'];

        if ($amount < $limits['minimum_amount']) {
            throw new \Exception('Transaction amount is below minimum limit of KES ' . number_format($limits['minimum_amount'], 2));
        }

        if ($amount > $limits['maximum_amount']) {
            throw new \Exception('Transaction amount exceeds maximum limit of KES ' . number_format($limits['maximum_amount'], 2));
        }
    }

    /**
     * Test M-Pesa connection
     */
    public function testConnection(): array
    {
        try {
            $accessToken = $this->getAccessToken();
            $environment = $this->config['default_environment'];

            if ($accessToken) {
                return [
                    'success' => true,
                    'message' => 'M-Pesa connection successful',
                    'environment' => $environment,
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to authenticate with M-Pesa',
                    'environment' => $environment,
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Connection test failed: ' . $e->getMessage(),
                'environment' => $this->config['default_environment'],
            ];
        }
    }

    /**
     * Get current environment
     */
    public function getCurrentEnvironment(): string
    {
        return $this->config['default_environment'];
    }

    /**
     * Check if current environment is sandbox
     */
    public function isSandbox(): bool
    {
        return $this->getCurrentEnvironment() === 'sandbox';
    }

    /**
     * Check if current environment is production
     */
    public function isProduction(): bool
    {
        return $this->getCurrentEnvironment() === 'production';
    }

    /**
     * Sanitize sensitive data for logging
     */
    protected function sanitizeLogData(array $data): array
    {
        $sanitized = $data;

        // Remove sensitive information from logs
        if (isset($sanitized['Password'])) {
            $sanitized['Password'] = '***HIDDEN***';
        }

        return $sanitized;
    }
}
