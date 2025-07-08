<?php

namespace App\Http\Controllers;

use App\Http\Requests\DonationRequest;
use App\Models\Application;
use App\Models\Contribution;
use App\Models\DonationLink;
use App\Models\Transaction;
use App\Services\CyberSourceService;
use App\Services\DonationStatisticsService;
use App\Services\MpesaService;
use App\Services\MpesaStatusChecker;
use App\Services\WalletService;
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

    public function __construct(
        CyberSourceService $cyberSourceService,
        MpesaService $mpesaService,
        WalletService $walletService,
        DonationStatisticsService $donationStatisticsService,
    ) {
        $this->cyberSourceService = $cyberSourceService;
        $this->mpesaService = $mpesaService;
        $this->walletService = $walletService;
        $this->donationStatisticsService = $donationStatisticsService;
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

        $stkPushEnabled = $donationLink->isStkPushEnabled();
        $paybillEnabled = $donationLink->isPaybillEnabled();

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

        return view('donations.form', compact('donationLink', 'application', 'applicant', 'paybillDetails', 'stkPushEnabled', 'paybillEnabled', 'contributionStats', 'progressPercentage'));
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

    /**
     * Handle CyberSource webhook
     */
    public function webhook(Request $request)
    {
        Log::info('CyberSource webhook received', $request->all());

        try {
            $result = $this->cyberSourceService->processWebhookResponse($request->all());

            if ($result['success']) {
                // If payment was successful, credit the wallet- handled in the service
                // $contribution = $result['contribution'];

                // if ($contribution && $contribution->payment_status === Contribution::STATUS_COMPLETED) {
                //     $this->creditWalletFromDonation($contribution);
                // }

                return response('OK', 200);
            }

            return response('Error processing webhook', 400);
        } catch (\Exception $e) {
            Log::error('Webhook processing failed', [
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
            ]);

            return response('Error processing webhook', 500);
        }
    }

    /**
     * Handle successful payment return
     */
    public function success(Request $request)
    {
        $contribution = null;

        if ($request->has('req_transaction_uuid')) {
            $contribution = Contribution::where('cybersource_request_id', $request->req_transaction_uuid)->first();
        }

        return view('donations.success', compact('contribution'));
    }

    /**
     * Handle cancelled payment return
     */
    public function cancel(Request $request)
    {
        $contribution = null;

        if ($request->has('req_transaction_uuid')) {
            $contribution = Contribution::where('cybersource_request_id', $request->req_transaction_uuid)->first();

            if ($contribution) {
                $contribution->update(['payment_status' => Contribution::STATUS_CANCELLED]);
            }
        }

        return view('donations.cancel', compact('contribution'));
    }

    /**
     * Handle error payment return
     */
    public function error(Request $request)
    {
        $contribution = null;

        if ($request->has('req_transaction_uuid')) {
            $contribution = Contribution::where('cybersource_request_id', $request->req_transaction_uuid)->first();

            if ($contribution) {
                $contribution->update(['payment_status' => Contribution::STATUS_FAILED]);
            }
        }

        return view('donations.error', compact('contribution'));
    }

    /**
     * Create contribution record
     */
    private function createContribution(DonationLink $donationLink, array $data): Contribution
    {
        return $donationLink->contributions()->create([
            'amount' => $data['amount'],
            'currency' => $data['currency'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'donation_type' => $data['donation_type'],
            'payment_method' => $data['payment_method'],
            'payment_status' => Contribution::STATUS_PENDING,
        ]);
    }

    /**
     * Handle card payment via CyberSource
     */
    private function handleCardPayment(Contribution $contribution): array
    {
        return $this->cyberSourceService->generatePaymentFormData($contribution);
    }

    /**
     * Redirect to CyberSource hosted payment page
     */
    private function redirectToCyberSource(array $paymentData)
    {
        Log::info('CyberSource Redirect Form Data', ['params' => $paymentData['params']]);
        return view('donations.cybersource-redirect', [
            'actionUrl' => $paymentData['action_url'],
            'params' => $paymentData['params'],
        ]);
    }

    /**
     * Handle M-Pesa payment (placeholder for future implementation)
     */
    protected function handleMpesaPayment(DonationLink $donationLink, Contribution $contribution, DonationRequest $request)
    {
        try {
            // Determine M-Pesa payment method (STK Push or Paybill)
            if ($donationLink->isPaybillEnabled() && $request->has('paybill_account_number')) {
                // Handle paybill payment
                return $this->handlePaybillPayment($donationLink, $contribution, $request);
            } elseif ($donationLink->isStkPushEnabled()) {
                // Handle STK Push payment
                return $this->handleStkPushPayment($contribution);
            } else {
                throw new \Exception('No valid M-Pesa payment method configured for this donation link');
            }
        } catch (\Exception $e) {
            Log::error('M-Pesa payment handling failed', [
                'contribution_id' => $contribution->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Handle STK Push payment
     */
    protected function handleStkPushPayment(Contribution $contribution)
    {
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

            return redirect()->back()
                ->withErrors(['error' => $result['error']])
                ->withInput();
        }
    }

    /**
     * Handle paybill payment
     */
    protected function handlePaybillPayment(DonationLink $donationLink, Contribution $contribution, DonationRequest $request)
    {
        $paybillMethod = $donationLink->paybillPayoutMethod;

        if (!$paybillMethod) {
            throw new \Exception('Paybill configuration not found');
        }

        // Validate provided account details
        $isValid = $this->mpesaService->validatePaybillAccount(
            $paybillMethod,
            $request->paybill_account_number,
            $request->paybill_account_name
        );

        if (!$isValid) {
            return redirect()->back()
                ->withErrors(['error' => 'Invalid paybill account details. Please check the account number and name.'])
                ->withInput();
        }

        $result = $this->mpesaService->processPaybill($contribution, $paybillMethod);

        if ($result['success']) {
            Log::info('Paybill payment initiated successfully', [
                'contribution_id' => $contribution->id,
                'paybill_number' => $result['paybill_details']['paybill_number'],
                'environment' => $this->mpesaService->getCurrentEnvironment(),
            ]);

            return view('donations.mpesa-paybill', [
                'contribution' => $contribution,
                'paybill_details' => $result['paybill_details'],
                'instructions' => $result['instructions'],
                'transaction' => $result['transaction'],
                'environment' => $this->mpesaService->getCurrentEnvironment(),
            ]);
        } else {
            Log::error('Paybill payment setup failed', [
                'contribution_id' => $contribution->id,
                'error' => $result['error'],
                'environment' => $this->mpesaService->getCurrentEnvironment(),
            ]);

            return redirect()->back()
                ->withErrors(['error' => $result['error']])
                ->withInput();
        }
    }

    /**
     * M-Pesa callback handler
     */
    public function mpesaCallback(Request $request)
    {
        // Log everything about the incoming request
        Log::info('Raw M-Pesa callback received', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'headers' => $request->headers->all(),
            'body' => $request->getContent(),
            'all_data' => $request->all(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now(),
        ]);

        Log::info('M-Pesa callback received', [
            'data' => $request->all(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'environment' => $this->mpesaService->getCurrentEnvironment(),
        ]);

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

                // Auto-acknowledge if enabled
                // if (config('mpesa.webhooks.auto_acknowledge', true)) {
                //     return response()->json([
                //         'ResultCode' => 0,
                //         'ResultDesc' => 'Accepted'
                //     ], 200);
                // }

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

    public function manualStatusCheck(Transaction $transaction)
    {
        $statusChecker = app(MpesaStatusChecker::class);
        $statusChecker->checkTransactionStatus($transaction);

        return response()->json([
            'message' => 'Status check initiated',
            'transaction' => $transaction->fresh()
        ]);
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
     * Check STK Push status (for AJAX polling)
     */
    public function checkStkPushStatus(Request $request)
    {
        $request->validate([
            'checkout_request_id' => 'required|string',
        ]);

        try {
            $result = $this->mpesaService->queryStkPushStatus($request->checkout_request_id);

            Log::info('STK Push status check', [
                'checkout_request_id' => $request->checkout_request_id,
                'success' => $result['success'],
                'environment' => $this->mpesaService->getCurrentEnvironment(),
            ]);

            return response()->json([
                'success' => $result['success'],
                'data' => $result['data'] ?? null,
                'error' => $result['error'] ?? null,
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
            $payoutMethods = $application->applicant->payoutMethods()->get();
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


    public function verifyCyberSource()
    {
        $verificationResult = $this->cyberSourceService->verifyKeysAndTimestamp();
        $timestampResult = $this->cyberSourceService->verifyTimestampAdvanced();
        $keyCleaningResult = $this->cyberSourceService->cleanKeys();

        return response()->json([
            'overall_status' => $verificationResult['status'],
            'issues' => $verificationResult['issues'],
            'warnings' => $verificationResult['warnings'],
            'verification_details' => $verificationResult['verification_details'],
            'timestamp_verification' => $timestampResult,
            'key_cleaning' => $keyCleaningResult,
            'recommendations' => $this->getRecommendations($verificationResult, $keyCleaningResult)
        ], 200);
    }

    private function getRecommendations(array $verificationResult, array $keyCleaningResult): array
    {
        $recommendations = [];

        if (!empty($verificationResult['issues'])) {
            $recommendations[] = "❌ CRITICAL ISSUES FOUND - Must be fixed before CyberSource will work";

            foreach ($verificationResult['issues'] as $issue) {
                if (strpos($issue, 'length') !== false) {
                    $recommendations[] = "🔑 Re-copy your keys from CyberSource Business Center";
                    $recommendations[] = "📋 Use a plain text editor (Notepad) to avoid formatting issues";
                }

                if (strpos($issue, 'word-wrapped') !== false) {
                    $recommendations[] = "🧹 Your keys contain spaces/newlines - they were word-wrapped during copy/paste";
                    $recommendations[] = "💡 Use the cleaned keys from the key_cleaning section above";
                }

                if (strpos($issue, 'time') !== false) {
                    $recommendations[] = "⏰ Your server time is not synchronized with UTC";
                    $recommendations[] = "🔧 Run: sudo ntpdate -s time.nist.gov (Linux) or sync your server time";
                }
            }
        }

        if ($keyCleaningResult['keys_were_dirty']) {
            $recommendations[] = "🚨 Your keys were word-wrapped! Update your .env with the cleaned versions";
        }

        if (empty($verificationResult['issues']) && empty($recommendations)) {
            $recommendations[] = "✅ Keys and timestamp look good - the issue is likely profile configuration";
            $recommendations[] = "🔍 Check that your CyberSource profile is ACTIVATED in Business Center";
        }

        return $recommendations;
    }
}
