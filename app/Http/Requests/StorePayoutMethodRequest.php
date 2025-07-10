<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePayoutMethodRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(['mobile_money', 'bank_account', 'paybill'])],
            'provider' => ['required_if:type,mobile_money,paybill', 'nullable', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'max:255'],
            'account_name' => ['required_if:type,mobile_money,bank_account', 'nullable', 'string', 'max:255'],
            'bank_id' => ['required_if:type,bank_account', 'nullable', 'exists:banks,id'],
            'paybill_number' => ['required_if:type,paybill', 'nullable', 'string', 'max:255'],
            'paybill_account_name' => ['required_if:type,paybill', 'nullable', 'string', 'max:255'],
            'paybill_description' => ['nullable', 'string', 'max:1000'],
            'is_primary' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'account_number.required' => 'The account number or phone number is required.',
            'account_name.required_if' => 'The account name is required for mobile money and bank account payouts.',
            'provider.required_if' => 'The provider is required for mobile money and paybill payouts.',
            'bank_id.required_if' => 'The bank is required for bank account payouts.',
            'paybill_number.required_if' => 'The paybill number is required for paybill payouts.',
            'paybill_account_name.required_if' => 'The paybill account name is required for paybill payouts.',
            'paybill_description.max' => 'The description must not exceed 1000 characters.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Clean up data based on payout type
        if ($this->type === 'mobile_money') {
            $this->merge([
                'bank_id' => null,
                'paybill_number' => null,
                'paybill_account_name' => null,
                'paybill_description' => null,
            ]);
        } elseif ($this->type === 'bank_account') {
            $this->merge([
                'provider' => null,
                'paybill_number' => null,
                'paybill_account_name' => null,
                'paybill_description' => null,
            ]);
        } elseif ($this->type === 'paybill') {
            $this->merge([
                'bank_id' => null,
                // For paybill, set account_name to paybill_account_name for DB compatibility
                'account_name' => $this->paybill_account_name,
            ]);
        }
    }
}
