<div class="grid grid-cols-1 gap-6 md:grid-cols-2">
    <div>
        <dt class="text-sm font-medium text-gray-500">Full Name</dt>
        <dd class="mt-1 text-sm text-gray-900">{{ $individual->getFullNameAttribute() }}</dd>
    </div>
    <div>
        <dt class="text-sm font-medium text-gray-500">Email</dt>
        <dd class="mt-1 text-sm text-gray-900">{{ $individual->email }}</dd>
    </div>
    <div>
        <dt class="text-sm font-medium text-gray-500">Phone</dt>
        <dd class="mt-1 text-sm text-gray-900">{{ $individual->phone }}</dd>
    </div>
    <div>
        <dt class="text-sm font-medium text-gray-500">ID Type</dt>
        <dd class="mt-1 text-sm text-gray-900">{{ $individual->idType->display_name ?? 'N/A' }}</dd>
    </div>
    <div>
        <dt class="text-sm font-medium text-gray-500">ID Number</dt>
        <dd class="mt-1 text-sm text-gray-900">{{ $individual->id_number }}</dd>
    </div>
    <div>
        <dt class="text-sm font-medium text-gray-500">KRA PIN</dt>
        <dd class="mt-1 text-sm text-gray-900">{{ $individual->kra_pin ?? 'Not provided' }}</dd>
    </div>
    <div>
        <dt class="text-sm font-medium text-gray-500">Amount Raised</dt>
        <dd class="mt-1 text-sm text-gray-900">
            KES {{ number_format($individual->amount_raised ?? 0, 2) }}
            @if ($individual->fees_charged)
                <div class="text-xs text-gray-500">Fees: KES {{ number_format($individual->fees_charged, 2) }}</div>
            @endif
        </dd>
    </div>
</div>

@if ($individual->additional_info)
    <div class="mt-6">
        <dt class="text-sm font-medium text-gray-500">Additional Information</dt>
        <dd class="p-3 mt-1 text-sm text-gray-900 rounded bg-gray-50">
            @if (is_array($individual->additional_info))
                @foreach ($individual->additional_info as $key => $value)
                    <div class="mb-2">
                        <strong>{{ ucwords(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}
                    </div>
                @endforeach
            @else
                {{ $individual->additional_info }}
            @endif
        </dd>
    </div>
@endif
