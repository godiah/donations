<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DonationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'amount' => [
                'required',
                'numeric',
                'min:1',
                //'max:1000000', // Adjust max amount as needed
            ],
            'currency' => [
                'required',
                'in:KES,USD',
            ],
            'donation_type' => [
                'required',
                'in:anonymous,family,friend,colleague,supporter',
            ],
            'email' => [
                'required',
                'email:rfc,dns',
                'max:255',
            ],
            'phone' => [
                'nullable',
                'required_if:payment_method,card,mpesa', // Required for both card and M-Pesa
                'string',
                'max:20',
                'regex:/^[\+]?[0-9\-\(\)\s]+$/',
            ],
            'payment_method' => [
                'required',
                'in:mpesa,card',
            ],
            // Paybill specific fields (when needed)
            'paybill_account_number' => [
                'nullable',
                'required_if:mpesa_payment_type,paybill',
                'string',
                'max:50',
            ],
            'paybill_account_name' => [
                'nullable',
                'required_if:mpesa_payment_type,paybill',
                'string',
                'max:100',
            ],
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'amount.required' => 'Please enter a donation amount.',
            'amount.numeric' => 'Donation amount must be a valid number.',
            'amount.min' => 'Minimum donation amount is 1.',
            //'amount.max' => 'Maximum donation amount is 1,000,000.',
            'currency.required' => 'Please select a currency.',
            'currency.in' => 'Please select either KES or USD.',
            'donation_type.required' => 'Please select how you want to donate.',
            'donation_type.in' => 'Please select a valid donation type.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'phone.required_if' => 'Phone number is required for this payment method.',
            'phone.regex' => 'Please enter a valid phone number.',
            'payment_method.required' => 'Please select a payment method.',
            'payment_method.in' => 'Please select either M-Pesa or Card payment.',
            'paybill_account_number.required_if' => 'Account number is required for paybill payments.',
            'paybill_account_name.required_if' => 'Account name is required for paybill payments.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'donation_type' => 'donation type',
            'payment_method' => 'payment method',
            'paybill_account_number' => 'paybill account number',
            'paybill_account_name' => 'paybill account name',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Force currency to KES for M-Pesa payments
            if ($this->payment_method === 'mpesa' && $this->currency !== 'KES') {
                $validator->errors()->add('currency', 'M-Pesa payments only support KES currency.');
            }

            // Additional validation for currency-specific minimum amounts
            if ($this->currency === 'USD' && $this->amount < 1) {
                $validator->errors()->add('amount', 'Minimum donation amount for USD is $1.');
            } elseif ($this->currency === 'KES' && $this->amount < 1) {
                $validator->errors()->add('amount', 'Minimum donation amount for KES is 1.');
            }

            // Validate phone format for M-Pesa (Kenyan numbers)
            if ($this->payment_method === 'mpesa' && $this->phone) {
                if (!$this->isValidKenyanPhoneNumber($this->phone)) {
                    $validator->errors()->add('phone', 'Please enter a valid Kenyan phone number (e.g., +254700000000 or 0700000000).');
                }
            }

            // Validate phone format for card payments
            if ($this->payment_method === 'card' && $this->phone) {
                if ($this->currency === 'KES' && !$this->isValidKenyanPhoneNumber($this->phone)) {
                    $validator->errors()->add('phone', 'Please enter a valid Kenyan phone number.');
                }
            }

            // M-Pesa amount limits
            if ($this->payment_method === 'mpesa') {
                if ($this->amount < 1) {
                    $validator->errors()->add('amount', 'Minimum M-Pesa payment is KES 1.');
                }
                // elseif ($this->amount > 150000) {
                //     $validator->errors()->add('amount', 'Maximum M-Pesa payment is KES 150,000.');
                // }
            }
        });
    }

    /**
     * Check if phone number is a valid Kenyan number
     */
    protected function isValidKenyanPhoneNumber(string $phone): bool
    {
        // Remove any non-numeric characters except +
        $cleanPhone = preg_replace('/[^0-9+]/', '', $phone);

        // Check various Kenyan phone number formats
        $patterns = [
            '/^(\+254|254)[17]\d{8}$/',  // +254 or 254 format
            '/^0[17]\d{8}$/',            // 0 format
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $cleanPhone)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the formatted phone number for M-Pesa
     */
    public function getFormattedPhoneNumber(): string
    {
        if (!$this->phone) {
            return '';
        }

        // Remove any non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $this->phone);

        // Convert to 254 format
        if (str_starts_with($phone, '0')) {
            return '254' . substr($phone, 1);
        } elseif (str_starts_with($phone, '254')) {
            return $phone;
        }

        // Assume it's a Kenyan number without country code
        return '254' . $phone;
    }

    /**
     * Prepare data for validation
     */
    protected function prepareForValidation(): void
    {
        // Force currency to KES for M-Pesa payments
        if ($this->payment_method === 'mpesa') {
            $this->merge([
                'currency' => 'KES'
            ]);
        }

        // Format phone number
        if ($this->phone) {
            $this->merge([
                'phone' => $this->getFormattedPhoneNumber()
            ]);
        }
    }
}
