<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                All Active Applications
            </h2>
            <a href="{{ route('pending') }}"
                class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                ← View Pending
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-4 border-b border-gray-200">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div class="flex items-center space-x-4">
                            <h3 class="text-sm font-medium text-gray-900">Filter by status:</h3>
                            <div class="flex space-x-2">
                                <a href="{{ route('active') }}"
                                    class="inline-flex items-center px-3 py-1.5 rounded-md text-sm font-medium transition-colors
                                          {{ !request('status') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-500 hover:text-gray-700' }}">
                                    All
                                </a>
                                <a href="{{ route('active', ['status' => 'submitted']) }}"
                                    class="inline-flex items-center px-3 py-1.5 rounded-md text-sm font-medium transition-colors
                                          {{ request('status') === 'submitted' ? 'bg-blue-100 text-blue-700' : 'text-gray-500 hover:text-gray-700' }}">
                                    Submitted
                                </a>
                                <a href="{{ route('active', ['status' => 'under_review']) }}"
                                    class="inline-flex items-center px-3 py-1.5 rounded-md text-sm font-medium transition-colors
                                          {{ request('status') === 'under_review' ? 'bg-yellow-100 text-yellow-700' : 'text-gray-500 hover:text-gray-700' }}">
                                    Under Review
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Applications List -->
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if ($applications->count() > 0)
                        <div class="space-y-6">
                            @foreach ($applications as $application)
                                <div class="p-6 transition-shadow border border-gray-200 rounded-lg hover:shadow-md">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center mb-3 space-x-3">
                                                <span class="text-lg font-semibold text-gray-900">
                                                    #{{ $application->application_number }}
                                                </span>
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $application->status->getColorClass() }}">
                                                    {{ $application->status->getDisplayName() }}
                                                </span>
                                            </div>

                                            <h3 class="mb-2 text-xl font-medium text-gray-900">
                                                {{ $application->applicant->contribution_name }}
                                            </h3>

                                            <div class="grid grid-cols-1 gap-3 mb-4 md:grid-cols-3">
                                                <div>
                                                    <span class="text-sm font-medium text-gray-500">Target
                                                        Amount</span>
                                                    <p class="text-lg font-semibold text-gray-900">KSh
                                                        {{ number_format($application->applicant->target_amount, 2) }}
                                                    </p>
                                                </div>
                                                <div>
                                                    <span class="text-sm font-medium text-gray-500">Target
                                                        Date</span>
                                                    <p class="text-sm text-gray-900">
                                                        {{ $application->applicant->target_date->format('M j, Y') }}
                                                    </p>
                                                </div>
                                                <div>
                                                    <span class="text-sm font-medium text-gray-500">Submitted</span>
                                                    <p class="text-sm text-gray-900">
                                                        {{ $application->submitted_at->format('M j, Y g:i A') }}
                                                    </p>
                                                </div>
                                            </div>

                                            @if ($application->applicant->contribution_description)
                                                <div class="pt-4 mt-4 border-t border-gray-100">
                                                    <p class="text-sm text-gray-600">
                                                        {{ $application->applicant->contribution_description }}
                                                    </p>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="flex flex-col ml-6 space-y-2">
                                            @if ($application->applicant_type === \App\Models\Individual::class)
                                                <a href="{{ route('individual.applications.show', $application->application_number) }}"
                                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700 transition-colors">
                                                    View Details
                                                </a>
                                            @else
                                                <a href="{{ route('company.applications.show', $application->application_number) }}"
                                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700 transition-colors">
                                                    View Details
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-8">
                            {{ $applications->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="py-12 text-center">
                            <div class="mb-4 text-4xl text-gray-400">📋</div>
                            <h3 class="mb-2 text-lg font-medium text-gray-900">No applications found</h3>
                            <p class="text-gray-600">
                                @if (request('status'))
                                    No applications with "{{ ucfirst(str_replace('_', ' ', request('status'))) }}"
                                    status found.
                                @else
                                    You don't have any submitted or under review applications yet.
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
        </div>
    </div>
</x-app-layout>
