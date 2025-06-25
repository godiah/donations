<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Individual;
use App\Models\Company;
use App\Enums\ApplicationStatus;
use App\Models\SupportDocument;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ApplicationController extends Controller
{
    /**
     * Display all applications with filtering capabilities
     */
    public function index(Request $request)
    {
        $type = $request->get('type', 'all'); // all, individual, company
        $status = $request->get('status', 'all'); // all, submitted, under_review, etc.

        // Get applications with eager loading
        $applications = $this->getFilteredApplications($type, $status);

        // Get counts for tabs
        $counts = $this->getApplicationCounts();

        return view('admin.applications.index', compact('applications', 'type', 'status', 'counts'));
    }

    /**
     * Show application details
     */
    public function show(Application $application)
    {
        $application->load(['users', 'applicant', 'reviewer']);

        // Get support documents through the applicant
        $documents = $application->applicant->supportDocuments()->get();

        return view('admin.applications.show', compact('application', 'documents'));
    }

    /**
     * Filter applications by type (AJAX endpoint)
     */
    public function filterByType(Request $request, string $type)
    {
        $status = $request->get('status', 'all');
        $applications = $this->getFilteredApplications($type, $status);

        return response()->json([
            'html' => view('admin.applications.partials.applications-table', compact('applications'))->render()
        ]);
    }

    /**
     * Filter applications by status (AJAX endpoint)  
     */
    public function filterByStatus(Request $request, string $status)
    {
        $type = $request->get('type', 'all');
        $applications = $this->getFilteredApplications($type, $status);

        return response()->json([
            'html' => view('admin.applications.partials.applications-table', compact('applications'))->render()
        ]);
    }

    /**
     * Filter applications by both type and status (AJAX endpoint)
     */
    public function filterByTypeAndStatus(string $type, string $status)
    {
        $applications = $this->getFilteredApplications($type, $status);

        return response()->json([
            'html' => view('admin.applications.partials.applications-table', compact('applications'))->render()
        ]);
    }

    /**
     * Get filtered applications based on type and status
     */
    private function getFilteredApplications(string $type, string $status)
    {
        $query = Application::with(['users', 'applicant', 'reviewer'])
            ->orderBy('created_at', 'desc');

        // Filter by applicant type
        if ($type !== 'all') {
            $applicantType = $type === 'individual' ? Individual::class : Company::class;
            $query->where('applicant_type', $applicantType);
        }

        // Filter by status
        if ($status !== 'all') {
            $statusMap = [
                'submitted' => ApplicationStatus::Submitted,
                'under_review' => ApplicationStatus::UnderReview,
                'additional_info' => ApplicationStatus::AdditionalInfoRequired,
                'approved' => ApplicationStatus::Approved,
                'rejected' => ApplicationStatus::Rejected,
            ];

            if (isset($statusMap[$status])) {
                $query->where('status', $statusMap[$status]);
            }
        }

        return $query->paginate(20);
    }

    /**
     * Get application counts for tab badges
     */
    private function getApplicationCounts(): array
    {
        $statusCounts = Application::selectRaw('status, applicant_type, COUNT(*) as count')
            ->groupBy(['status', 'applicant_type'])
            ->get()
            ->groupBy(['applicant_type', 'status']);

        return [
            'individual' => [
                'all' => Application::where('applicant_type', Individual::class)->count(),
                'submitted' => $statusCounts[Individual::class][ApplicationStatus::Submitted->value][0]->count ?? 0,
                'under_review' => $statusCounts[Individual::class][ApplicationStatus::UnderReview->value][0]->count ?? 0,
                'additional_info' => $statusCounts[Individual::class][ApplicationStatus::AdditionalInfoRequired->value][0]->count ?? 0,
                'approved' => $statusCounts[Individual::class][ApplicationStatus::Approved->value][0]->count ?? 0,
                'rejected' => $statusCounts[Individual::class][ApplicationStatus::Rejected->value][0]->count ?? 0,
            ],
            'company' => [
                'all' => Application::where('applicant_type', Company::class)->count(),
                'submitted' => $statusCounts[Company::class][ApplicationStatus::Submitted->value][0]->count ?? 0,
                'under_review' => $statusCounts[Company::class][ApplicationStatus::UnderReview->value][0]->count ?? 0,
                'additional_info' => $statusCounts[Company::class][ApplicationStatus::AdditionalInfoRequired->value][0]->count ?? 0,
                'approved' => $statusCounts[Company::class][ApplicationStatus::Approved->value][0]->count ?? 0,
                'rejected' => $statusCounts[Company::class][ApplicationStatus::Rejected->value][0]->count ?? 0,
            ],
            'total' => [
                'all' => Application::count(),
                'submitted' => Application::where('status', ApplicationStatus::Submitted)->count(),
                'under_review' => Application::where('status', ApplicationStatus::UnderReview)->count(),
                'additional_info' => Application::where('status', ApplicationStatus::AdditionalInfoRequired)->count(),
                'approved' => Application::where('status', ApplicationStatus::Approved)->count(),
                'rejected' => Application::where('status', ApplicationStatus::Rejected)->count(),
            ]
        ];
    }

    /**
     * Start Review
     */
    public function startReview(Request $request, $application)
    {
        $application = Application::findOrFail($application);

        // Optional: Add authorization check
        // $this->authorize('update', $application);

        $application->update([
            'status' => 'under_review',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Application under review'
        ]);
    }

    /**
     * Display private support documents
     */
    public function serveDocument($document)
    {
        $document = SupportDocument::findOrFail($document);

        // Optional: Add authorization check
        // $this->authorize('view', $document);

        $filePath = $document->file_path;

        if (!Storage::disk('private')->exists($filePath)) {
            abort(404, 'File not found');
        }

        $file = Storage::disk('private')->get($filePath);
        $mimeType = Storage::disk('private')->mimeType($filePath);

        return response($file, 200)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'inline; filename="' . $document->original_filename . '"');
    }

    /**
     * Serve company documents from private disk
     */
    public function serveCompanyDocument(Company $company, $field)
    {
        // Validate the field to ensure it's one of the allowed document fields
        $allowedFields = ['registration_certificate', 'cr12', 'bank_account_proof'];

        if (!in_array($field, $allowedFields)) {
            abort(404, 'Invalid document field');
        }

        // Get the file path from the company model
        $filePath = $company->{$field};

        if (!$filePath || !Storage::disk('private')->exists($filePath)) {
            abort(404, 'File not found');
        }

        // Optional: Add authorization check
        // $this->authorize('view', $company);

        $file = Storage::disk('private')->get($filePath);
        $mimeType = Storage::disk('private')->mimeType($filePath);

        // Extract original filename from path or create a friendly name
        $originalFilename = basename($filePath);

        return response($file, 200)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'inline; filename="' . $originalFilename . '"');
    }

    /**
     * Verify support documents
     */
    public function updateDocumentStatus(Request $request, $document)
    {
        $request->validate([
            'status' => 'required|in:verified,rejected',
            'verification_notes' => 'nullable|string|max:1000',
        ]);

        $document = SupportDocument::findOrFail($document);

        // Optional: Add authorization check
        // $this->authorize('update', $document);

        $document->update([
            'status' => $request->status,
            'verification_notes' => $request->verification_notes,
            'verified_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Document status updated to ' . $request->status,
        ]);
    }

    /**
     * Mark as approved
     */
    public function approve(Request $request, $application)
    {
        $application = Application::findOrFail($application);

        // Optional: Add authorization check
        // $this->authorize('update', $application);

        // Verify all documents are verified
        $totalDocuments = $application->applicant->supportDocuments->count();
        $verifiedDocuments = $application->applicant->supportDocuments->where('status', 'verified')->count();

        if ($totalDocuments === 0 || $totalDocuments !== $verifiedDocuments) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot approve: Not all documents are verified'
            ], 422);
        }

        $application->update([
            'status' => ApplicationStatus::Approved,
            'reviewer_id' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Application approved successfully'
        ]);
    }
}
