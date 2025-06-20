<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Company;
use App\Models\SupportDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    /**
     * Display the specified application by application number
     */
    public function showIndividual(string $application_number)
    {
        $user = Auth::user();

        // Find application by application_number
        $application = Application::with([
            'applicant.contributionReason',
            'applicant.idType',
            'applicant.supportDocuments.contributionReason',
            'reviewer'
        ])
            ->where('application_number', $application_number)
            ->firstOrFail();

        // Check if user owns this application or is admin
        if (!$user->hasRole('admin') && $application->user_id !== $user->id) {
            abort(403, 'Unauthorized to view this application.');
        }

        // Get support documents through the applicant
        $supportDocuments = $application->applicant->supportDocuments()
            ->with('contributionReason')
            ->get();

        return view('applications.individuals.show', compact('application', 'supportDocuments'));
    }

    public function showCompany(string $application_number)
    {
        $user = Auth::user();

        // Find application by application_number
        $application = Application::with([
            'applicant.contributionReason',
            'applicant.bank',
            'applicant.supportDocuments.contributionReason',
            'reviewer'
        ])
            ->where('application_number', $application_number)
            ->firstOrFail();

        // Ensure the application belongs to the authenticated user and is a company application
        if (!$user->hasRole('admin') && $application->user_id !== $user->id) {
            abort(403, 'Unauthorized to view this application.');
        }

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
