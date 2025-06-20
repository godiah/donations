<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Company Donation Application: {{ $application->application_number }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-8 bg-white">
                    <!-- Application Status -->
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Application Status</h3>
                            <span
                                class="inline-flex items-center px-3 py-1 mt-2 text-sm font-medium rounded-full 
                                {{ ($application->status->value === 'Submitted'
                                        ? 'text-yellow-800 bg-yellow-100'
                                        : $application->status->value === 'Approved')
                                    ? 'text-green-800 bg-green-100'
                                    : 'text-red-800 bg-red-100' }}">
                                {{ $application->status->value }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600">Submitted on
                            {{ $application->submitted_at->format('F j, Y \a\t g:i A') }}</p>
                    </div>

                    <!-- Contribution Details -->
                    <div class="mb-8">
                        <h4 class="mb-4 text-lg font-semibold text-gray-900">Contribution Details</h4>
                        <div class="p-6 bg-gray-50 rounded-lg">
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Contribution Name</label>
                                    <p class="mt-1 text-gray-900">
                                        {{ $application->applicant->contribution_name ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Description</label>
                                    <p class="mt-1 text-gray-900">
                                        {{ $application->applicant->contribution_description ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Reason</label>
                                    <p class="mt-1 text-gray-900">
                                        {{ $application->applicant->contributionReason->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Company Information -->
                    <div class="mb-8">
                        <h4 class="mb-4 text-lg font-semibold text-gray-900">Company Information</h4>
                        <div class="p-6 bg-gray-50 rounded-lg">
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Company Name</label>
                                    <p class="mt-1 text-gray-900">{{ $application->applicant->company_name ?? 'N/A' }}
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">PIN Number</label>
                                    <p class="mt-1 text-gray-900">{{ $application->applicant->pin_number ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Registration
                                        Certificate</label>
                                    @if ($application->applicant->registration_certificate)
                                        <a href="{{ route('company.download', ['application' => $application, 'file' => 'registration_certificate']) }}"
                                            class="mt-1 text-blue-600 hover:underline">Download</a>
                                    @else
                                        <p class="mt-1 text-gray-900">Not provided</p>
                                    @endif
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">CR12 Document</label>
                                    @if ($application->applicant->cr12)
                                        <a href="{{ route('company.download', ['application' => $application, 'file' => 'cr12']) }}"
                                            class="mt-1 text-blue-600 hover:underline">Download</a>
                                    @else
                                        <p class="mt-1 text-gray-900">Not provided</p>
                                    @endif
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">CR12 Date</label>
                                    <p class="mt-1 text-gray-900">
                                        {{ $application->applicant->cr12_date ? \Carbon\Carbon::parse($application->applicant->cr12_date)->format('F j, Y') : 'N/A' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Address Information -->
                    <div class="mb-8">
                        <h4 class="mb-4 text-lg font-semibold text-gray-900">Address Information</h4>
                        <div class="p-6 bg-gray-50 rounded-lg">
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-500">Address</label>
                                    <p class="mt-1 text-gray-900">{{ $application->applicant->address ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">City</label>
                                    <p class="mt-1 text-gray-900">{{ $application->applicant->city ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">County</label>
                                    <p class="mt-1 text-gray-900">{{ $application->applicant->county ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Postal Code</label>
                                    <p class="mt-1 text-gray-900">{{ $application->applicant->postal_code ?? 'N/A' }}
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Country</label>
                                    <p class="mt-1 text-gray-900">{{ $application->applicant->country ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Banking Information -->
                    <div class="mb-8">
                        <h4 class="mb-4 text-lg font-semibold text-gray-900">Banking Information</h4>
                        <div class="p-6 bg-gray-50 rounded-lg">
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Bank</label>
                                    <p class="mt-1 text-gray-900">
                                        {{ $application->applicant->bank->display_name ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Bank Account Number</label>
                                    <p class="mt-1 text-gray-900">
                                        {{ $application->applicant->bank_account_number ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Bank Account Proof</label>
                                    @if ($application->applicant->bank_account_proof)
                                        <a href="{{ route('company.download', ['application' => $application, 'file' => 'bank_account_proof']) }}"
                                            class="mt-1 text-blue-600 hover:underline">Download</a>
                                    @else
                                        <p class="mt-1 text-gray-900">Not provided</p>
                                    @endif
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-500">Settlement
                                        Information</label>
                                    <p class="mt-1 text-gray-900">{{ $application->applicant->settlement ?? 'N/A' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Persons -->
                    <div class="mb-8">
                        <h4 class="mb-4 text-lg font-semibold text-gray-900">Contact Persons</h4>
                        <div class="p-6 bg-gray-50 rounded-lg">
                            @forelse ($application->applicant->contact_persons as $index => $person)
                                <div
                                    class="mb-4 {{ $index < count($application->applicant->contact_persons) - 1 ? 'border-b pb-4' : '' }}">
                                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-500">Name</label>
                                            <p class="mt-1 text-gray-900">{{ $person['name'] ?? 'N/A' }}</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-500">Position</label>
                                            <p class="mt-1 text-gray-900">{{ $person['position'] ?? 'N/A' }}</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-500">Phone</label>
                                            <p class="mt-1 text-gray-900">{{ $person['phone'] ?? 'N/A' }}</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-500">Email</label>
                                            <p class="mt-1 text-gray-900">{{ $person['email'] ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-900">No contact persons provided.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Financial Information -->
                    <div class="mb-8">
                        <h4 class="mb-4 text-lg font-semibold text-gray-900">Financial Information</h4>
                        <div class="p-6 bg-gray-50 rounded-lg">
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Target Amount</label>
                                    <p class="mt-1 text-gray-900">KES
                                        {{ number_format($application->applicant->target_amount ?? 0, 2) }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Target Date</label>
                                    <p class="mt-1 text-gray-900">
                                        {{ $application->applicant->target_date ? \Carbon\Carbon::parse($application->applicant->target_date)->format('F j, Y') : 'N/A' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="mb-8">
                        <h4 class="mb-4 text-lg font-semibold text-gray-900">Additional Information</h4>
                        <div class="p-6 bg-gray-50 rounded-lg">
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Purpose & Use of
                                        Funds</label>
                                    <p class="mt-1 text-gray-900">
                                        {{ $application->applicant->additional_info['purpose'] ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Project Timeline</label>
                                    <p class="mt-1 text-gray-900">
                                        {{ $application->applicant->additional_info['timeline'] ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Expected Impact</label>
                                    <p class="mt-1 text-gray-900">
                                        {{ $application->applicant->additional_info['expected_impact'] ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Support Documents -->
                    <div class="mb-8">
                        <h4 class="mb-4 text-lg font-semibold text-gray-900">Support Documents</h4>
                        <div class="p-6 bg-gray-50 rounded-lg">
                            @forelse ($supportDocuments as $document)
                                <div class="flex items-center justify-between mb-4 pb-4 border-b">
                                    <div>
                                        <p class="text-gray-900">{{ $document->original_filename }}</p>
                                        {{-- <p class="text-sm text-gray-500">
                                            {{ $document->documentType->display_name ?? 'N/A' }}</p> --}}
                                    </div>
                                    <a href="{{ route('company.download.support', ['application' => $application, 'document' => $document]) }}"
                                        class="text-blue-600 hover:underline">Download</a>
                                </div>
                            @empty
                                <p class="text-gray-900">No support documents provided.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end">
                        <a href="{{ route('dashboard') }}"
                            class="inline-flex items-center px-6 py-3 text-base font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 5a2 2 0 012-2h2a2 2 0 012 2v2H8V5z"></path>
                            </svg>
                            Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
