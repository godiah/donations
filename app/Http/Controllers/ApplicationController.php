<?php

namespace App\Http\Controllers;

use App\Enums\ApplicationStatus;
use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Company;
use App\Models\SupportDocument;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display all active applications (submitted/under review)
     */
    public function active(Request $request)
    {
        $user = Auth::user();
        $perPage = $request->get('per_page', 10);
        $status = $request->get('status');

        $query = Application::with(['applicant', 'reviewer'])
            ->whereNotNull('submitted_at')
            ->whereIn('status', [ApplicationStatus::Submitted, ApplicationStatus::UnderReview])
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhereHas('payoutMandate', function ($subQuery) use ($user) {
                        $subQuery->where('checker_id', $user->id);
                    });
            });

        // Filter by specific status if provided
        if ($status && in_array($status, ['submitted', 'under_review'])) {
            $query->where('status', ApplicationStatus::from($status));
        }

        $applications = $query->orderBy('submitted_at', 'desc')
            ->paginate($perPage);

        return view('applications.active', compact('applications', 'status'));
    }

    /**
     * Display approved applications (My Donations)
     */
    public function donations(Request $request)
    {
        $user = Auth::user();
        $perPage = $request->get('per_page', 10);

        $donations = Application::with(['applicant', 'reviewer', 'payoutMandate'])
            ->whereNotNull('submitted_at')
            ->where('status', ApplicationStatus::Approved)
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhereHas('payoutMandate', function ($subQuery) use ($user) {
                        $subQuery->where('checker_id', $user->id);
                    });
            })
            ->orderBy('reviewed_at', 'desc')
            ->paginate($perPage);

        return view('donations.index', compact('donations'));
    }

    /**
     * Display applications requiring attention (rejected, cancelled, additional info)
     */
    public function pending(Request $request)
    {
        $user = Auth::user();
        $perPage = $request->get('per_page', 10);
        $status = $request->get('status');

        $query = Application::with(['applicant', 'reviewer'])
            ->whereNotNull('submitted_at')
            ->whereIn('status', [
                ApplicationStatus::Rejected,
                ApplicationStatus::Cancelled,
                ApplicationStatus::AdditionalInfoRequired
            ])
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhereHas('payoutMandate', function ($subQuery) use ($user) {
                        $subQuery->where('checker_id', $user->id);
                    });
            });

        // Filter by specific status if provided
        if ($status && in_array($status, ['rejected', 'cancelled', 'additional_info_required'])) {
            $query->where('status', ApplicationStatus::from($status));
        }

        $pendingApplications = $query->orderBy('reviewed_at', 'desc')
            ->paginate($perPage);

        return view('applications.pending', compact('pendingApplications', 'status'));
    }

    /**
     * Display the specified individual application by application number
     */
    public function showIndividual(string $application_number)
    {
        $user = Auth::user();

        // Find application by application_number
        $application = Application::with([
            'applicant.contributionReason',
            'applicant.idType',
            'applicant.supportDocuments.contributionReason',
            'reviewer',
            'payoutMandate'
        ])
            ->where('application_number', $application_number)
            ->firstOrFail();

        // Check if user owns this application or is admin or is a checker
        $this->authorize('view', $application);

        // Get support documents through the applicant
        $supportDocuments = $application->applicant->supportDocuments()
            ->with('contributionReason')
            ->get();

        return view('applications.individuals.show', compact('application', 'supportDocuments'));
    }

    /**
     * Display the specified company application by application number
     */
    public function showCompany(string $application_number)
    {
        $user = Auth::user();

        // Find application by application_number
        $application = Application::with([
            'applicant.contributionReason',
            'applicant.bank',
            'applicant.supportDocuments.contributionReason',
            'reviewer',
            'payoutMandate'
        ])
            ->where('application_number', $application_number)
            ->firstOrFail();

        // Check if user owns this application or is admin or is a checker
        $this->authorize('view', $application);

        $supportDocuments = $application->applicant->supportDocuments()
            ->with('contributionReason')
            ->get();

        return view('applications.companies.show', compact('application', 'supportDocuments'));
    }

    // Placeholder for file download (to be implemented separately)
    public function download(Request $request, Application $application, $file)
    {
        // Implementation depends on file storage setup
        abort(501, 'File download not implemented.');
    }

    public function downloadSupport(Request $request, Application $application, SupportDocument $document)
    {
        // Implementation depends on file storage setup
        abort(501, 'Support document download not implemented.');
    }
}
