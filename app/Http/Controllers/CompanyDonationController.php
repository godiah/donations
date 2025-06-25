<?php

namespace App\Http\Controllers;

use App\Enums\ApplicationStatus;
use App\Mail\CheckerInvitation;
use App\Models\Application;
use App\Models\Company;
use App\Models\ContributionReason;
use App\Models\DocumentType;
use App\Models\Invitation;
use App\Models\PayoutMandate;
use App\Models\SupportDocument;
use App\Notifications\CompanyApplicationSubmitted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CompanyDonationController extends Controller
{
    /**
     * Get document types for a specific contribution reason
     */
    public function getDocumentTypes(ContributionReason $contributionReason)
    {
        if (!$contributionReason->requires_document) {
            return response()->json([]);
        }

        $documentTypes = DocumentType::whereIn('type_key', $contributionReason->required_document_types)
            ->where('is_active', true)
            ->select('type_key', 'display_name', 'description')
            ->get();

        return response()->json($documentTypes);
    }

    /**
     * Store the company donation application
     */
    public function store(Request $request)
    {
        // Validate the request
        $validator = $this->validateCompanyDonation($request);

        if ($validator->fails()) {
            Log::error('Validation failed for individual donation.', [
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

            // Format contact persons and additional info
            $validated = $request->all();
            $validated['contact_persons'] = $this->formatContactPersons($validated);
            $validated['additional_info'] = $this->formatAdditionalInfo($validated);

            // Create the company record
            $company = $this->createCompany($validated, $request);

            // Handle support documents
            $this->handleDocumentUploads($request, $company);

            // Create application record
            $application = $this->createApplication($company);

            // Create payout mandate
            $payoutMandate = $this->createPayoutMandate($request, $application);

            // Handle dual mandate invitation if needed
            if ($payoutMandate->isDual()) {
                $this->createAndSendInvitation($request, $application, $payoutMandate);
            }

            // Assign roles
            $this->assignUserRoles($request, $application, $payoutMandate);

            // Send notification to company email
            $company->notify(new CompanyApplicationSubmitted($company, $application->application_number));


            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Company donation application submitted successfully!' .
                    ($payoutMandate->isDual() ? ' An invitation has been sent to the checker.' : ''),
                'application_number' => $application->application_number,
                'redirect_url' => route('company.success', $application)
            ]);
        } catch (\Exception $e) {
            Log::error('Company application submission error: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => Auth::id(),
                'input' => $request->except(['registration_certificate', 'cr12', 'bank_account_proof', 'support_documents']),
                'trace' => $e->getTraceAsString()
            ]);

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your application. Please try again.'
            ], 500);
        }
    }

    /**
     * Show success page
     */
    public function success(Application $application)
    {
        // Ensure the application belongs to the authenticated user
        if ($application->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to application.');
        }

        return view('applications.companies.success', compact('application'));
    }

    /**
     * Validate the company donation request
     */
    private function validateCompanyDonation(Request $request)
    {
        $rules = [
            // Step 1: Contribution Details
            'contribution_name' => 'required|string|max:255',
            'contribution_description' => 'required|string|max:1000',
            'contribution_reason_id' => 'required|exists:contribution_reasons,id',

            // Step 2: Company Information
            'company_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'registration_certificate' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB
            'pin_number' => 'required|string|max:50',
            'cr12' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'cr12_date' => 'nullable|date|before_or_equal:today',

            // Step 3: Address Information
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'county' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'required|string|max:100',

            // Step 4: Banking & Contact Information
            'bank_id' => 'required|exists:banks,id',
            'bank_account_number' => 'required|string|max:50',
            'bank_account_proof' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'settlement' => 'nullable|string|max:500',
            'contact_persons' => 'required|array|min:1',
            'contact_persons.*.name' => 'required|string|max:255',
            'contact_persons.*.phone' => 'required|string|max:20',
            'contact_persons.*.email' => 'required|email|max:255',
            'contact_persons.*.position' => 'nullable|string|max:100',
            'target_amount' => 'required|numeric|min:1|max:999999999.99',
            'target_date' => 'required|date|after:today',

            // Additional Information
            'additional_info.purpose' => 'nullable|string|max:1000',
            'additional_info.timeline' => 'nullable|string|max:500',
            'additional_info.expected_impact' => 'nullable|string|max:1000',

            // Payout mandate validation
            'mandate_type' => 'required|in:single,dual',
        ];

        // Add support document validation
        $contributionReason = ContributionReason::find($request->contribution_reason_id);
        if ($contributionReason && $contributionReason->requires_document) {
            $rules['support_documents'] = 'required|array|min:1';
            $rules['support_documents.*'] = 'required|file|mimes:pdf,jpg,jpeg,png|max:5120'; // 5MB
        }

        if ($request->input('mandate_type') === 'dual') {
            $rules['checker_name'] = ['required', 'string', 'max:255'];
            $rules['checker_email'] = ['required', 'email', 'max:255'];
        } else {
            // Clear values explicitly if single mandate (to avoid string "null" from HTML form)
            $request->merge([
                'checker_name' => null,
                'checker_email' => null,
            ]);
        }

        return Validator::make($request->all(), $rules);
    }

    /**
     * Create the company record
     */
    private function createCompany(array $validated, Request $request)
    {
        return Company::create([
            'user_id' => Auth::id(),
            'contribution_name' => $validated['contribution_name'],
            'contribution_description' => $validated['contribution_description'],
            'contribution_reason_id' => $validated['contribution_reason_id'],
            'company_name' => $validated['company_name'],
            'email' => $validated['email'],
            'registration_certificate' => $this->handleFileUpload($request, 'registration_certificate'),
            'pin_number' => $validated['pin_number'],
            'cr12' => $this->handleFileUpload($request, 'cr12'),
            'cr12_date' => $validated['cr12_date'],
            'address' => $validated['address'],
            'city' => $validated['city'],
            'county' => $validated['county'] ?? null,
            'postal_code' => $validated['postal_code'] ?? null,
            'country' => $validated['country'],
            'bank_id' => $validated['bank_id'],
            'bank_account_number' => $validated['bank_account_number'],
            'bank_account_proof' => $this->handleFileUpload($request, 'bank_account_proof'),
            'settlement' => $validated['settlement'] ?? null,
            'contact_persons' => $validated['contact_persons'],
            'target_amount' => $validated['target_amount'],
            'target_date' => $validated['target_date'],
            'amount_raised' => 0,
            'fees_charged' => 0,
            'additional_info' => $validated['additional_info'],
        ]);
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

    /**
     * Handle support document uploads
     */
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
     * Create the application record
     */
    private function createApplication(Company $company)
    {
        return Application::create([
            'application_number' => $this->generateApplicationNumber(),
            'user_id' => Auth::id(),
            'applicant_type' => Company::class,
            'applicant_id' => $company->id,
            'status' => ApplicationStatus::Submitted,
            'submitted_at' => now(),
        ]);
    }

    /**
     * Create payout mandate record
     */
    private function createPayoutMandate(Request $request, Application $application)
    {
        return PayoutMandate::create([
            'application_id' => $application->id,
            'type' => $request->mandate_type,
            'maker_id' => Auth::id(),
            'checker_id' => null, // Will be updated when checker registers
            'checker_name' => $request->checker_name,
            'checker_email' => $request->checker_email,
            'is_active' => true,
        ]);
    }

    /**
     * Create and send invitation for dual mandate
     */
    private function createAndSendInvitation(Request $request, Application $application, PayoutMandate $payoutMandate)
    {
        $token = Str::random(64);
        $expiresAt = now()->addHours(24);

        $invitation = Invitation::create([
            'application_id' => $application->id,
            'payout_mandate_id' => $payoutMandate->id,
            'email' => $request->checker_email,
            'name' => $request->checker_name,
            'token' => $token,
            'expires_at' => $expiresAt,
        ]);

        // Send invitation email
        try {
            Mail::to($request->checker_email)->queue(new CheckerInvitation($invitation));
        } catch (\Exception $e) {
            Log::error('Failed to send checker invitation email: ' . $e->getMessage(), [
                'invitation_id' => $invitation->id,
                'email' => $request->checker_email,
            ]);
        }

        return $invitation;
    }

    /**
     * Assign roles to the user based on the payout mandate and application.
     */
    private function assignUserRoles(Request $request, Application $application, PayoutMandate $payoutMandate): bool
    {
        $user = Auth::user();

        try {
            if ($payoutMandate->isSingle()) {
                // Single mandate - user is both maker and checker
                $user->assignRole('single_mandate_user', $application->id);
            } else {
                // Dual mandate - user is maker
                $user->assignRole('payout_maker', $application->id);
                // Checker role will be assigned when they register
            }
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to assign role: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Generate a unique application number
     */
    private function generateApplicationNumber()
    {
        do {
            $number = 'CMP-' . date('Y') . '-' . strtoupper(Str::random(6));
        } while (Application::where('application_number', $number)->exists());

        return $number;
    }

    /**
     * Format contact persons
     */
    private function formatContactPersons(array $validated): array
    {
        if (!isset($validated['contact_persons']) || !is_array($validated['contact_persons'])) {
            return [];
        }

        return array_map(function ($contact) {
            $phone = preg_replace('/[^0-9+\-\(\) ]/', '', $contact['phone'] ?? '');
            if (!str_starts_with($phone, '+')) {
                $phone = '+254' . ltrim($phone, '0'); // Default to Kenya country code
            }

            return [
                'name' => trim($contact['name'] ?? ''),
                'phone' => $phone,
                'email' => filter_var($contact['email'] ?? '', FILTER_SANITIZE_EMAIL),
                'position' => isset($contact['position']) ? trim($contact['position']) : null,
            ];
        }, $validated['contact_persons']);
    }

    /**
     * Format additional information
     */
    private function formatAdditionalInfo(array $validated): array
    {
        $additionalInfo = $validated['additional_info'] ?? [];

        return [
            'purpose' => isset($additionalInfo['purpose']) ? trim(substr($additionalInfo['purpose'], 0, 1000)) : null,
            'timeline' => isset($additionalInfo['timeline']) ? trim(substr($additionalInfo['timeline'], 0, 500)) : null,
            'expected_impact' => isset($additionalInfo['expected_impact']) ? trim(substr($additionalInfo['expected_impact'], 0, 1000)) : null,
        ];
    }
}
