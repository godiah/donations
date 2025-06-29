<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\DonationLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DonationController extends Controller
{
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
     * Process donation (placeholder for future implementation)
     */
    public function process(Request $request, $code)
    {
        // Find the donation link
        $donationLink = DonationLink::where('code', $code)->first();

        if (!$donationLink || !$donationLink->isActive()) {
            return redirect()->back()->with('error', 'Invalid or expired donation link');
        }

        // Validate request
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|in:mpesa,card',
            'donor_name' => 'required|string|max:255',
            'donor_email' => 'required|email',
        ]);

        // TODO: Implement actual payment processing logic here
        // For now, just log the attempt
        Log::info('Donation attempt', [
            'code' => $code,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'donor_email' => $request->donor_email
        ]);

        return redirect()->back()->with('success', 'Payment processing will be implemented soon!');
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
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Get donation links for this application
        $donationLinks = DonationLink::where('application_id', $application->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get payout methods
        $payoutMethods = $application->applicant->payoutMethods()->get();

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
            'progressPercentage'
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
}
