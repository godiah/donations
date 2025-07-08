<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
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
                //'min:100', // Minimum withdrawal amount
                //'max:50000', // Maximum withdrawal amount
            ],
            'withdrawal_method' => [
                'required',
                Rule::in(['mpesa', 'bank_transfer']),
            ],
            // M-Pesa fields
            'mpesa_number' => [
                'required_if:withdrawal_method,mpesa',
                'regex:/^254[0-9]{9}$/', // Kenyan phone number format
            ],
            // Bank transfer fields
            'bank_name' => 'required_if:withdrawal_method,bank_transfer|string|max:100',
            'account_number' => 'required_if:withdrawal_method,bank_transfer|string|max:50',
            'account_name' => 'required_if:withdrawal_method,bank_transfer|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            //'amount.min' => 'Minimum withdrawal amount is KES 100',
            //'amount.max' => 'Maximum withdrawal amount is KES 50,000',
            'mpesa_number.regex' => 'Please enter a valid Kenyan phone number (254XXXXXXXXX)',
            'mpesa_number.required_if' => 'M-Pesa number is required for M-Pesa withdrawals',
            'bank_name.required_if' => 'Bank name is required for bank transfers',
            'account_number.required_if' => 'Account number is required for bank transfers',
            'account_name.required_if' => 'Account name is required for bank transfers',
        ];
    }

    /**
     * Get withdrawal details based on method
     */
    public function getWithdrawalDetails(): array
    {
        if ($this->withdrawal_method === 'mpesa') {
            return [
                'method' => 'mpesa',
                'phone_number' => $this->mpesa_number,
            ];
        }

        return [
            'method' => 'bank_transfer',
            'bank_name' => $this->bank_name,
            'account_number' => $this->account_number,
            'account_name' => $this->account_name,
        ];
    }
}
