<?php

namespace App\Http\Controllers;

use App\Http\Requests\DonationRequest;
use App\Models\Application;
use App\Models\Contribution;
use App\Models\DonationLink;
use App\Models\Transaction;
use App\Services\CyberSourceService;
use App\Services\DonationNotificationService;
use App\Services\DonationStatisticsService;
use App\Services\MpesaService;
use App\Services\MpesaStatusChecker;
use App\Services\WalletService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DonationController extends Controller
{
    protected $cyberSourceService;
    protected $mpesaService;
    protected $walletService;
    protected $donationStatisticsService;
    protected $notificationService;

    public function __construct(
        CyberSourceService $cyberSourceService,
        MpesaService $mpesaService,
        WalletService $walletService,
        DonationStatisticsService $donationStatisticsService,
        DonationNotificationService $notificationService
    ) {
        $this->cyberSourceService = $cyberSourceService;
        $this->mpesaService = $mpesaService;
        $this->walletService = $walletService;
        $this->donationStatisticsService = $donationStatisticsService;
        $this->notificationService = $notificationService;
    }

    /**
     * Show the donation form for a specific donation link
     */
    public function show($code)
    {
        // Find the donation link by code
        $donationLink = DonationLink::with(['application', 'application.applicant'])
            ->where('code', $code)
            ->first();

        // Check if link exists
        if (!$donationLink) {
            Log::warning('Invalid donation link accessed', ['code' => $code]);
            abort(404, 'Donation link not found');
        }

        // Check if link is active and not expired
        if (!$donationLink->isActive()) {
            Log::warning('Inactive or expired donation link accessed', [
                'code' => $code,
                'status' => $donationLink->status,
                'expires_at' => $donationLink->expires_at
            ]);

            return view('donations.expired', compact('donationLink'));
        }

        // Get paybill details if available
        $paybillDetails = null;
        if ($donationLink->isPaybillEnabled()) {
            $paybillDetails = $donationLink->getPaybillDetails();
        }

        // Record the access
        // $donationLink->recordAccess();

        // Get application and contribution details
        $application = $donationLink->application;
        $applicant = $application->applicant;

        // Get donation statistics using the service
        $contributionStats = $this->donationStatisticsService->getStatistics($donationLink);

        // Extract progress percentage for easier access in blade
        $progressPercentage = $contributionStats['progress_percentage'];

        // Log successful access
        Log::info('Donation link accessed', [
            'code' => $code,
            'application_id' => $application->id,
        ]);

        return view('donations.form', compact('donationLink', 'application', 'applicant', 'paybillDetails', 'contributionStats', 'progressPercentage'));
    }

    /**
     * Process donation form submission
     */
    public function process(DonationRequest $request, $code)
    {
        // Find the donation link
        $donationLink = DonationLink::where('code', $code)->first();

        if (!$donationLink || !$donationLink->isActive()) {
            return redirect()->back()->with('error', 'Invalid or expired donation link');
        }

        // Validate CyberSource configuration BEFORE any processing
        if ($request->payment_method === 'card') {
            $configErrors = $this->cyberSourceService->validateConfiguration();
            if (!empty($configErrors)) {
                Log::error('CyberSource configuration errors', [
                    'errors' => $configErrors,
                    'donation_link_code' => $code
                ]);
                return redirect()->back()
                    ->withErrors(['error' => 'Payment system configuration error. Please contact support.'])
                    ->withInput();
            }
        } elseif ($request->payment_method === 'mpesa') {
            $configErrors = $this->mpesaService->validateConfiguration();
            if (!empty($configErrors)) {
                Log::error('M-Pesa configuration errors', [
                    'errors' => $configErrors,
                    'donation_link_code' => $code
                ]);
                return redirect()->back()
                    ->withErrors(['error' => 'M-Pesa system configuration error. Please contact support.'])
                    ->withInput();
            }
        }

        try {
            DB::beginTransaction();

            // Create contribution record
            $contribution = $this->createContribution($donationLink, $request->validated());

            // Handle payment method
            if ($request->payment_method === 'card') {
                $paymentData = $this->handleCardPayment($contribution);
                DB::commit();
                return $this->redirectToCyberSource($paymentData);
            } else {
                // Handle M-Pesa payment
                $result = $this->handleMpesaPayment($donationLink, $contribution, $request);
                DB::commit();
                return $result;
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Donation processing failed', [
                'error' => $e->getMessage(),
                'donation_link_code' => $code,
                'request_data' => $request->validated(),
            ]);

            return redirect()->back()
                ->withErrors(['error' => 'Something went wrong. Please try again.'])
                ->withInput();
        }
    }

    public function cyberSourceCallback(Request $request)
    {
        try {
            $responseData = $request->all();

            Log::info('CyberSource callback received', [
                'decision' => $responseData['decision'] ?? 'unknown',
                'reason_code' => $responseData['reason_code'] ?? 'unknown',
                'reference_number' => $responseData['req_reference_number'] ?? 'unknown',
                'request_id' => $responseData['request_id'] ?? 'unknown'
            ]);

            // Process the callback
            $result = $this->cyberSourceService->processCallback($responseData);
            $contribution = $result['contribution'];
            $decision = $result['decision'];

            // Send notifications for successful donations
            if ($decision === 'ACCEPT' && $contribution->payment_status === Contribution::STATUS_COMPLETED) {
                $this->notificationService->sendDonationNotifications($contribution);
            }

            // Route based on the decision/outcome
            return $this->routeBasedOnDecision($contribution, $decision, $result);
        } catch (\Exception $e) {
            Log::error('CyberSource callback processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            // Try to extract contribution info for error routing
            $referenceNumber = $request->input('req_reference_number');
            if ($referenceNumber) {
                $contribution = Contribution::find($referenceNumber);
                if ($contribution) {
                    return redirect()->route('donation.failure', $contribution->donationLink->code)
                        ->with('error', 'Payment processing error. Please contact support if you were charged.');
                }
            }

            // Fallback error page
            return redirect()->route('donation.failure', 'error')
                ->with('error', 'Payment processing error. Please contact support.');
        }
    }

    /**
     * Route user to appropriate page based on transaction decision
     */
    private function routeBasedOnDecision(Contribution $contribution, string $decision, array $result): \Illuminate\Http\RedirectResponse
    {
        $donationCode = $contribution->donationLink->code;

        switch ($decision) {
            case 'ACCEPT':
                Log::info('Payment successful', [
                    'contribution_id' => $contribution->id,
                    'transaction_id' => $contribution->cybersource_transaction_id,
                    'amount' => $contribution->amount,
                    'currency' => $contribution->currency
                ]);

                return redirect()->route('donation.success', $donationCode)
                    ->with('success', 'Your donation was processed successfully!')
                    ->with('contribution', $contribution);

            case 'CANCEL':
                Log::info('Payment cancelled by user', [
                    'contribution_id' => $contribution->id,
                    'reason_code' => $result['reason_code'] ?? 'unknown'
                ]);

                return redirect()->route('donation.cancel', $donationCode)
                    ->with('info', 'Payment was cancelled. No charges were made.')
                    ->with('contribution', $contribution);

            case 'DECLINE':
            case 'ERROR':
                Log::warning('Payment failed', [
                    'contribution_id' => $contribution->id,
                    'decision' => $decision,
                    'reason_code' => $result['reason_code'] ?? 'unknown',
                    'declined_reason' => $this->getDeclineReason($result['reason_code'] ?? null)
                ]);

                $errorMessage = $this->getUserFriendlyErrorMessage($result['reason_code'] ?? null);

                return redirect()->route('donation.failure', $donationCode)
                    ->with('error', $errorMessage)
                    ->with('contribution', $contribution);

            case 'REVIEW':
                Log::info('Payment under review', [
                    'contribution_id' => $contribution->id,
                    'reason_code' => $result['reason_code'] ?? 'unknown'
                ]);

                return redirect()->route('donation.success', $donationCode)
                    ->with('warning', 'Your payment is being reviewed for security. You will receive confirmation shortly.')
                    ->with('contribution', $contribution);

            default:
                Log::error('Unknown payment decision', [
                    'contribution_id' => $contribution->id,
                    'decision' => $decision,
                    'result' => $result
                ]);

                return redirect()->route('donation.failure', $donationCode)
                    ->with('error', 'Payment status unclear. Please contact support to verify your donation.')
                    ->with('contribution', $contribution);
        }
    }

    /**
     * Get user-friendly error message based on reason code
     */
    private function getUserFriendlyErrorMessage(?string $reasonCode): string
    {
        return match ($reasonCode) {
            '102' => 'One or more fields in the request contains invalid data. Please check your information and try again.',
            '200' => 'The authorization request was approved by the issuing bank but declined by CyberSource due to risk factors.',
            '201' => 'The issuing bank has questions about the request. Please contact your bank.',
            '202' => 'Your card has expired. Please use a different card.',
            '203' => 'Your card was declined by the bank. Please try a different payment method.',
            '204' => 'Insufficient funds available. Please check your account balance.',
            '205' => 'Your card was stolen or lost. Please contact your bank.',
            '207' => 'The issuing bank is unavailable. Please try again later.',
            '208' => 'Your card is inactive or not authorized for card-not-present transactions.',
            '210' => 'The credit limit for your card has been exceeded.',
            '211' => 'Your card verification number (CVN) is invalid.',
            '221' => 'The customer matched an entry on the processor\'s negative file.',
            '230' => 'The authorization request was approved by the issuing bank but declined due to risk factors.',
            '231' => 'The card verification number (CVN) is invalid.',
            '232' => 'The card type sent is invalid or does not correlate with the credit card number.',
            '233' => 'General decline by the processor.',
            '234' => 'There is a problem with your merchant configuration.',
            '236' => 'Processor failure.',
            '240' => 'The card type sent is invalid or does not correlate with the credit card number.',
            '475' => 'The cardholder is enrolled for payer authentication.',
            '476' => 'Payer authentication could not be authenticated.',
            default => 'Your payment could not be processed. Please try again or contact your bank.'
        };
    }

    /**
     * Get internal decline reason for logging
     */
    private function getDeclineReason(?string $reasonCode): string
    {
        return match ($reasonCode) {
            '102' => 'Invalid field data',
            '200' => 'Risk decline',
            '201' => 'Issuer inquiry',
            '202' => 'Expired card',
            '203' => 'General decline',
            '204' => 'Insufficient funds',
            '205' => 'Stolen/lost card',
            '207' => 'Issuer unavailable',
            '208' => 'Inactive card',
            '210' => 'Credit limit exceeded',
            '211', '231' => 'Invalid CVN',
            '221' => 'Negative file match',
            '230' => 'Risk decline',
            '232', '240' => 'Invalid card type',
            '233' => 'General processor decline',
            '234' => 'Configuration problem',
            '236' => 'Processor failure',
            '475' => 'Payer auth enrolled',
            '476' => 'Payer auth failed',
            default => "Unknown reason code: {$reasonCode}"
        };
    }

    /**
     * Show success page
     */
    public function showSuccess($code)
    {
        $donationLink = DonationLink::where('code', $code)->first();

        if (!$donationLink) {
            abort(404);
        }

        // Get the latest successful contribution for this donation link
        $contribution = $donationLink->contributions()
            ->where('payment_status', Contribution::STATUS_COMPLETED)
            ->latest()
            ->first();

        return view('donations.success', compact('donationLink', 'contribution'));
    }

    /**
     * Show cancel page
     */
    public function showCancel($code)
    {
        $donationLink = DonationLink::where('code', $code)->first();

        if (!$donationLink) {
            abort(404);
        }

        return view('donations.cancel', compact('donationLink'));
    }

    /**
     * Show failure page
     */
    public function showFailure($code)
    {
        $donationLink = DonationLink::where('code', $code)->first();

        if (!$donationLink) {
            abort(404);
        }

        // Get the latest failed contribution for this donation link
        $contribution = $donationLink->contributions()
            ->whereIn('payment_status', [Contribution::STATUS_FAILED, Contribution::STATUS_CANCELLED])
            ->latest()
            ->first();

        return view('donations.failure', compact('donationLink', 'contribution'));
    }

    /**
     * Create contribution record
     */
    private function createContribution(DonationLink $donationLink, array $data): Contribution
    {
        $paymentMethod = $data['payment_method'];

        // Base contribution data
        $contributionData = [
            'amount' => $data['amount'],
            'currency' => $data['currency'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'donation_type' => $data['donation_type'],
            'payment_method' => $paymentMethod,
            'payment_status' => Contribution::STATUS_PENDING,
        ];

        // Handle billing information based on payment method
        if ($paymentMethod === 'card') {
            // Card payments require full billing information for CyberSource
            $firstName = '';
            $lastName = '';

            if (!empty($data['full_name'])) {
                $nameParts = explode(' ', trim($data['full_name']), 2);
                $firstName = $nameParts[0] ?? '';
                $lastName = $nameParts[1] ?? $firstName; // Use first name as fallback
            }

            $contributionData = array_merge($contributionData, [
                'bill_to_forename' => $firstName,
                'bill_to_surname' => $lastName,
                'bill_to_address_line1' => $data['address_line1'],
                'bill_to_address_city' => $data['city'],
                'bill_to_address_state' => $data['state'],
                'bill_to_address_postal_code' => $data['postal_code'],
                'bill_to_address_country' => strtoupper($data['country']),
            ]);
        } elseif ($paymentMethod === 'mpesa') {
            // M-Pesa payments: Generate minimal billing info or use provided name
            $firstName = '';
            $lastName = '';

            if (!empty($data['full_name'])) {
                $nameParts = explode(' ', trim($data['full_name']), 2);
                $firstName = $nameParts[0] ?? '';
                $lastName = $nameParts[1] ?? '';
            } else {
                // Generate name from email if not provided
                $emailParts = explode('@', $data['email']);
                $firstName = ucfirst($emailParts[0]);
                $lastName = 'Donor'; // Default last name
            }

            $contributionData = array_merge($contributionData, [
                'bill_to_forename' => $firstName,
                'bill_to_surname' => $lastName,
                'bill_to_address_line1' => 'N/A', // M-Pesa doesn't require address
                'bill_to_address_city' => 'Nairobi', // Default city for M-Pesa
                'bill_to_address_state' => 'Nairobi', // Default state
                'bill_to_address_postal_code' => '00100', // Default postal code
                'bill_to_address_country' => 'KE', // Kenya for M-Pesa
            ]);
        }

        // Log contribution creation for debugging
        Log::info('Creating contribution', [
            'payment_method' => $paymentMethod,
            'amount' => $data['amount'],
            'currency' => $data['currency'],
            'email' => $data['email'],
            'donation_type' => $data['donation_type'],
            'billing_name' => ($contributionData['bill_to_forename'] ?? '') . ' ' . ($contributionData['bill_to_surname'] ?? ''),
        ]);

        return $donationLink->contributions()->create($contributionData);
    }

    /**
     * Handle card payment through CyberSource
     */
    private function handleCardPayment(Contribution $contribution): array
    {
        try {
            return $this->cyberSourceService->generatePaymentData($contribution);
        } catch (\Exception $e) {
            Log::error('CyberSource payment data generation failed', [
                'contribution_id' => $contribution->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Redirect to CyberSource hosted checkout
     */
    private function redirectToCyberSource(array $paymentData)
    {
        return view('donations.cybersource-redirect', [
            'gateway_url' => $paymentData['gateway_url'],
            'fields' => $paymentData['fields']
        ]);
    }

    /**
     * Handle M-Pesa payment (STK Push only) - Prevents duplicate requests
     */
    protected function handleMpesaPayment(DonationLink $donationLink, Contribution $contribution, DonationRequest $request)
    {
        // Check if transaction already exists for this contribution
        $existingTransaction = Transaction::where('contribution_id', $contribution->id)
            ->where('gateway', 'mpesa')
            ->first();

        if ($existingTransaction) {
            if ($existingTransaction->status === Transaction::STATUS_COMPLETED) {
                Log::info('Payment already completed, showing success view', [
                    'contribution_id' => $contribution->id,
                    'transaction_id' => $existingTransaction->id,
                ]);

                return view('donations.mpesa-stk-push', [
                    'contribution' => $contribution,
                    'checkout_request_id' => $existingTransaction->mpesa_checkout_request_id,
                    'merchant_request_id' => $existingTransaction->mpesa_merchant_request_id,
                    'message' => 'Payment already completed successfully',
                    'environment' => $this->mpesaService->getCurrentEnvironment(),
                    'payment_completed' => true,
                ]);
            } elseif ($existingTransaction->status === Transaction::STATUS_PENDING) {
                Log::info('Existing pending M-Pesa transaction found', [
                    'contribution_id' => $contribution->id,
                    'transaction_id' => $existingTransaction->id,
                    'checkout_request_id' => $existingTransaction->mpesa_checkout_request_id,
                ]);

                return view('donations.mpesa-stk-push', [
                    'contribution' => $contribution,
                    'checkout_request_id' => $existingTransaction->mpesa_checkout_request_id,
                    'merchant_request_id' => $existingTransaction->mpesa_merchant_request_id,
                    'message' => 'Please complete the payment on your phone',
                    'environment' => $this->mpesaService->getCurrentEnvironment(),
                ]);
            } elseif ($existingTransaction->status === Transaction::STATUS_FAILED || $existingTransaction->status === Transaction::STATUS_CANCELLED) {
                Log::info('Previous M-Pesa transaction failed, creating new transaction', [
                    'contribution_id' => $contribution->id,
                    'failed_transaction_id' => $existingTransaction->id,
                ]);
            }
        }

        // No existing pending/completed transaction - proceed with new STK push
        $result = $this->mpesaService->processStkPush($contribution);

        if ($result['success']) {
            Log::info('STK Push initiated successfully', [
                'contribution_id' => $contribution->id,
                'checkout_request_id' => $result['checkout_request_id'],
                'environment' => $this->mpesaService->getCurrentEnvironment(),
            ]);

            return view('donations.mpesa-stk-push', [
                'contribution' => $contribution,
                'checkout_request_id' => $result['checkout_request_id'],
                'merchant_request_id' => $result['merchant_request_id'],
                'message' => $result['message'],
                'environment' => $this->mpesaService->getCurrentEnvironment(),
            ]);
        } else {
            Log::error('STK Push failed', [
                'contribution_id' => $contribution->id,
                'error' => $result['error'],
                'environment' => $this->mpesaService->getCurrentEnvironment(),
            ]);

            // Throw exception to trigger rollback in main handler
            throw new \Exception($result['error']);
        }
    }

    /**
     * M-Pesa callback handler
     */
    public function mpesaCallback(Request $request)
    {
        // Validate callback IP if enabled
        // if (config('mpesa.security.verify_callback_ip', false)) {
        //     $allowedIps = explode(',', config('mpesa.security.allowed_callback_ips', ''));
        //     if (!empty($allowedIps) && !in_array($request->ip(), $allowedIps)) {
        //         Log::warning('M-Pesa callback from unauthorized IP', [
        //             'ip' => $request->ip(),
        //             'allowed_ips' => $allowedIps,
        //         ]);
        //         return response('Unauthorized', 403);
        //     }
        // }

        try {
            $result = $this->mpesaService->handleCallback($request->all());

            if ($result['success']) {
                Log::info('M-Pesa callback processed successfully', [
                    'contribution_id' => $result['contribution']->id ?? null,
                    'transaction_id' => $result['transaction']->id ?? null,
                    'environment' => $this->mpesaService->getCurrentEnvironment(),
                ]);

                return response('OK', 200);
            } else {
                Log::error('M-Pesa callback processing failed', [
                    'error' => $result['error'],
                    'environment' => $this->mpesaService->getCurrentEnvironment(),
                ]);
                return response('Error processing callback', 400);
            }
        } catch (\Exception $e) {
            Log::error('M-Pesa callback exception', [
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString(),
                'environment' => $this->mpesaService->getCurrentEnvironment(),
            ]);
            return response('Error processing callback', 500);
        }
    }

    /**
     * Check STK Push status
     */
    public function checkStkPushStatus(Request $request)
    {
        $request->validate([
            'checkout_request_id' => 'required|string',
        ]);

        try {
            // Query M-Pesa API for status
            $result = $this->mpesaService->queryStkPushStatus($request->checkout_request_id);

            Log::info('STK Push status check', [
                'checkout_request_id' => $request->checkout_request_id,
                'success' => $result['success'],
                'environment' => $this->mpesaService->getCurrentEnvironment(),
            ]);

            // Find local transaction
            $transaction = Transaction::where('mpesa_checkout_request_id', $request->checkout_request_id)->first();

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'error' => 'Transaction not found',
                ], 404);
            }

            $contribution = $transaction->contribution;

            // If M-Pesa query was successful, check the status
            if ($result['success'] && isset($result['data']['ResultCode'])) {
                $resultCode = $result['data']['ResultCode'];
                $resultDesc = $result['data']['ResultDesc'] ?? '';

                if ($resultCode === '0' || $resultCode === 0) {
                    // Payment successful - update database if not already updated
                    if ($transaction->status !== Transaction::STATUS_COMPLETED) {
                        DB::beginTransaction();
                        try {
                            // Extract payment details from M-Pesa response
                            $paymentData = [];
                            if (isset($result['data']['CallbackMetadata']['Item'])) {
                                $paymentData = $this->extractCallbackMetadata($result['data']['CallbackMetadata']['Item']);
                            }

                            // Update transaction
                            $transaction->update([
                                'status' => Transaction::STATUS_COMPLETED,
                                'mpesa_receipt_number' => $paymentData['receipt_number'] ?? null,
                                'mpesa_amount' => $paymentData['amount'] ?? $contribution->amount,
                                'mpesa_transaction_date' => $paymentData['transaction_date'] ?? now(),
                                'mpesa_phone_number' => $paymentData['phone_number'] ?? null,
                                'gateway_response' => array_merge($transaction->gateway_response ?? [], $result['data']),
                                'processed_at' => now(),
                            ]);

                            // Calculate platform fee before marking contribution as completed
                            if (!$contribution->hasPlatformFeeCalculated()) {
                                $contribution->refresh();
                                $contribution->calculatePlatformFee(null, $transaction);
                                $contribution->save();
                            }

                            // Update contribution
                            $contribution->update([
                                'payment_status' => Contribution::STATUS_COMPLETED,
                                'platform_fee' => $contribution->platform_fee,
                                'net_amount' => $contribution->net_amount,
                                'platform_fee_percentage' => $contribution->platform_fee_percentage,
                                'processed_at' => now(),
                            ]);

                            // Refresh contribution to get latest wallet_credited status
                            $contribution->refresh();

                            // Credit wallet only if not already credited
                            if (!$contribution->wallet_credited) {
                                $this->walletService->creditFromDonation($contribution);
                            } else {
                                Log::info('Wallet already credited for contribution via status check', [
                                    'contribution_id' => $contribution->id,
                                ]);
                            }

                            DB::commit();

                            if ($contribution->payment_status === Contribution::STATUS_COMPLETED) {
                                $this->notificationService->sendDonationNotifications($contribution);
                            }

                            Log::info('M-Pesa payment completed via status check with platform fee', [
                                'contribution_id' => $contribution->id,
                                'transaction_id' => $transaction->id,
                                'receipt_number' => $paymentData['receipt_number'] ?? 'N/A',
                                'gross_amount' => $contribution->amount,
                                'platform_fee' => $contribution->platform_fee,
                                'net_amount' => $contribution->net_amount,
                            ]);
                        } catch (\Exception $e) {
                            DB::rollback();
                            Log::error('Failed to update transaction status', [
                                'transaction_id' => $transaction->id,
                                'error' => $e->getMessage(),
                            ]);
                        }
                    }

                    return response()->json([
                        'success' => true,
                        'status' => 'completed',
                        'data' => [
                            'ResultCode' => $resultCode,
                            'ResultDesc' => $resultDesc,
                            'transaction_status' => $transaction->status,
                            'contribution_status' => $contribution->payment_status,
                        ],
                    ]);
                } elseif ($resultCode && $resultCode !== '500.001.1001') {
                    // Payment failed, cancelled, or timed out
                    if ($transaction->status !== Transaction::STATUS_FAILED && $transaction->status !== Transaction::STATUS_CANCELLED) {
                        $status = ($resultCode == 1032) ? Transaction::STATUS_CANCELLED : Transaction::STATUS_FAILED;
                        $transaction->update([
                            'status' => $status,
                            'gateway_response' => array_merge($transaction->gateway_response ?? [], $result['data']),
                            'notes' => $resultDesc,
                            'processed_at' => now(),
                        ]);

                        $contribution->update([
                            'payment_status' => $status === Transaction::STATUS_CANCELLED ? Contribution::STATUS_CANCELLED : Contribution::STATUS_FAILED,
                            'processed_at' => now(),
                        ]);

                        Log::info('M-Pesa payment failed/cancelled via status check', [
                            'contribution_id' => $contribution->id,
                            'transaction_id' => $transaction->id,
                            'result_code' => $resultCode,
                            'result_desc' => $resultDesc,
                            'status' => $status,
                        ]);
                    }

                    return response()->json([
                        'success' => true,
                        'status' => $status === Transaction::STATUS_CANCELLED ? 'cancelled' : 'failed',
                        'data' => [
                            'ResultCode' => $resultCode,
                            'ResultDesc' => $resultDesc,
                            'transaction_status' => $transaction->status,
                            'contribution_status' => $contribution->payment_status,
                        ],
                    ]);
                }
            }

            // Payment still processing or query failed - return current status
            return response()->json([
                'success' => true,
                'status' => 'processing',
                'data' => [
                    'ResultCode' => $result['data']['ResultCode'] ?? '500.001.1001',
                    'ResultDesc' => $result['data']['ResultDesc'] ?? 'Request is being processed',
                    'transaction_status' => $transaction->status,
                    'contribution_status' => $contribution->payment_status,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('STK Push status check failed', [
                'checkout_request_id' => $request->checkout_request_id,
                'error' => $e->getMessage(),
                'environment' => $this->mpesaService->getCurrentEnvironment(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to check payment status',
            ], 500);
        }
    }

    /**
     * Extract callback metadata from M-Pesa response
     */
    private function extractCallbackMetadata(array $callbackMetadata): array
    {
        $data = [];

        foreach ($callbackMetadata as $item) {
            if (!isset($item['Name'])) continue;

            switch ($item['Name']) {
                case 'Amount':
                    $data['amount'] = $item['Value'] ?? null;
                    break;
                case 'MpesaReceiptNumber':
                    $data['receipt_number'] = $item['Value'] ?? null;
                    break;
                case 'TransactionDate':
                    $data['transaction_date'] = isset($item['Value']) ?
                        Carbon::createFromFormat('YmdHis', $item['Value']) : null;
                    break;
                case 'PhoneNumber':
                    $data['phone_number'] = $item['Value'] ?? null;
                    break;
            }
        }

        return $data;
    }

    /**
     * Test M-Pesa connection (for admin/debugging purposes)
     */
    public function testMpesaConnection()
    {
        if (!app()->environment(['local', 'staging'])) {
            abort(404);
        }

        try {
            $result = $this->mpesaService->testConnection();

            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'],
                'environment' => $result['environment'],
                'is_sandbox' => $this->mpesaService->isSandbox(),
                'is_production' => $this->mpesaService->isProduction(),
            ]);
        } catch (\Exception $e) {
            Log::error('M-Pesa connection test failed', [
                'error' => $e->getMessage(),
                'environment' => $this->mpesaService->getCurrentEnvironment(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Connection test failed: ' . $e->getMessage(),
                'environment' => $this->mpesaService->getCurrentEnvironment(),
            ], 500);
        }
    }

    /**
     * Credit wallet when donation is confirmed
     */
    protected function creditWalletFromDonation(Contribution $contribution): void
    {
        try {
            // Check if wallet has already been credited
            if ($contribution->wallet_credited) {
                Log::info('Wallet already credited for contribution', [
                    'contribution_id' => $contribution->id,
                ]);
                return;
            }

            // Credit the beneficiary's wallet
            $walletTransaction = $this->walletService->creditFromDonation($contribution);

            Log::info('Wallet credited successfully', [
                'contribution_id' => $contribution->id,
                'wallet_transaction_id' => $walletTransaction->id,
                'amount' => $contribution->amount,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to credit wallet from donation', [
                'contribution_id' => $contribution->id,
                'error' => $e->getMessage(),
            ]);

            // You might want to queue this for retry or send admin notification
            // For now, we'll log the error but not fail the webhook
        }
    }

    /**
     * Admin: List all donation links
     */
    public function index(Request $request)
    {
        $query = DonationLink::with(['application', 'createdBy']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by application
        if ($request->filled('application_id')) {
            $query->where('application_id', $request->application_id);
        }

        // Search by code
        if ($request->filled('search')) {
            $query->where('code', 'like', '%' . $request->search . '%');
        }

        $donationLinks = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.donation-links.index', compact('donationLinks'));
    }

    /**
     * Admin: Show donation link details
     */
    public function showLink(DonationLink $donationLink)
    {
        $donationLink->load(['application', 'application.applicant', 'createdBy']);

        return view('admin.donation-links.show', compact('donationLink'));
    }

    /**
     * User: Show donation management dashboard for a specific application
     */
    public function showDonation($applicationNumber)
    {
        $application = Application::where('application_number', $applicationNumber)
            ->with(['payoutMandate', 'users'])
            ->firstOrFail();

        // Check if user has access to this application
        if (!$this->userHasApplicationAccess($application, Auth::id())) {
            abort(403, 'You do not have access to this application.');
        }

        // Get donation links for this application
        $donationLinks = DonationLink::where('application_id', $application->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Check user authorization for payout methods
        $userRole = $this->getUserRoleForApplication($application, Auth::id());
        $canViewPayoutMethods = $this->canViewPayoutMethods($userRole);
        $checkerInfo = null;

        // Get payout methods only if user is authorized
        $payoutMethods = collect();
        if ($canViewPayoutMethods) {
            // Get payout methods from the application owner (user)
            $payoutMethods = $application->user->payoutMethods()
                ->orderBy('is_primary', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            // Get checker information for unauthorized users
            $checkerInfo = $this->getCheckerInfo($application);
        }

        // Calculate combined donation statistics for all donation links
        $combinedStats = $this->calculateCombinedDonationStats($donationLinks);

        // Extract commonly used values for backward compatibility
        $totalCollected = $combinedStats['total_raised_kes'];
        $targetAmount = $combinedStats['target_amount'];
        $progressPercentage = $combinedStats['progress_percentage'];

        return view('donations.show', compact(
            'application',
            'donationLinks',
            'payoutMethods',
            'totalCollected',
            'targetAmount',
            'progressPercentage',
            'combinedStats',
            'canViewPayoutMethods',
            'userRole',
            'checkerInfo'
        ));
    }

    /**
     * Admin: Toggle donation link status
     */
    public function toggleStatus(DonationLink $donationLink)
    {
        $newStatus = $donationLink->status === 'active' ? 'inactive' : 'active';

        $donationLink->update(['status' => $newStatus]);

        Log::info('Donation link status toggled', [
            'donation_link_id' => $donationLink->id,
            'old_status' => $donationLink->status,
            'new_status' => $newStatus,
            'toggled_by' => Auth::id()
        ]);

        return redirect()->back()->with(
            'success',
            "Donation link has been " . ($newStatus === 'active' ? 'activated' : 'deactivated') . " successfully."
        );
    }

    /**
     * Get donation statistics for a specific link
     */
    public function getStats($code)
    {
        $donationLink = DonationLink::where('code', $code)->first();

        if (!$donationLink) {
            return response()->json(['error' => 'Link not found'], 404);
        }

        return response()->json([
            'access_count' => $donationLink->access_count,
            'first_accessed_at' => $donationLink->first_accessed_at,
            'last_accessed_at' => $donationLink->last_accessed_at,
            'is_active' => $donationLink->isActive(),
            'is_expired' => $donationLink->isExpired(),
            'status' => $donationLink->status,
            'expires_at' => $donationLink->expires_at,
        ]);
    }

    /**
     * Check if user has access to the application (either creator or has role)
     */
    private function userHasApplicationAccess(Application $application, int $userId): bool
    {
        // Check if user is the application creator
        if ($application->user_id === $userId) {
            return true;
        }

        // Check if user has a role assigned to this application
        $hasRole = $application->users()
            ->where('user_id', $userId)
            ->exists();

        return $hasRole;
    }

    /**
     * Get the user's role for a specific application
     */
    private function getUserRoleForApplication(Application $application, $userId)
    {
        $applicationUser = $application->users()
            ->where('user_id', $userId)
            ->with('roles')
            ->first();

        return $applicationUser ? $applicationUser->pivot->role->name : null;
    }

    /**
     * Check if user can view payout methods based on their role
     */
    private function canViewPayoutMethods($userRole)
    {
        return in_array($userRole, ['single_mandate_user', 'payout_checker']);
    }

    /**
     * Get checker information for the application
     */
    private function getCheckerInfo(Application $application)
    {
        if ($application->payoutMandate && $application->payoutMandate->isDual()) {
            return [
                'name' => $application->payoutMandate->checker_name,
                'email' => $application->payoutMandate->checker_email
            ];
        }

        return null;
    }

    /**
     * Calculate combined donation statistics for all donation links of an application
     */
    private function calculateCombinedDonationStats($donationLinks): array
    {
        try {
            $donationStatsService = app(\App\Services\DonationStatisticsService::class);

            // Initialize combined totals
            $totalRaisedKes = 0;
            $totalContributors = 0;
            $allCurrencyBreakdown = [];
            $targetAmount = 0;
            $hasContributions = false;
            $exchangeRateUsed = $donationStatsService->getUsdToKesExchangeRate();

            // Individual donation link stats for detailed view
            $donationLinkStats = [];

            foreach ($donationLinks as $donationLink) {
                $linkStats = $donationStatsService->getStatistics($donationLink);
                $donationLinkStats[] = [
                    'donation_link' => $donationLink,
                    'stats' => $linkStats,
                ];

                // Accumulate totals
                $totalRaisedKes += $linkStats['total_raised_kes'];
                $totalContributors += $linkStats['total_contributors'];

                // Combine currency breakdowns
                foreach ($linkStats['currency_breakdown'] as $currency => $breakdown) {
                    if (!isset($allCurrencyBreakdown[$currency])) {
                        $allCurrencyBreakdown[$currency] = [
                            'count' => 0,
                            'total_amount' => 0,
                            'total_kes_equivalent' => 0,
                        ];
                    }

                    $allCurrencyBreakdown[$currency]['count'] += $breakdown['count'];
                    $allCurrencyBreakdown[$currency]['total_amount'] += $breakdown['total_amount'];
                    $allCurrencyBreakdown[$currency]['total_kes_equivalent'] += $breakdown['total_kes_equivalent'];
                }

                // Get target amount (should be same for all links in an application)
                if ($targetAmount === 0) {
                    $targetAmount = $linkStats['target_amount'];
                }

                if ($linkStats['has_contributions']) {
                    $hasContributions = true;
                }
            }

            // Calculate combined progress
            $progressData = $this->calculateCombinedProgress($totalRaisedKes, $targetAmount, $totalContributors);

            // Calculate average contribution
            $averageContributionKes = $totalContributors > 0 ? $totalRaisedKes / $totalContributors : 0;

            $combinedStats = [
                // Core statistics
                'total_raised_kes' => round($totalRaisedKes, 2),
                'total_raised_formatted' => number_format($totalRaisedKes, 2),
                'total_contributors' => $totalContributors,
                'average_contribution_kes' => round($averageContributionKes, 2),
                'average_contribution_formatted' => number_format($averageContributionKes, 2),

                // Progress data
                'target_amount' => $targetAmount,
                'target_amount_formatted' => number_format($targetAmount, 2),
                'progress_percentage' => $progressData['percentage'],
                'progress_type' => $progressData['type'],
                'remaining_to_target' => $progressData['remaining'],
                'remaining_to_target_formatted' => number_format($progressData['remaining'], 2),

                // Currency breakdown
                'currency_breakdown' => $allCurrencyBreakdown,
                'has_multiple_currencies' => count($allCurrencyBreakdown) > 1,

                // Donation link details
                'donation_link_stats' => $donationLinkStats,
                'total_donation_links' => count($donationLinks),
                'active_donation_links' => count(array_filter($donationLinks->toArray(), function ($link) {
                    return $link['status'] === 'active';
                })),

                // Meta information
                'exchange_rate_used' => $exchangeRateUsed,
                'last_updated' => now(),

                // Status flags
                'has_contributions' => $hasContributions,
                'has_target' => $targetAmount > 0,
                'target_reached' => $targetAmount > 0 && $totalRaisedKes >= $targetAmount,
            ];

            Log::info('Combined donation statistics calculated', [
                'application_id' => $donationLinks->first()->application_id ?? null,
                'total_raised_kes' => $combinedStats['total_raised_kes'],
                'total_contributors' => $combinedStats['total_contributors'],
                'total_donation_links' => $combinedStats['total_donation_links'],
                'progress_percentage' => $combinedStats['progress_percentage'],
            ]);

            return $combinedStats;
        } catch (\Exception $e) {
            Log::error('Failed to calculate combined donation statistics', [
                'error' => $e->getMessage(),
                'donation_links_count' => count($donationLinks),
                'trace' => $e->getTraceAsString(),
            ]);

            // Return default stats on error
            return $this->getDefaultCombinedStats($donationLinks);
        }
    }

    /**
     * Calculate combined progress based on total raised and target
     */
    private function calculateCombinedProgress(float $totalRaisedKes, float $targetAmount, int $totalContributors): array
    {
        if ($targetAmount > 0) {
            // Target-based progress
            $percentage = min(($totalRaisedKes / $targetAmount) * 100, 100);
            $remaining = max($targetAmount - $totalRaisedKes, 0);

            return [
                'percentage' => round($percentage, 2),
                'type' => 'target_based',
                'remaining' => $remaining,
            ];
        } else {
            // Activity-based progress (when no target is set)
            if ($totalContributors === 0) {
                $percentage = 0;
            } elseif ($totalContributors >= 200) {
                $percentage = 100; // Cap at 100% for 200+ contributors across all links
            } else {
                // Scale based on contributors and amount
                $contributorScore = min($totalContributors, 100); // Max 100% from contributors
                $amountScore = min($totalRaisedKes / 2000, 50); // Max 50% from amount (2K KES = 50%)
                $percentage = min($contributorScore + $amountScore, 100);
            }

            return [
                'percentage' => round($percentage, 2),
                'type' => 'activity_based',
                'remaining' => 0,
            ];
        }
    }

    /**
     * Get default combined statistics when calculation fails
     */
    private function getDefaultCombinedStats($donationLinks): array
    {
        return [
            'total_raised_kes' => 0,
            'total_raised_formatted' => '0.00',
            'total_contributors' => 0,
            'average_contribution_kes' => 0,
            'average_contribution_formatted' => '0.00',
            'target_amount' => 0,
            'target_amount_formatted' => '0.00',
            'progress_percentage' => 0,
            'progress_type' => 'target_based',
            'remaining_to_target' => 0,
            'remaining_to_target_formatted' => '0.00',
            'currency_breakdown' => [],
            'has_multiple_currencies' => false,
            'donation_link_stats' => [],
            'total_donation_links' => count($donationLinks),
            'active_donation_links' => 0,
            'exchange_rate_used' => 135.0,
            'last_updated' => now(),
            'has_contributions' => false,
            'has_target' => false,
            'target_reached' => false,
            'error' => true,
        ];
    }
}
