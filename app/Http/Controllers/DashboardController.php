<?php

namespace App\Http\Controllers;

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

        // Get user's applications with related data
        $applications = Application::with(['applicant', 'reviewer'])
            ->where('user_id', $user->id)
            ->whereNotNull('submitted_at') // Only show submitted applications
            ->orderBy('submitted_at', 'desc')
            ->get();

        return view('dashboard', compact('user', 'applications'));
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
