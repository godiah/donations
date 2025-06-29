<?php

namespace App\Http\Controllers;

use App\Enums\ApplicationStatus;
use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Bank;
use App\Models\ContributionReason;
use App\Models\IdType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            ->limit(4)
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

        return view('dashboard', compact('user', 'recentApplications', 'totalActiveApplications'));
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
}
