<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Individual;
use App\Models\Company;
use App\Enums\ApplicationStatus;
use App\Models\DonationLink;
use App\Models\SupportDocument;
use App\Notifications\CompanyApplicationApproved;
use App\Notifications\IndividualApplicationApproved;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

        // For individual applicants, load KYC verification data
        if ($application->applicant_type === 'App\\Models\\Individual') {
            $application->applicant->load(['kycVerifications' => function ($query) use ($application) {
                $query->where('application_id', $application->id)
                    ->orderBy('created_at', 'desc');
            }]);
        }

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
     * Mark application as approved
     */
    public function approve(Request $request, $application)
    {
        $application = Application::with(['applicant', 'applicant.supportDocuments', 'applicant.kycVerifications'])->findOrFail($application);

        // Optional: Add authorization check
        // $this->authorize('update', $application);

        try {
            DB::beginTransaction();

            // Verify document requirements based on application type
            $validationResult = $this->validateDocumentsForApproval($application);

            if (!$validationResult['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => $validationResult['message']
                ], 422);
            }

            // Update application status
            $application->update([
                'status' => ApplicationStatus::Approved,
                'reviewer_id' => Auth::id(),
                'reviewed_at' => now(),
            ]);

            // Generate donation link
            $donationLink = $this->generateDonationLink($application);

            // Send appropriate notification based on applicant type
            $this->sendApprovalNotification($application, $donationLink);

            DB::commit();

            Log::info('Application approved successfully', [
                'application_id' => $application->id,
                'application_number' => $application->application_number,
                'applicant_type' => $application->applicant_type,
                'reviewer_id' => Auth::id(),
                'donation_link_code' => $donationLink->code
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Application approved successfully. Confirmation notifications have been sent.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to approve application', [
                'application_id' => $application->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to approve application. Please try again.'
            ], 500);
        }
    }

    /**
     * Validate documents for approval based on application type
     */
    private function validateDocumentsForApproval($application)
    {
        $applicant = $application->applicant;
        $supportDocuments = $applicant->supportDocuments;

        // Check support documents
        $totalDocuments = $supportDocuments->count();
        $verifiedDocuments = $supportDocuments->where('status', 'verified')->count();

        if ($totalDocuments === 0) {
            return [
                'valid' => false,
                'message' => 'Cannot approve: No documents have been uploaded'
            ];
        }

        if ($totalDocuments !== $verifiedDocuments) {
            return [
                'valid' => false,
                'message' => 'Cannot approve: Not all support documents are verified'
            ];
        }

        // Additional validation for individual applications (KYC/National ID)
        if ($application->applicant_type === 'App\\Models\\Individual') {
            $kycVerifications = $applicant->kycVerifications()
                ->where('application_id', $application->id)
                ->latest()
                ->first();

            if (!$kycVerifications || $kycVerifications->status !== 'verified') {
                return [
                    'valid' => false,
                    'message' => 'Cannot approve: National ID verification is required for individual applications'
                ];
            }
        }

        return [
            'valid' => true,
            'message' => 'All requirements met'
        ];
    }

    /**
     * Generate donation link for approved application
     */
    private function generateDonationLink($application)
    {
        // Set expiration to 1 year from now (configurable)
        $expirationDate = now()->addYear();

        $donationLink = DonationLink::create([
            'application_id' => $application->id,
            'expires_at' => $expirationDate,
            'created_by' => Auth::id(),
        ]);

        Log::info('Donation link generated', [
            'application_id' => $application->id,
            'donation_link_id' => $donationLink->id,
            'code' => $donationLink->code,
            'expires_at' => $expirationDate
        ]);

        return $donationLink;
    }

    /**
     * Send approval notification based on applicant type
     */
    private function sendApprovalNotification($application, $donationLink)
    {
        $applicant = $application->applicant;
        $user = $applicant->user;

        if ($application->applicant_type === 'App\\Models\\Individual') {
            // Send individual approval notification (email + SMS)
            $user->notify(new IndividualApplicationApproved($application, $applicant, $donationLink));

            Log::info('Individual approval notification sent', [
                'application_id' => $application->id,
                'individual_id' => $applicant->id,
                'user_id' => $user->id,
                'donation_link_code' => $donationLink->code
            ]);
        } elseif ($application->applicant_type === 'App\\Models\\Company') {
            // Send company approval notification (email only)
            $user->notify(new CompanyApplicationApproved($application, $applicant, $donationLink));

            Log::info('Company approval notification sent', [
                'application_id' => $application->id,
                'company_id' => $applicant->id,
                'user_id' => $user->id,
                'donation_link_code' => $donationLink->code
            ]);
        }
    }
}
