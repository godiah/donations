<?php

namespace App\Http\Controllers;

use App\Http\Requests\DonationRequest;
use App\Models\Application;
use App\Models\Contribution;
use App\Models\DonationLink;
use App\Services\CyberSourceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DonationController extends Controller
{
    protected $cyberSourceService;

    public function __construct(CyberSourceService $cyberSourceService)
    {
        $this->cyberSourceService = $cyberSourceService;
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

        // Record the access
        $donationLink->recordAccess();

        // Get application and contribution details
        $application = $donationLink->application;
        $applicant = $application->applicant;

        // Log successful access
        Log::info('Donation link accessed', [
            'code' => $code,
            'application_id' => $application->id,
            'access_count' => $donationLink->access_count
        ]);

        return view('donations.form', compact('donationLink', 'application', 'applicant'));
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
                // Handle M-Pesa payment (implement as needed)
                DB::commit();
                return $this->handleMpesaPayment($contribution);
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

        $result = $this->cyberSourceService->processWebhookResponse($request->all());

        if ($result['success']) {
            return response('OK', 200);
        }

        return response('Error processing webhook', 400);
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
    private function handleMpesaPayment(Contribution $contribution)
    {
        // Implement M-Pesa STK Push or similar
        // For now, redirect to a placeholder page
        return view('donation.mpesa', compact('contribution'));
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

        // Calculate total collected (you may need to adjust this based on your donations table)
        $totalCollected = 0; // Implement based on your donation records
        $targetAmount = $application->applicant->target_amount ?? 0;
        $progressPercentage = $targetAmount > 0 ? min(($totalCollected / $targetAmount) * 100, 100) : 0;

        return view('donations.show', compact(
            'application',
            'donationLinks',
            'payoutMethods',
            'totalCollected',
            'targetAmount',
            'progressPercentage',
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
}
