<?php

namespace App\Http\Controllers;

use App\Enums\ApplicationStatus;
use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Bank;
use App\Models\Company;
use App\Models\SupportDocument;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Notifications\IndividualApplicationUpdated;
use App\Models\Individual;
use App\Notifications\CompanyApplicationUpdated;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
     * Display applications requiring attention (rejected, resubmitted, additional info)
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
                ApplicationStatus::Resubmitted,
                ApplicationStatus::AdditionalInfoRequired
            ])
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhereHas('payoutMandate', function ($subQuery) use ($user) {
                        $subQuery->where('checker_id', $user->id);
                    });
            });

        // Filter by specific status if provided
        if ($status && in_array($status, ['rejected', 'resubmitted', 'additional_info_required'])) {
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

    /**
     * Display the update form for individual application
     */
    public function updateIndividual(string $application_number)
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

        // Check if user owns this application
        $this->authorize('update', $application);

        // Only allow updates for applications requiring additional info
        if ($application->status !== \App\Enums\ApplicationStatus::AdditionalInfoRequired) {
            abort(403, 'This application cannot be updated at this time.');
        }

        // Get support documents through the applicant
        $supportDocuments = $application->applicant->supportDocuments()
            ->with('contributionReason')
            ->get();

        // Get ID types for the dropdown
        $idTypes = \App\Models\IdType::all();

        return view('applications.individuals.update', compact('application', 'supportDocuments', 'idTypes'));
    }

    /**
     * Update the individual application
     */
    public function updateIndividualStore(Request $request, string $application_number)
    {
        $user = Auth::user();

        // Find application by application_number
        $application = Application::with(['applicant'])
            ->where('application_number', $application_number)
            ->firstOrFail();

        // Check if user owns this application
        $this->authorize('update', $application);

        // Only allow updates for applications requiring additional info
        if ($application->status !== \App\Enums\ApplicationStatus::AdditionalInfoRequired) {
            return response()->json([
                'success' => false,
                'message' => 'This application cannot be updated at this time.'
            ], 403);
        }

        // Validate the request
        $validator = $this->validateIndividualUpdate($request);

        if ($validator->fails()) {
            Log::error('Validation failed for individual application update.', [
                'errors' => $validator->errors()->toArray(),
                'input' => $request->all(),
                'application_number' => $application_number,
            ]);

            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Update the individual record
            $this->updateIndividualData($request, $application->applicant);

            // Handle document uploads if provided
            $this->handleDocumentUpdates($request, $application->applicant);

            // Update application status back to submitted for re-review
            $application->update([
                'status' => \App\Enums\ApplicationStatus::Resubmitted,
                'reviewed_at' => null,
                'admin_comments' => null
            ]);

            DB::commit();

            // Send notification about the update
            Auth::user()->notify(new IndividualApplicationUpdated($application, $application->applicant));

            return response()->json([
                'success' => true,
                'message' => 'Application updated successfully! It has been resubmitted for review.',
                'redirect_url' => route('individual.applications.show', $application->application_number)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error updating individual application.', [
                'error' => $e->getMessage(),
                'application_number' => $application_number,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating your application. Please try again.'
            ], 500);
        }
    }

    /**
     * Validate individual application update
     */
    private function validateIndividualUpdate(Request $request)
    {
        // Normalize phone numbers
        $request->merge([
            'phone' => $this->normalizePhoneNumber($request->phone),
        ]);

        $rules = [
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|regex:/^\+254\d{9}$/',
            'id_type_id' => 'required|exists:id_types,id',
            'id_number' => 'required|string|max:50',
            'kra_pin' => 'nullable|string|max:20',

            // Support documents (optional for updates)
            'support_documents' => 'nullable|array',
            'support_documents.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
            'remove_documents' => 'nullable|array',
            'remove_documents.*' => 'exists:support_documents,id'
        ];

        return Validator::make($request->all(), $rules);
    }

    /**
     * Update individual data
     */
    private function updateIndividualData(Request $request, Individual $individual)
    {
        $individual->update([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'id_type_id' => $request->id_type_id,
            'id_number' => $request->id_number,
            'kra_pin' => $request->kra_pin,
        ]);
    }

    /**
     * Handle document updates (add new, remove existing)
     */
    private function handleDocumentUpdates(Request $request, Individual $individual)
    {
        // Remove documents if requested
        if ($request->has('remove_documents')) {
            $documentsToRemove = SupportDocument::whereIn('id', $request->remove_documents)
                ->where('documentable_id', $individual->id)
                ->where('documentable_type', Individual::class)
                ->get();

            foreach ($documentsToRemove as $document) {
                // Delete file from storage
                if (Storage::disk('private')->exists($document->file_path)) {
                    Storage::disk('private')->delete($document->file_path);
                }
                // Delete database record
                $document->delete();
            }
        }

        // Add new documents if provided
        if ($request->hasFile('support_documents')) {
            foreach ($request->file('support_documents') as $file) {
                $originalName = $file->getClientOriginalName();
                $storedName = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('support-documents', $storedName, 'private');
                $fileHash = hash_file('sha256', $file->getPathname());

                SupportDocument::create([
                    'documentable_type' => Individual::class,
                    'documentable_id' => $individual->id,
                    'contribution_reason_id' => $individual->contribution_reason_id,
                    'original_filename' => $originalName,
                    'stored_filename' => $storedName,
                    'file_path' => $filePath,
                    'file_hash' => $fileHash,
                    'status' => 'pending',
                ]);
            }
        }
    }

    /**
     * Normalize phone number
     */
    private function normalizePhoneNumber(?string $phone): ?string
    {
        if (!$phone) {
            return null;
        }

        // Remove spaces and special characters
        $phone = preg_replace('/\D+/', '', $phone);

        // If already starts with country code
        if (Str::startsWith($phone, '254')) {
            return '+' . $phone;
        }

        // If starts with 0, replace with +254
        if (Str::startsWith($phone, '0')) {
            return '+254' . substr($phone, 1);
        }

        // Fallback
        return $phone;
    }

    /**
     * Show the update form for company application
     */
    public function updateCompany(string $application_number)
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

        // Only allow updates for applications requiring additional info
        if ($application->status !== \App\Enums\ApplicationStatus::AdditionalInfoRequired) {
            abort(403, 'This application cannot be updated at this time.');
        }

        $supportDocuments = $application->applicant->supportDocuments()
            ->with('contributionReason')
            ->get();

        // Get banks for dropdown
        $banks = Bank::orderBy('display_name')->get();

        return view('applications.companies.update', compact('application', 'supportDocuments', 'banks'));
    }

    /**
     * Update the specified company application
     */
    public function updateCompanyStore(Request $request, string $application_number)
    {
        $user = Auth::user();

        // Find application by application_number
        $application = Application::with(['applicant'])
            ->where('application_number', $application_number)
            ->firstOrFail();

        // Check if user owns this application
        $this->authorize('view', $application);

        // Only allow updates if status is AdditionalInfoRequired
        if ($application->status !== \App\Enums\ApplicationStatus::AdditionalInfoRequired) {
            return response()->json([
                'success' => false,
                'message' => 'This application cannot be updated at this time.'
            ], 403);
        }

        // Validate the update request
        $validator = $this->validateCompanyUpdate($request);

        if ($validator->fails()) {
            Log::error('Validation failed for company application update.', [
                'errors' => $validator->errors()->toArray(),
                'input' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $company = $application->applicant;
            $updateData = [];

            // Update company information fields if provided
            if ($request->filled('pin_number')) {
                $updateData['pin_number'] = $request->pin_number;
            }

            if ($request->hasFile('registration_certificate')) {
                // Delete old file if exists
                if ($company->registration_certificate) {
                    Storage::disk('private')->delete($company->registration_certificate);
                }
                $updateData['registration_certificate'] = $this->handleFileUpload($request, 'registration_certificate');
            }

            if ($request->hasFile('cr12')) {
                // Delete old file if exists
                if ($company->cr12) {
                    Storage::disk('private')->delete($company->cr12);
                }
                $updateData['cr12'] = $this->handleFileUpload($request, 'cr12');
            }

            if ($request->filled('cr12_date')) {
                $updateData['cr12_date'] = $request->cr12_date;
            }

            // Update banking information if provided
            if ($request->filled('bank_id')) {
                $updateData['bank_id'] = $request->bank_id;
            }

            if ($request->filled('bank_account_number')) {
                $updateData['bank_account_number'] = $request->bank_account_number;
            }

            if ($request->hasFile('bank_account_proof')) {
                // Delete old file if exists
                if ($company->bank_account_proof) {
                    Storage::disk('private')->delete($company->bank_account_proof);
                }
                $updateData['bank_account_proof'] = $this->handleFileUpload($request, 'bank_account_proof');
            }

            if ($request->filled('settlement')) {
                $updateData['settlement'] = $request->settlement;
            }

            // Update the company record if there are changes
            if (!empty($updateData)) {
                $company->update($updateData);
            }

            // Handle support documents if provided
            if ($request->hasFile('support_documents')) {
                $this->handleDocumentUploads($request, $company);
            }

            // Update application status back to submitted for re-review
            $application->update([
                'status' => \App\Enums\ApplicationStatus::Resubmitted,
                'submitted_at' => now()
            ]);

            // Send notification about the update
            $company->notify(new CompanyApplicationUpdated($company, $application->application_number));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Application updated successfully and resubmitted for review!',
                'redirect_url' => route('company.applications.show', $application->application_number)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating company application: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating your application. Please try again.'
            ], 500);
        }
    }

    /**
     * Validate company update request
     */
    private function validateCompanyUpdate(Request $request)
    {
        $rules = [
            // Company Information (all optional for updates)
            'pin_number' => 'nullable|string|max:50',
            'registration_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB
            'cr12' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'cr12_date' => 'nullable|date|before_or_equal:today',

            // Banking Information (all optional for updates)
            'bank_id' => 'nullable|exists:banks,id',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_account_proof' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'settlement' => 'nullable|string|max:500',

            // Support documents (optional)
            'support_documents' => 'nullable|array',
            'support_documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB
        ];

        return Validator::make($request->all(), $rules);
    }

    private function handleDocumentUploads(Request $request, Company $company)
    {
        if (!$request->hasFile('support_documents')) {
            return;
        }

        foreach ($request->file('support_documents') as $file) {
            $originalName = $file->getClientOriginalName();
            $storedName = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('support-documents', $storedName, 'private');
            $fileHash = hash_file('sha256', $file->getPathname());

            SupportDocument::create([
                'documentable_type' => Company::class,
                'documentable_id' => $company->id,
                'contribution_reason_id' => $company->contribution_reason_id,
                'original_filename' => $originalName,
                'stored_filename' => $storedName,
                'file_path' => $filePath,
                'file_hash' => $fileHash,
                'status' => 'pending',
            ]);
        }
    }

    /**
     * Handle file uploads (Registration cert, CR12, Bank account proof)
     */
    private function handleFileUpload(Request $request, string $fieldName): ?string
    {
        if (!$request->hasFile($fieldName)) {
            return null;
        }

        $file = $request->file($fieldName);
        $extension = $file->getClientOriginalExtension();
        $fileName = Str::uuid() . '.' . $extension;

        $path = $file->storeAs('company-documents', $fileName, 'private');

        return $path;
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
