<?php

namespace App\Http\Requests;

use App\Services\WalletService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class WithdrawalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => [
                'required',
                'numeric',
                'min:1', // Minimum withdrawal amount
                'max:1000000', // Maximum withdrawal amount
                'regex:/^\d+(\.\d{1,2})?$/', // Only up to 2 decimal places
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required' => 'Withdrawal amount is required',
            'amount.numeric' => 'Withdrawal amount must be a valid number',
            'amount.min' => 'Minimum withdrawal amount is KES 1',
            'amount.max' => 'Maximum withdrawal amount is KES 1,000,000',
            'amount.regex' => 'Amount can only have up to 2 decimal places',
        ];
    }

    /**
     * Prepare the data for validation
     */
    protected function prepareForValidation()
    {
        // Clean and format the amount
        if ($this->has('amount')) {
            $amount = $this->input('amount');

            // Remove any commas or spaces
            $amount = str_replace([',', ' '], '', $amount);

            // Convert to float and back to string to normalize
            $amount = (float) $amount;

            $this->merge([
                'amount' => $amount
            ]);
        }
    }

    /**
     * Configure the validator instance
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->amount) {
                // Check if user has sufficient balance
                $user = Auth::user();
                $availableBalance = app(WalletService::class)->getAvailableBalance($user);

                if ($this->amount > $availableBalance) {
                    $validator->errors()->add(
                        'amount',
                        'Insufficient available balance. Available: KES ' . number_format($availableBalance, 2)
                    );
                }

                // Check if user has a payout method
                $walletService = app(WalletService::class);
                if (!$walletService->userHasPayoutMethod($user)) {
                    $validator->errors()->add(
                        'payout_method',
                        'Please set up a payout method before making a withdrawal'
                    );
                }
            }
        });
    }

    /**
     * Get custom attributes for validator errors
     */
    public function attributes(): array
    {
        return [
            'amount' => 'withdrawal amount',
        ];
    }
}
