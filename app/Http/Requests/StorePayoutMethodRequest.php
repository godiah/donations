<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePayoutMethodRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(['mobile_money', 'bank_account'])],
            'provider' => ['required_if:type,mobile_money', 'nullable', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'max:255'],
            'account_name' => ['required', 'string', 'max:255'],
            'bank_id' => ['required_if:type,bank_account', 'nullable', 'exists:banks,id'],
            'is_primary' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'account_number.required' => 'The account number or phone number is required.',
            'account_name.required' => 'The account name is required.',
            'provider.required_if' => 'The provider is required for mobile money payouts.',
            'bank_id.required_if' => 'The bank is required for bank account payouts.',
        ];
    }
}
