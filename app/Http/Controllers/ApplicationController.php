<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    /**
     * Display the specified application by application number
     */
    public function show(string $application_number)
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
}
