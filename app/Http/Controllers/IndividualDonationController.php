<?php

namespace App\Http\Controllers;

use App\Enums\ApplicationStatus;
use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\ContributionReason;
use App\Models\DocumentType;
use App\Models\IdType;
use App\Models\Individual;
use App\Models\SupportDocument;
use App\Notifications\IndividualApplicationSubmitted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

            DB::commit();

            // Send notifications
            Notification::send(Auth::user(), new IndividualApplicationSubmitted($application, $individual));

            return response()->json([
                'success' => true,
                'message' => 'Individual donation application submitted successfully!',
                'application_number' => $application->application_number,
                'redirect_url' => route('individual.success', $application)
            ]);
        } catch (\Exception $e) {
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
        $rules = [
            'contribution_name' => 'required|string|max:255',
            'contribution_description' => 'nullable|string|max:1000',
            'contribution_reason_id' => 'required|exists:contribution_reasons,id',
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => $this->normalizePhoneNumber($request->phone),
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => $this->normalizePhoneNumber($request->emergency_contact_phone),
            'id_number' => 'required|string|max:50',
            'kra_pin' => 'nullable|string|max:20',
            'target_amount' => 'nullable|numeric|min:1|max:999999999.99',
            'target_date' => 'nullable|date|after:today',
            'additional_info' => 'nullable|string',
        ];

        // Check if documents are required for the selected contribution reason
        $contributionReason = ContributionReason::find($request->contribution_reason_id);
        if ($contributionReason && $contributionReason->requires_document) {
            $rules['support_documents'] = 'required|array|min:1';
            $rules['support_documents.*'] = 'required|file|mimes:pdf,jpg,jpeg,png|max:5120'; // 5MB max
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
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'emergency_contact_name' => $request->emergency_contact_name,
            'emergency_contact_phone' => $request->emergency_contact_phone,
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
