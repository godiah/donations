<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use App\Enums\UserType;
use App\Enums\UserStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Notifications\WelcomeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Enum;

class AuthController extends Controller
{
    /**
     * Register a new user via API
     */
    public function register(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
                'user_type' => ['required', new Enum(UserType::class)],
                'password' => [
                    'required',
                    'confirmed',
                    'min:8',
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/'
                ],
            ], [
                'password.regex' => 'The password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
                'user_type.required' => 'Please select a registration type.',
            ]);

            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'user_type' => $validatedData['user_type'],
                'status' => UserStatus::Active,
            ]);

            // Assign role
            try {
                $user->assignRole('member');
            } catch (\Exception $e) {
                Log::error('Role assignment failed', ['error' => $e->getMessage()]);
                throw new \Exception('Role assignment failed');
            }

            // Send welcome notification to user
            $user->notify(new WelcomeNotification($user));

            Auth::guard('web')->login($user);

            return response()->json([
                'status' => 'success',
                'message' => 'New User Onboarded Successfully',
                'user' => $user,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Registration failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Registration failed',
            ], 500);
        }
    }

    /**
     * Handle an incoming API authentication request.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $request->authenticate();

            $request->session()->regenerate();

            return response()->json([
                'status' => 'success',
                'message' => 'Login successful',
                'user' => Auth::user(),
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Login failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Login failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Login failed',
            ], 500);
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully',
        ], 200);
    }


    /**
     * Get authenticated user via API
     */
    public function user(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'user_type' => $user->user_type->value,
                        'status' => $user->status->value,
                        'email_verified_at' => $user->email_verified_at,
                        'created_at' => $user->created_at,
                        'updated_at' => $user->updated_at,
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve user data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
