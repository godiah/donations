<?php

namespace App\Http\Controllers;

use App\Enums\UserStatus;
use App\Mail\CheckerInvitation;
use App\Mail\SignupSuccessMail;
use App\Models\Application;
use App\Models\Invitation;
use App\Models\PayoutMandate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class InvitationController extends Controller
{
    /**
     * Show the registration form for an invited checker.
     */
    public function showRegistrationForm(string $token)
    {
        $invitation = Invitation::where('token', $token)
            ->where('expires_at', '>', now())
            ->first();

        if (!$invitation) {
            return redirect()->route('login')->with('error', 'Invalid or expired invitation link.');
        }

        // Determine user_type based on application's applicant_type
        $application = $invitation->application;
        $user_type = $application->applicant_type === \App\Models\Individual::class ? 'individual' : 'company';

        return view('invitation.register', compact('invitation', 'user_type'));
    }

    /**
     * Handle the registration submission for an invited checker.
     */
    public function register(Request $request, string $token)
    {
        $invitation = Invitation::where('token', $token)
            ->where('expires_at', '>', now())
            ->first();

        if (!$invitation) {
            return redirect()->route('login')->with('error', 'Invalid or expired invitation link.');
        }

        $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
        ]);

        try {
            DB::beginTransaction();

            // Determine user_type based on application's applicant_type
            $application = $invitation->application;
            $user_type = $application->applicant_type === \App\Models\Individual::class ? 'individual' : 'company';

            // Create the new user
            $user = User::create([
                'name' => $invitation->name,
                'email' => $invitation->email,
                'password' => Hash::make($request->password),
                'user_type' => $user_type,
                'status' => UserStatus::Active,
            ]);

            // Assign the checker role for the application and default member role
            $user->assignRole('payout_checker', $invitation->application_id);
            $user->assignRole('member');

            // Update the payout mandate with the checker's user ID
            PayoutMandate::where('id', $invitation->payout_mandate_id)->update([
                'checker_id' => $user->id,
            ]);

            // Delete the invitation
            $invitation->delete();

            DB::commit();

            // Log the user in
            Auth::login($user);

            // Send success mail
            Mail::to($user->email)->queue(new SignupSuccessMail($user));

            return redirect()->route('dashboard')->with('success', 'Registration completed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to register invited checker: ' . $e->getMessage(), [
                'invitation_id' => $invitation->id,
                'email' => $invitation->email,
            ]);

            return back()->with('error', 'An error occurred during registration. Please try again.');
        }
    }

    public function createAndSendInvitation(Request $request, Application $application, PayoutMandate $payoutMandate)
    {

        // Retrieve checker name and email from request
        $applicationId = $payoutMandate->application_id;
        $checkerEmail = $payoutMandate->checker_email;
        $checkerName = $payoutMandate->checker_name;

        // Validate checker email and name
        if (!$checkerEmail || !$checkerName) {
            throw new \Exception('Checker email and email name are required.');
        }

        // Check if a valid invitation already exists
        $existingInvitation = $payoutMandate->invitations()->where('accepted', false)
            ->where('expires_at', '>=', now())
            ->first();

        if ($existingInvitation) {
            return $existingInvitation; // Return existing valid invitation
        }

        // Create new invitation
        $token = Str::random(64);
        $expiresAt = now()->addHours(24);

        $invitation = Invitation::create([
            'application_id' => $applicationId,
            'payout_mandate_id' => $payoutMandate->id,
            'email' => $checkerEmail,
            'name' => $checkerName,
            'token' => $token,
            'expires_at' => $expiresAt,
        ]);

        // Delete expired invitations after creating the new one (if any)
        $payoutMandate->invitations()->where('expires_at', '<', now())->delete();

        // Send invitation email
        try {
            Mail::to($checkerEmail)->queue(new CheckerInvitation($invitation));
        } catch (\Exception $e) {
            Log::error('Failed to send checker invitation email: ' . $e->getMessage(), [
                'invitation_id' => $invitation->id,
                'email' => $checkerEmail,
            ]);
        }

        return redirect()->back()->with('success', 'Invitation link sent successfully.');
    }
}
