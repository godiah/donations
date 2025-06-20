<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Application Details - #{{ $application->application_number }}
            </h2>
            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                ← Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
            <!-- Application Status -->
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Application Status</h3>
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $application->status->value === 'draft' ? 'bg-gray-100 text-gray-800' : '' }}
                            {{ $application->status->value === 'submitted' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $application->status->value === 'under_review' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $application->status->value === 'additional_info_required' ? 'bg-orange-100 text-orange-800' : '' }}
                            {{ $application->status->value === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $application->status->value === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                            {{ $application->status->value === 'cancelled' ? 'bg-purple-100 text-purple-800' : '' }}">
                            {{ ucfirst(str_replace('_', ' ', $application->status->value)) }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 gap-4 text-sm md:grid-cols-2">
                        <div>
                            <span class="font-medium text-gray-700">Application Number:</span>
                            <span class="text-gray-900">{{ $application->application_number }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Submitted:</span>
                            <span class="text-gray-900">{{ $application->submitted_at->format('M j, Y g:i A') }}</span>
                        </div>
                        @if ($application->reviewed_at)
                            <div>
                                <span class="font-medium text-gray-700">Reviewed:</span>
                                <span
                                    class="text-gray-900">{{ $application->reviewed_at->format('M j, Y g:i A') }}</span>
                            </div>
                            @if ($application->reviewer)
                                <div>
                                    <span class="font-medium text-gray-700">Reviewed By:</span>
                                    <span class="text-gray-900">{{ $application->reviewer->name }}</span>
                                </div>
                            @endif
                        @endif
                    </div>

                    @if ($application->admin_comments)
                        <div class="p-4 mt-4 rounded-lg bg-gray-50">
                            <h4 class="mb-2 font-medium text-gray-800">Admin Comments:</h4>
                            <p class="text-sm text-gray-700">{{ $application->admin_comments }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Contribution Details -->
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="mb-4 text-lg font-semibold text-gray-800">Contribution Details</h3>

                    <div class="space-y-4">
                        <div>
                            <h4 class="mb-2 font-medium text-gray-700">{{ $application->applicant->contribution_name }}
                            </h4>
                            @if ($application->applicant->contribution_description)
                                <p class="text-sm text-gray-600">
                                    {{ $application->applicant->contribution_description }}</p>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 gap-4 text-sm md:grid-cols-2 lg:grid-cols-3">
                            <div>
                                <span class="font-medium text-gray-700">Target Amount:</span>
                                <div class="text-lg font-semibold text-green-600">KSh
                                    {{ number_format($application->applicant->target_amount, 2) }}</div>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Amount Raised:</span>
                                <div class="text-lg font-semibold text-blue-600">KSh
                                    {{ number_format($application->applicant->amount_raised ?? 0, 2) }}</div>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Target Date:</span>
                                <div class="text-gray-900">{{ $application->applicant->target_date->format('M j, Y') }}
                                </div>
                            </div>
                        </div>

                        <div>
                            <span class="font-medium text-gray-700">Contribution Reason:</span>
                            <span
                                class="text-gray-900">{{ $application->applicant->contributionReason->name ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Personal Information -->
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="mb-4 text-lg font-semibold text-gray-800">Personal Information</h3>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div class="space-y-3">
                            <div>
                                <span class="font-medium text-gray-700">Full Name:</span>
                                <div class="text-gray-900">{{ $application->applicant->full_name }}</div>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Email:</span>
                                <div class="text-gray-900">{{ $application->applicant->email }}</div>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Phone:</span>
                                <div class="text-gray-900">{{ $application->applicant->phone }}</div>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div>
                                <span class="font-medium text-gray-700">ID Type:</span>
                                <div class="text-gray-900">{{ $application->applicant->idType->name ?? 'N/A' }}</div>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">ID Number:</span>
                                <div class="text-gray-900">{{ $application->applicant->id_number }}</div>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">KRA PIN:</span>
                                <div class="text-gray-900">{{ $application->applicant->kra_pin ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>

                    @if ($application->applicant->emergency_contact_name)
                        <div class="pt-4 mt-6 border-t border-gray-200">
                            <h4 class="mb-3 font-medium text-gray-700">Emergency Contact</h4>
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <span class="font-medium text-gray-700">Name:</span>
                                    <div class="text-gray-900">{{ $application->applicant->emergency_contact_name }}
                                    </div>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700">Phone:</span>
                                    <div class="text-gray-900">{{ $application->applicant->emergency_contact_phone }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Support Documents -->
            @if ($supportDocuments->isNotEmpty())
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="mb-4 text-lg font-semibold text-gray-800">Support Documents</h3>

                        <div class="space-y-3">
                            @foreach ($supportDocuments as $document)
                                <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0">
                                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $document->original_filename }}</div>
                                            @if ($document->verification_notes)
                                                <div class="text-xs text-gray-500">{{ $document->verification_notes }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                            {{ $document->status === 'verified' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $document->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $document->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                            {{ ucfirst($document->status) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Additional Information -->
            @if ($application->applicant->additional_info)
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="mb-4 text-lg font-semibold text-gray-800">Additional Information</h3>
                        <div class="text-sm prose text-gray-700">
                            @if (is_array($application->applicant->additional_info))
                                @foreach ($application->applicant->additional_info as $key => $value)
                                    <div class="mb-2">
                                        <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                        <span>{{ $value }}</span>
                                    </div>
                                @endforeach
                            @else
                                <p>{{ $application->applicant->additional_info }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
