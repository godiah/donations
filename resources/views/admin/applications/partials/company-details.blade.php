<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <dt class="text-sm font-medium text-gray-500">Company Name</dt>
        <dd class="mt-1 text-sm text-gray-900">{{ $company->company_name }}</dd>
    </div>
    <div>
        <dt class="text-sm font-medium text-gray-500">Registration Certificate</dt>
        <dd class="mt-1 text-sm text-gray-900">{{ $company->registration_certificate ? 'Uploaded' : 'Not uploaded' }}
        </dd>
    </div>
    <div>
        <dt class="text-sm font-medium text-gray-500">PIN Number</dt>
        <dd class="mt-1 text-sm text-gray-900">{{ $company->pin_number }}</dd>
    </div>
    <div>
        <dt class="text-sm font-medium text-gray-500">CR12</dt>
        <dd class="mt-1 text-sm text-gray-900">
            {{ $company->cr12 ? 'Uploaded' : 'Not uploaded' }}
            @if ($company->cr12_date)
                <div class="text-xs text-gray-500">Date: {{ $company->cr12_date->format('M d, Y') }}</div>
            @endif
        </dd>
    </div>
    <div class="md:col-span-2">
        <dt class="text-sm font-medium text-gray-500">Address</dt>
        <dd class="mt-1 text-sm text-gray-900">
            {{ $company->address }}
            @if ($company->city || $company->county || $company->postal_code)
                <div class="text-xs text-gray-500 mt-1">
                    {{ collect([$company->city, $company->county, $company->postal_code, $company->country])->filter()->implode(', ') }}
                </div>
            @endif
        </dd>
    </div>
    <div>
        <dt class="text-sm font-medium text-gray-500">Bank</dt>
        <dd class="mt-1 text-sm text-gray-900">{{ $company->bank->display_name ?? 'N/A' }}</dd>
    </div>
    <div>
        <dt class="text-sm font-medium text-gray-500">Bank Account</dt>
        <dd class="mt-1 text-sm text-gray-900">
            {{ $company->bank_account_number }}
            <div class="text-xs text-gray-500">
                Proof: {{ $company->bank_account_proof ? 'Uploaded' : 'Not uploaded' }}
            </div>
        </dd>
    </div>
    <div>
        <dt class="text-sm font-medium text-gray-500">Settlement</dt>
        <dd class="mt-1 text-sm text-gray-900">{{ $company->settlement ?? 'Not specified' }}</dd>
    </div>
    <div>
        <dt class="text-sm font-medium text-gray-500">Amount Raised</dt>
        <dd class="mt-1 text-sm text-gray-900">
            KES {{ number_format($company->amount_raised ?? 0, 2) }}
            @if ($company->fees_charged)
                <div class="text-xs text-gray-500">Fees: KES {{ number_format($company->fees_charged, 2) }}</div>
            @endif
        </dd>
    </div>
</div>

@if ($company->contact_persons && count($company->contact_persons) > 0)
    <div class="mt-6">
        <dt class="text-sm font-medium text-gray-500">Contact Persons</dt>
        <dd class="mt-1">
            <div class="bg-gray-50 p-3 rounded">
                @foreach ($company->contact_persons as $contact)
                    <div class="mb-2 last:mb-0">
                        <div class="text-sm font-medium text-gray-900">{{ $contact['name'] ?? 'N/A' }}</div>
                        @if (isset($contact['phone']))
                            <div class="text-xs text-gray-500">Phone: {{ $contact['phone'] }}</div>
                        @endif
                        @if (isset($contact['email']))
                            <div class="text-xs text-gray-500">Email: {{ $contact['email'] }}</div>
                        @endif
                        @if (isset($contact['position']))
                            <div class="text-xs text-gray-500">Position: {{ $contact['position'] }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        </dd>
    </div>
@endif

@if ($company->additional_info)
    <div class="mt-6">
        <dt class="text-sm font-medium text-gray-500">Additional Information</dt>
        <dd class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded">
            @if (is_array($company->additional_info))
                @foreach ($company->additional_info as $key => $value)
                    <div class="mb-2">
                        <strong>{{ ucwords(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}
                    </div>
                @endforeach
            @else
                {{ $company->additional_info }}
            @endif
        </dd>
    </div>
@endif
