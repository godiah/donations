<?php

namespace App\Http\Controllers;

use App\Enums\ApplicationStatus;
use App\Http\Controllers\Controller;
use App\Mail\CheckerInvitation;
use App\Models\Application;
use App\Models\ContributionReason;
use App\Models\DocumentType;
use App\Models\IdType;
use App\Models\Individual;
use App\Models\Invitation;
use App\Models\PayoutMandate;
use App\Models\SupportDocument;
use App\Notifications\IndividualApplicationSubmitted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class IndividualDonationController extends Controller
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
            ->get(['type_key', 'display_name', 'description']);

        return response()->json($documentTypes);
    }

    /**
     * Store the individual donation application
     */
    public function store(Request $request)
    {
        // Validate the request
        $validator = $this->validateIndividualDonation($request);

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

            // Create the individual record
            $individual = $this->createIndividual($request);

            // Handle document uploads if required
            $this->handleDocumentUploads($request, $individual);

            // Create the application record
            $application = $this->createApplication($individual);

            // Create payout mandate
            $payoutMandate = $this->createPayoutMandate($request, $application);

            // Handle dual mandate invitation if needed
            if ($payoutMandate->isDual()) {
                $this->createAndSendInvitation($request, $application, $payoutMandate);
            }

            // Assign roles
            $this->assignUserRoles($request, $application, $payoutMandate);

            DB::commit();

            // Send queued notification to the authenticated user
            Auth::user()->notify(new IndividualApplicationSubmitted($application, $individual));

            // Optional: Also notify admins
            // $admins = User::where('role', 'admin')->get();
            // if ($admins->count() > 0) {
            //     Notification::send($admins, new IndividualApplicationSubmitted($application, $individual));
            // }

            return response()->json([
                'success' => true,
                'message' => 'Individual donation application submitted successfully!' .
                    ($payoutMandate->isDual() ? ' An invitation has been sent to the checker.' : ''),
                'application_number' => $application->application_number,
                'redirect_url' => route('individual.success', $application)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Individual application submission error: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => Auth::id(),
                'input' => $request->all(),
            ]);

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
            abort(403);
        }

        return view('applications.individuals.success', compact('application'));
    }


    /**
     * Validate the individual donation request
     */
    private function validateIndividualDonation(Request $request)
    {
        // First, normalize the phone numbers in the request data
        $request->merge([
            'phone' => $this->normalizePhoneNumber($request->phone),
        ]);

        $rules = [
            'contribution_name' => 'required|string|max:255',
            'contribution_description' => 'nullable|string|max:1000',
            'contribution_reason_id' => 'required|exists:contribution_reasons,id',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|regex:/^\+254\d{9}$/',
            'id_number' => 'required|string|max:50',
            'kra_pin' => 'nullable|string|max:20',
            'target_amount' => 'nullable|numeric|min:1|max:999999999.99',
            'target_date' => 'nullable|date|after:today',
            'additional_info' => 'nullable|string',

            // Payout mandate validation
            'mandate_type' => 'required|in:single,dual',
        ];

        // Check if documents are required for the selected contribution reason
        $contributionReason = ContributionReason::find($request->contribution_reason_id);
        if ($contributionReason && $contributionReason->requires_document) {
            $rules['support_documents'] = 'required|array|min:1';
            $rules['support_documents.*'] = 'required|file|mimes:pdf,jpg,jpeg,png|max:5120'; // 5MB max
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
     * Create the individual record
     */
    private function createIndividual(Request $request)
    {
        return Individual::create([
            'user_id' => Auth::id(),
            'contribution_name' => $request->contribution_name,
            'contribution_description' => $request->contribution_description,
            'contribution_reason_id' => $request->contribution_reason_id,
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'id_type_id' => $request->id_type_id,
            'id_number' => $request->id_number,
            'kra_pin' => $request->kra_pin,
            'target_amount' => $request->target_amount,
            'target_date' => $request->target_date,
            'amount_raised' => 0,
            'fees_charged' => 0,
            'additional_info' => $request->additional_info ?? [],
        ]);
    }

    /**
     * Handle document uploads
     */
    private function handleDocumentUploads(Request $request, Individual $individual)
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

    /**
     * Create the application record
     */
    private function createApplication(Individual $individual)
    {
        return Application::create([
            'application_number' => $this->generateApplicationNumber(),
            'user_id' => Auth::id(),
            'applicant_type' => Individual::class,
            'applicant_id' => $individual->id,
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
            $number = 'IND-' . date('Y') . '-' . strtoupper(Str::random(6));
        } while (Application::where('application_number', $number)->exists());

        return $number;
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
}
