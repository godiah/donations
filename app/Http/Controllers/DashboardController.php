<?php

namespace App\Http\Controllers;

use App\Enums\ApplicationStatus;
use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Bank;
use App\Models\ContributionReason;
use App\Models\DonationLink;
use App\Models\IdType;
use App\Models\User;
use App\Services\DonationStatisticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    // View dashboard
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get user's latest 4 applications that are submitted or under review
        $recentApplications = Application::with(['applicant', 'reviewer'])
            ->whereNotNull('submitted_at')
            ->whereIn('status', [ApplicationStatus::Submitted, ApplicationStatus::UnderReview])
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id) // maker
                    ->orWhereHas('payoutMandate', function ($subQuery) use ($user) {
                        $subQuery->where('checker_id', $user->id); // checker 
                    });
            })
            ->orderBy('submitted_at', 'desc')
            ->limit(2)
            ->get();

        // Count total applications for the "View All" button context
        $totalActiveApplications = Application::whereNotNull('submitted_at')
            ->whereIn('status', [ApplicationStatus::Submitted, ApplicationStatus::UnderReview])
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhereHas('payoutMandate', function ($subQuery) use ($user) {
                        $subQuery->where('checker_id', $user->id);
                    });
            })
            ->count();

        // Calculate donation statistics for the user
        $donationStats = $this->calculateUserDonationStatsWithCaching($user);

        return view('dashboard', compact('user', 'recentApplications', 'totalActiveApplications', 'donationStats'));
    }

    // View individual donation application form
    public function showIndividual()
    {
        $contributionReasons = ContributionReason::where('is_active', true)->get();
        $idTypes = IdType::where('is_active', true)->get();

        return view('applications.individuals.index', compact('contributionReasons', 'idTypes'));
    }

    // View company donation application form
    public function showCompany()
    {
        $contributionReasons = ContributionReason::where('is_active', true)->get();
        $banks = Bank::all();

        return view('applications.companies.index', compact('contributionReasons', 'banks'));
    }

    private function calculateUserDonationStatsWithCaching(User $user): array
    {
        $cacheKey = "user_donation_stats_{$user->id}";
        $cacheDuration = 15; // minutes

        return Cache::remember($cacheKey, now()->addMinutes($cacheDuration), function () use ($user) {
            return $this->calculateUserDonationStats($user);
        });
    }

    private function calculateUserDonationStats(User $user): array
    {
        try {
            // Get all donation links for approved applications created by this user
            $userDonationLinks = DonationLink::whereHas('application', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->where('status', ApplicationStatus::Approved);
            })
                ->with('application') // Eager load to avoid N+1 queries
                ->get();

            $totalRaisedKes = 0;
            $activeCampaigns = 0;

            // Initialize donation statistics service
            $donationStatsService = new DonationStatisticsService();

            foreach ($userDonationLinks as $donationLink) {
                // Only count active donation links (not expired, not disabled)
                if ($this->isDonationLinkActive($donationLink)) {
                    $activeCampaigns++;

                    // Get statistics for this donation link
                    $linkStats = $donationStatsService->getStatistics($donationLink);
                    $totalRaisedKes += $linkStats['total_raised_kes'];
                }
            }

            Log::info('User donation statistics calculated', [
                'user_id' => $user->id,
                'active_campaigns' => $activeCampaigns,
                'total_raised_kes' => $totalRaisedKes,
            ]);

            return [
                'userCampaigns' => $activeCampaigns,
                'totalRaised' => $totalRaisedKes,
            ];
        } catch (\Exception $e) {
            Log::error('Failed to calculate user donation statistics for dashboard', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Return default values on error
            return [
                'userCampaigns' => 0,
                'totalRaised' => 0,
            ];
        }
    }

    private function isDonationLinkActive(DonationLink $donationLink): bool
    {
        // Check if donation link is not expired
        if ($donationLink->expires_at && $donationLink->expires_at->isPast()) {
            return false;
        }

        // Check if donation link status is active (if you have this field)
        if (isset($donationLink->status) && $donationLink->status !== 'active') {
            return false;
        }

        // Check if the associated application is approved
        if ($donationLink->application->status !== ApplicationStatus::Approved) {
            return false;
        }

        return true;
    }
}
