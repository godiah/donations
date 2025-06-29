<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
            <!-- Welcome Section -->
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if ($user->hasRole('admin'))
                        <div class="space-y-4">
                            <div class="text-2xl font-bold">Welcome, {{ $user->name }}!</div>
                            <div class="space-y-2">
                                <p><span class="font-semibold">Email:</span> {{ $user->email }}</p>
                            </div>
                        </div>
                    @else
                        <div class="space-y-4">
                            <div class="text-2xl font-bold">Welcome, {{ $user->name }}!</div>
                            <div class="space-y-2">
                                <p><span class="font-semibold">Email:</span> {{ $user->email }}</p>
                                <p><span class="font-semibold">User Type:</span> {{ $user->user_type }}</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            @if ($user->user_type === \App\Enums\UserType::Individual)
                                <a href="{{ route('individual.application') }}"
                                    class="inline-flex items-center px-4 py-2 font-semibold text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                                    Set Up New Donation
                                </a>
                            @else
                                <a href="{{ route('company.application') }}"
                                    class="inline-flex items-center px-4 py-2 font-semibold text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                                    Set Up New Company Donation
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            @if ($user->hasRole('admin'))
                <!-- Admin Panel -->
                <div class="py-4">
                    <h1>Quick Actions</h1>
                    <div>
                        <a href="{{ route('admin.applications.index') }}">View Applications</a>
                        <a href="{{ route('admin.donation-links.index') }}">View Donations</a>
                    </div>
                </div>
            @endif

            <!-- Applications Section -->
            @if (!$user->hasRole('admin'))
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">Recent Applications</h3>
                            <div class="flex items-center space-x-3">
                                <span class="text-sm text-gray-600">{{ $recentApplications->count() }} of
                                    {{ $totalActiveApplications }} active</span>
                                @if ($totalActiveApplications > 4)
                                    <a href="{{ route('active') }}"
                                        class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md text-xs font-medium text-white hover:bg-indigo-700 transition-colors">
                                        View All Applications
                                    </a>
                                @endif
                            </div>
                        </div>

                        @if ($recentApplications->isEmpty())
                            <div class="py-8 text-center">
                                <div class="mb-2 text-lg text-gray-400">📋</div>
                                <p class="text-gray-600">No active applications found.</p>
                                <p class="mt-1 text-sm text-gray-500">Click "Set Up Donation" above to create your first
                                    application.</p>
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach ($recentApplications as $application)
                                    <div
                                        class="p-4 transition-shadow border border-gray-200 rounded-lg hover:shadow-md">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <div class="flex items-center mb-2 space-x-3">
                                                    <span class="text-sm font-medium text-gray-900">
                                                        #{{ $application->application_number }}
                                                    </span>
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $application->status->getColorClass() }}">
                                                        {{ $application->status->getDisplayName() }}
                                                    </span>
                                                </div>

                                                <h4 class="mb-1 text-base font-medium text-gray-900">
                                                    {{ $application->applicant->contribution_name }}
                                                </h4>

                                                <div class="space-y-1 text-sm text-gray-600">
                                                    <p><span class="font-medium">Target Amount:</span> KSh
                                                        {{ number_format($application->applicant->target_amount, 2) }}
                                                    </p>
                                                    <p><span class="font-medium">Target Date:</span>
                                                        {{ $application->applicant->target_date->format('M j, Y') }}
                                                    </p>
                                                    <p><span class="font-medium">Submitted:</span>
                                                        {{ $application->submitted_at->format('M j, Y g:i A') }}</p>
                                                    @if ($application->reviewed_at)
                                                        <p><span class="font-medium">Reviewed:</span>
                                                            {{ $application->reviewed_at->format('M j, Y g:i A') }}</p>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="flex flex-col ml-4 space-y-2">
                                                @if ($application->applicant_type === \App\Models\Individual::class)
                                                    <a href="{{ route('individual.applications.show', $application->application_number) }}"
                                                        class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md text-xs font-medium text-white hover:bg-indigo-700 transition-colors">
                                                        View Details
                                                    </a>
                                                @else
                                                    <a href="{{ route('company.applications.show', $application->application_number) }}"
                                                        class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md text-xs font-medium text-white hover:bg-indigo-700 transition-colors">
                                                        View Details
                                                    </a>
                                                @endif
                                            </div>
                                        </div>

                                        @if ($application->applicant->contribution_description)
                                            <div class="pt-3 mt-3 border-t border-gray-100">
                                                <p class="text-sm text-gray-600 line-clamp-2">
                                                    {{ Str::limit($application->applicant->contribution_description, 150) }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            <!-- Quick Navigation Cards -->
                            <div class="grid grid-cols-1 gap-4 mt-6 md:grid-cols-3">
                                <a href="{{ route('active') }}"
                                    class="block p-4 transition-colors bg-blue-50 rounded-lg hover:bg-blue-100">
                                    <div class="flex items-center space-x-3">
                                        <div class="p-2 bg-blue-200 rounded-full">
                                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                </path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-blue-900">All Applications</p>
                                            <p class="text-xs text-blue-600">View submitted & under review</p>
                                        </div>
                                    </div>
                                </a>

                                <a href="{{ route('donations') }}"
                                    class="block p-4 transition-colors bg-green-50 rounded-lg hover:bg-green-100">
                                    <div class="flex items-center space-x-3">
                                        <div class="p-2 bg-green-200 rounded-full">
                                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-green-900">My Donations</p>
                                            <p class="text-xs text-green-600">Approved applications</p>
                                        </div>
                                    </div>
                                </a>

                                <a href="{{ route('pending') }}"
                                    class="block p-4 transition-colors bg-orange-50 rounded-lg hover:bg-orange-100">
                                    <div class="flex items-center space-x-3">
                                        <div class="p-2 bg-orange-200 rounded-full">
                                            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.664-.833-2.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z">
                                                </path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-orange-900">Needs Attention</p>
                                            <p class="text-xs text-orange-600">Rejected, cancelled, or pending info</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
