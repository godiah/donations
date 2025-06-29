<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Individual;
use App\Models\KycVerification;
use App\Services\KycService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class KycController extends Controller
{
    public function __construct(
        private KycService $kycService
    ) {}

    /**
     * Initiate KYC verification for an individual
     */
    public function initiateVerification(Request $request, Application $application): JsonResponse
    {
        try {
            // Ensure the application has an individual applicant
            if ($application->applicant_type !== Individual::class) {
                return response()->json([
                    'success' => false,
                    'message' => 'KYC verification is only available for individual applicants.'
                ], 400);
            }

            $individual = $application->applicant;

            // Check if there's already a pending or processing verification
            $existingVerification = $individual->kycVerifications()
                ->where('application_id', $application->id)
                ->whereIn('status', ['pending', 'processing'])
                ->first();

            if ($existingVerification) {
                return response()->json([
                    'success' => false,
                    'message' => 'A verification is already in progress for this individual.'
                ], 409);
            }

            // Validate required fields
            if (!$individual->id_number || !$individual->id_type_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID number and ID type are required for verification.'
                ], 400);
            }

            if (!$individual->first_name || !$individual->last_name) {
                return response()->json([
                    'success' => false,
                    'message' => 'First name and last name are required for verification.'
                ], 400);
            }

            // Initiate verification
            $verification = $this->kycService->initiateVerification(
                $individual,
                $application,
                Auth::id()
            );

            return response()->json([
                'success' => true,
                'message' => 'KYC verification initiated successfully.',
                'verification' => [
                    'id' => $verification->id,
                    'job_id' => $verification->job_id,
                    'status' => $verification->status,
                    'created_at' => $verification->created_at->format('Y-m-d H:i:s'),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to initiate KYC verification', [
                'application_id' => $application->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to initiate verification. Please try again.'
            ], 500);
        }
    }

    /**
     * Get KYC verification status for an application
     */
    public function getVerificationStatus(Application $application): JsonResponse
    {
        try {
            // Check if applicant exists and is an Individual
            if (!$application->applicant || !$application->applicant instanceof \App\Models\Individual) {
                Log::error('No individual associated with application', [
                    'application_id' => $application->id,
                    'applicant_type' => $application->applicant_type ?? 'none',
                    'applicant_id' => $application->applicant_id ?? 'none'
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'KYC verification is only available for individual applicants'
                ], 400); // Using 400 Bad Request since this is a client-side issue
            }

            $verification = $this->kycService->getVerificationStatus(
                $application->applicant, // Pass the Individual instance
                $application
            );

            return response()->json([
                'success' => true,
                'verification' => $verification ? [
                    'id' => $verification->id,
                    'status' => $verification->status,
                    'result_text' => $verification->result_text,
                    'application_id' => $verification->application_id,
                    'completed_at' => $verification->completed_at?->toISOString(),
                    'status_text' => $verification->getStatusText(),
                    'status_badge_class' => $verification->getStatusBadgeClass(),
                ] : null
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting KYC status', [
                'application_id' => $application->id,
                'applicant_type' => $application->applicant_type ?? 'none',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get verification status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show verification details
     */
    public function showVerification(KycVerification $verification): JsonResponse
    {
        $verification->load(['individual', 'application', 'initiatedBy']);

        return response()->json([
            'success' => true,
            'verification' => [
                'id' => $verification->id,
                'job_id' => $verification->job_id,
                'smile_job_id' => $verification->smile_job_id,
                'status' => $verification->status,
                'id_type' => $verification->id_type,
                'id_number' => $verification->id_number,
                'result_code' => $verification->result_code,
                'result_text' => $verification->result_text,
                'result_summary' => $verification->getResultSummary(),
                'submitted_at' => $verification->submitted_at?->format('Y-m-d H:i:s'),
                'completed_at' => $verification->completed_at?->format('Y-m-d H:i:s'),
                'failure_reason' => $verification->failure_reason,
                'initiated_by' => $verification->initiatedBy->name,
                'individual' => [
                    'id' => $verification->individual->id,
                    'full_name' => $verification->individual->getFullNameAttribute(),
                    'email' => $verification->individual->email,
                ],
                'application' => [
                    'id' => $verification->application->id,
                    'application_number' => $verification->application->application_number,
                ],
            ]
        ]);
    }

    /**
     * Handle webhook callback from Smile Identity
     */
    public function handleCallback(Request $request): JsonResponse
    {
        try {
            $callbackData = $request->all();

            Log::info('Received KYC callback', $callbackData);

            $success = $this->kycService->processCallback($callbackData);

            if ($success) {
                return response()->json(['status' => 'success']);
            } else {
                Log::warning('Callback processing failed', ['callback_data' => $callbackData]);
                return response()->json(['status' => 'error'], 400);
            }
        } catch (\Exception $e) {
            Log::error('Error handling KYC callback', [
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['status' => 'error'], 500);
        }
    }
}
