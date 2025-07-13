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
        $paymentMethod = $this->input('payment_method');

        // Base rules that apply to all donations
        $rules = [
            'amount' => ['required', 'numeric', 'min:1', 'max:999999.99'],
            'currency' => ['required', 'string', 'in:KES,USD'],
            'email' => ['required', 'email', 'max:255'],
            'donation_type' => ['required', 'string', 'in:anonymous,family,friend,colleague,supporter,other'],
            'payment_method' => ['required', 'string', 'in:card,mpesa'],
        ];

        // Payment method specific rules
        if ($paymentMethod === 'mpesa') {
            // M-Pesa specific validation
            $rules = array_merge($rules, [
                'phone' => ['required', 'string', 'regex:/^(\+?254|0)?[17]\d{8}$/'],
                'currency' => ['required', 'string', 'in:KES'], // M-Pesa only supports KES
                // Optional billing fields for M-Pesa
                'full_name' => ['nullable', 'string', 'max:100'],
                // Card fields should be ignored for M-Pesa
                'card_full_name' => ['nullable'],
                'card_phone' => ['nullable'],
                'address_line1' => ['nullable'],
                'city' => ['nullable'],
                'state' => ['nullable'],
                'postal_code' => ['nullable'],
                'country' => ['nullable'],
            ]);
        } elseif ($paymentMethod === 'card') {
            // Card payment specific validation (CyberSource requirements)
            $rules = array_merge($rules, [
                'phone' => ['nullable', 'string', 'max:20'], // Optional for card payments
                'card_full_name' => ['required', 'string', 'max:100'],
                'full_name' => ['nullable', 'string', 'max:100'],
                'address_line1' => ['required', 'string', 'max:100'],
                'city' => ['required', 'string', 'max:50'],
                'state' => ['required', 'string', 'max:50'],
                'postal_code' => ['required', 'string', 'max:20'],
                'country' => ['required', 'string', 'size:2'], // ISO 2-letter country code
                'card_phone' => ['nullable', 'string', 'max:20'],
            ]);
        }

        return $rules;
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            // Amount validation messages
            'amount.required' => 'Please enter a donation amount.',
            'amount.numeric' => 'Donation amount must be a valid number.',
            'amount.min' => 'Minimum donation amount is 1.',
            'amount.max' => 'Maximum donation amount is 999,999.99.',

            // Currency validation messages
            'currency.required' => 'Please select a currency.',
            'currency.in' => 'Selected currency is not supported.',

            // Contact information messages
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',

            // Donation type messages
            'donation_type.required' => 'Please select how you\'d like to donate.',
            'donation_type.in' => 'Please select a valid donation type.',

            // Payment method messages
            'payment_method.required' => 'Please select a payment method.',
            'payment_method.in' => 'Selected payment method is not supported.',

            // M-Pesa specific messages
            'phone.required' => 'Phone number is required for M-Pesa payments.',
            'phone.regex' => 'Please enter a valid Kenyan phone number (e.g., 0712345678).',

            // Card payment specific messages
            'card_full_name.required' => 'Full name is required for card payments.',
            'full_name.required' => 'Full name is required for card payments.',
            'address_line1.required' => 'Billing address is required for card payments.',
            'city.required' => 'City is required for card payments.',
            'state.required' => 'State/Province is required for card payments.',
            'postal_code.required' => 'Postal code is required for card payments.',
            'country.required' => 'Country is required for card payments.',
            'country.size' => 'Please select a valid country.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'full_name' => 'full name',
            'card_full_name' => 'full name',
            'address_line1' => 'billing address',
            'postal_code' => 'postal code',
            'donation_type' => 'donation type',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $paymentMethod = $this->input('payment_method');
            $currency = $this->input('currency');
            $amount = $this->input('amount');

            // M-Pesa specific validation
            if ($paymentMethod === 'mpesa') {
                // M-Pesa only supports KES
                if ($currency !== 'KES') {
                    $validator->errors()->add('currency', 'M-Pesa payments only support Kenyan Shillings (KES).');
                }

                // M-Pesa minimum amount validation
                if ($amount && $amount < 1) {
                    $validator->errors()->add('amount', 'Minimum M-Pesa donation amount is KES 10.');
                }
            }

            // Currency-specific amount validation
            if ($currency === 'KES' && $amount && $amount < 1) {
                $validator->errors()->add('amount', 'Minimum donation amount for KES is 10.');
            }

            if ($currency === 'USD' && $amount && $amount < 1) {
                $validator->errors()->add('amount', 'Minimum donation amount for USD is 1.');
            }
        });
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        $paymentMethod = $this->input('payment_method');

        if ($paymentMethod === 'mpesa') {
            if ($this->phone) {
                $phone = preg_replace('/\D/', '', $this->phone);

                if (str_starts_with($phone, '0')) {
                    $phone = '254' . substr($phone, 1);
                } elseif (str_starts_with($phone, '254')) {
                } elseif (strlen($phone) === 9) {
                    $phone = '254' . $phone;
                }

                $this->merge(['phone' => $phone]);
            }

            $this->merge(['currency' => 'KES']);
        } elseif ($paymentMethod === 'card') {
            // Sync card_full_name to full_name for card payments
            if ($this->card_full_name && !$this->full_name) {
                $this->merge(['full_name' => $this->card_full_name]);
            }

            // Sync card_phone to phone for card payments if phone is empty
            if ($this->card_phone && !$this->phone) {
                $this->merge(['phone' => $this->card_phone]);
            }
        }

        // Ensure currency is uppercase
        if ($this->currency) {
            $this->merge(['currency' => strtoupper($this->currency)]);
        }
    }

    /**
     * Get validated data with proper field mapping
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        // For card payments, ensure full_name is set from card_full_name
        if ($this->input('payment_method') === 'card' && isset($validated['card_full_name'])) {
            $validated['full_name'] = $validated['card_full_name'];
        }

        // For card payments, ensure phone is set from card_phone if provided
        if ($this->input('payment_method') === 'card' && isset($validated['card_phone']) && !empty($validated['card_phone'])) {
            $validated['phone'] = $validated['card_phone'];
        }

        return $key ? data_get($validated, $key, $default) : $validated;
    }
}
