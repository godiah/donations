<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Applications Needing Attention
                </h2>
                <p class="mt-1 text-sm text-gray-600">Rejected, cancelled, or requiring additional information</p>
            </div>
            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                ← Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <!-- Status Filter -->
            <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-4 border-b border-gray-200">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div class="flex items-center space-x-4">
                            <h3 class="text-sm font-medium text-gray-900">Filter by status:</h3>
                            <div class="flex space-x-2">
                                <a href="{{ route('pending') }}"
                                    class="inline-flex items-center px-3 py-1.5 rounded-md text-sm font-medium transition-colors
                                          {{ !request('status') ? 'bg-orange-100 text-orange-700' : 'text-gray-500 hover:text-gray-700' }}">
                                    All
                                </a>
                                <a href="{{ route('pending', ['status' => 'additional_info_required']) }}"
                                    class="inline-flex items-center px-3 py-1.5 rounded-md text-sm font-medium transition-colors
                                          {{ request('status') === 'additional_info_required' ? 'bg-orange-100 text-orange-700' : 'text-gray-500 hover:text-gray-700' }}">
                                    Additional Info Required
                                </a>
                                <a href="{{ route('pending', ['status' => 'rejected']) }}"
                                    class="inline-flex items-center px-3 py-1.5 rounded-md text-sm font-medium transition-colors
                                          {{ request('status') === 'rejected' ? 'bg-red-100 text-red-700' : 'text-gray-500 hover:text-gray-700' }}">
                                    Rejected
                                </a>
                                <a href="{{ route('pending', ['status' => 'cancelled']) }}"
                                    class="inline-flex items-center px-3 py-1.5 rounded-md text-sm font-medium transition-colors
                                          {{ request('status') === 'cancelled' ? 'bg-gray-100 text-gray-700' : 'text-gray-500 hover:text-gray-700' }}">
                                    Cancelled
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Applications List -->
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if ($pendingApplications->count() > 0)
                        <div class="space-y-6">
                            @foreach ($pendingApplications as $application)
                                <div
                                    class="p-6 transition-shadow border border-gray-200 rounded-lg 
                                          {{ $application->status === \App\Enums\ApplicationStatus::Rejected ? 'bg-red-50 border-red-200' : '' }}
                                          {{ $application->status === \App\Enums\ApplicationStatus::AdditionalInfoRequired ? 'bg-orange-50 border-orange-200' : '' }}
                                          {{ $application->status === \App\Enums\ApplicationStatus::Cancelled ? 'bg-gray-50 border-gray-200' : '' }}
                                          hover:shadow-md">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center mb-3 space-x-3">
                                                <span class="text-lg font-semibold text-gray-900">
                                                    #{{ $application->application_number }}
                                                </span>
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $application->status->getColorClass() }}">
                                                    @if ($application->status === \App\Enums\ApplicationStatus::Rejected)
                                                        ❌ {{ $application->status->getDisplayName() }}
                                                    @elseif($application->status === \App\Enums\ApplicationStatus::AdditionalInfoRequired)
                                                        ⚠️ {{ $application->status->getDisplayName() }}
                                                    @elseif($application->status === \App\Enums\ApplicationStatus::Cancelled)
                                                        🚫 {{ $application->status->getDisplayName() }}
                                                    @endif
                                                </span>

                                                @if ($application->status === \App\Enums\ApplicationStatus::AdditionalInfoRequired)
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                        Action Required
                                                    </span>
                                                @endif
                                            </div>

                                            <h3 class="mb-2 text-xl font-medium text-gray-900">
                                                {{ $application->applicant->contribution_name }}
                                            </h3>

                                            <div class="grid grid-cols-1 gap-4 mb-4 md:grid-cols-4">
                                                <div>
                                                    <span class="text-sm font-medium text-gray-500">Target Amount</span>
                                                    <p class="text-lg font-semibold text-gray-900">KSh
                                                        {{ number_format($application->applicant->target_amount, 2) }}
                                                    </p>
                                                </div>
                                                <div>
                                                    <span class="text-sm font-medium text-gray-500">Target Date</span>
                                                    <p class="text-sm text-gray-900">
                                                        {{ $application->applicant->target_date->format('M j, Y') }}
                                                    </p>
                                                </div>
                                                <div>
                                                    <span class="text-sm font-medium text-gray-500">Submitted</span>
                                                    <p class="text-sm text-gray-900">
                                                        {{ $application->submitted_at->format('M j, Y') }}</p>
                                                </div>
                                                <div>
                                                    <span class="text-sm font-medium text-gray-500">Status
                                                        Updated</span>
                                                    <p class="text-sm text-gray-900">
                                                        {{ $application->reviewed_at ? $application->reviewed_at->format('M j, Y') : 'N/A' }}
                                                    </p>
                                                </div>
                                            </div>

                                            @if ($application->admin_comments)
                                                <div
                                                    class="p-4 mt-4 
                                                          {{ $application->status === \App\Enums\ApplicationStatus::Rejected ? 'bg-red-100 border border-red-200' : '' }}
                                                          {{ $application->status === \App\Enums\ApplicationStatus::AdditionalInfoRequired ? 'bg-orange-100 border border-orange-200' : '' }}
                                                          {{ $application->status === \App\Enums\ApplicationStatus::Cancelled ? 'bg-gray-100 border border-gray-200' : '' }}
                                                          rounded-lg">
                                                    <div class="flex items-start space-x-2">
                                                        @if ($application->status === \App\Enums\ApplicationStatus::Rejected)
                                                            <svg class="w-5 h-5 text-red-600 mt-0.5 flex-shrink-0"
                                                                fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd"
                                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                                    clip-rule="evenodd"></path>
                                                            </svg>
                                                        @elseif($application->status === \App\Enums\ApplicationStatus::AdditionalInfoRequired)
                                                            <svg class="w-5 h-5 text-orange-600 mt-0.5 flex-shrink-0"
                                                                fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd"
                                                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                                    clip-rule="evenodd"></path>
                                                            </svg>
                                                        @else
                                                            <svg class="w-5 h-5 text-gray-600 mt-0.5 flex-shrink-0"
                                                                fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd"
                                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                                    clip-rule="evenodd"></path>
                                                            </svg>
                                                        @endif
                                                        <div>
                                                            <p
                                                                class="text-sm font-medium 
                                                                    {{ $application->status === \App\Enums\ApplicationStatus::Rejected ? 'text-red-800' : '' }}
                                                                    {{ $application->status === \App\Enums\ApplicationStatus::AdditionalInfoRequired ? 'text-orange-800' : '' }}
                                                                    {{ $application->status === \App\Enums\ApplicationStatus::Cancelled ? 'text-gray-800' : '' }}">
                                                                @if ($application->status === \App\Enums\ApplicationStatus::Rejected)
                                                                    Rejection Reason:
                                                                @elseif($application->status === \App\Enums\ApplicationStatus::AdditionalInfoRequired)
                                                                    Additional Information Required:
                                                                @else
                                                                    Admin Comments:
                                                                @endif
                                                            </p>
                                                            <p
                                                                class="mt-1 text-sm 
                                                                    {{ $application->status === \App\Enums\ApplicationStatus::Rejected ? 'text-red-700' : '' }}
                                                                    {{ $application->status === \App\Enums\ApplicationStatus::AdditionalInfoRequired ? 'text-orange-700' : '' }}
                                                                    {{ $application->status === \App\Enums\ApplicationStatus::Cancelled ? 'text-gray-700' : '' }}">
                                                                {{ $application->admin_comments }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            @if ($application->applicant->contribution_description)
                                                <div class="pt-4 mt-4 border-t border-gray-200">
                                                    <p class="text-sm text-gray-600">
                                                        {{ $application->applicant->contribution_description }}
                                                    </p>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="flex flex-col ml-6 space-y-2">
                                            @if ($application->applicant_type === \App\Models\Individual::class)
                                                <a href="{{ route('individual.applications.show', $application->application_number) }}"
                                                    class="inline-flex items-center px-4 py-2 
                                                           {{ $application->status === \App\Enums\ApplicationStatus::AdditionalInfoRequired ? 'bg-orange-600 hover:bg-orange-700' : 'bg-indigo-600 hover:bg-indigo-700' }}
                                                           border border-transparent rounded-md text-sm font-medium text-white transition-colors">
                                                    @if ($application->status === \App\Enums\ApplicationStatus::AdditionalInfoRequired)
                                                        Take Action
                                                    @else
                                                        View Details
                                                    @endif
                                                </a>
                                            @else
                                                <a href="{{ route('company.applications.show', $application->application_number) }}"
                                                    class="inline-flex items-center px-4 py-2 
                                                           {{ $application->status === \App\Enums\ApplicationStatus::AdditionalInfoRequired ? 'bg-orange-600 hover:bg-orange-700' : 'bg-indigo-600 hover:bg-indigo-700' }}
                                                           border border-transparent rounded-md text-sm font-medium text-white transition-colors">
                                                    @if ($application->status === \App\Enums\ApplicationStatus::AdditionalInfoRequired)
                                                        Take Action
                                                    @else
                                                        View Details
                                                    @endif
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-8">
                            {{ $pendingApplications->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="py-12 text-center">
                            <div class="mb-4 text-4xl text-gray-400">✅</div>
                            <h3 class="mb-2 text-lg font-medium text-gray-900">All caught up!</h3>
                            <p class="text-gray-600">
                                @if (request('status'))
                                    No applications with "{{ ucfirst(str_replace('_', ' ', request('status'))) }}"
                                    status found.
                                @else
                                    You don't have any applications that need attention right now.
                                @endif
                            </p>
                            <div class="mt-6">
                                <a href="{{ route('dashboard') }}"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700 transition-colors">
                                    Back to Dashboard
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Summary Cards -->
            @if ($pendingApplications->count() > 0)
                <div class="grid grid-cols-1 gap-6 mt-8 md:grid-cols-3">
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="p-3 bg-orange-100 rounded-full">
                                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.664-.833-2.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z">
                                            </path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Additional Info Required
                                        </dt>
                                        <dd class="text-lg font-medium text-gray-900">
                                            {{ $pendingApplications->filter(function ($app) {return $app->status === \App\Enums\ApplicationStatus::AdditionalInfoRequired;})->count() }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="p-3 bg-red-100 rounded-full">
                                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Rejected</dt>
                                        <dd class="text-lg font-medium text-gray-900">
                                            {{ $pendingApplications->filter(function ($app) {return $app->status === \App\Enums\ApplicationStatus::Rejected;})->count() }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
