<?php

namespace App\Http\Controllers;

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
