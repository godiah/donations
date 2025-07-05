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
                'max:1000000', // Adjust max amount as needed
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
                'required_if:payment_method,card',
                'string',
                'max:20',
                'regex:/^[\+]?[0-9\-\(\)\s]+$/',
            ],
            'payment_method' => [
                'required',
                'in:mpesa,card',
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
            'amount.max' => 'Maximum donation amount is 1,000,000.',
            'currency.required' => 'Please select a currency.',
            'currency.in' => 'Please select either KES or USD.',
            'donation_type.required' => 'Please select how you want to donate.',
            'donation_type.in' => 'Please select a valid donation type.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'phone.required_if' => 'Phone number is required for card payments.',
            'phone.regex' => 'Please enter a valid phone number.',
            'payment_method.required' => 'Please select a payment method.',
            'payment_method.in' => 'Please select either M-Pesa or Card payment.',
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
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Additional validation for currency-specific minimum amounts
            if ($this->currency === 'USD' && $this->amount < 1) {
                $validator->errors()->add('amount', 'Minimum donation amount for USD is $1.');
            } elseif ($this->currency === 'KES' && $this->amount < 50) {
                $validator->errors()->add('amount', 'Minimum donation amount for KES is 50.');
            }

            // Validate phone format based on currency/region
            if ($this->payment_method === 'card' && $this->phone) {
                if ($this->currency === 'KES' && !preg_match('/^(\+254|0)[17]\d{8}$/', $this->phone)) {
                    $validator->errors()->add('phone', 'Please enter a valid Kenyan phone number.');
                }
            }
        });
    }
}
