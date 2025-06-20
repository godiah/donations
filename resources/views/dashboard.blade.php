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

            <!-- Applications Section -->
            @if (!$user->hasRole('admin'))
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">My Applications</h3>
                            <span class="text-sm text-gray-600">{{ $applications->count() }} application(s)</span>
                        </div>

                        @if ($applications->isEmpty())
                            <div class="py-8 text-center">
                                <div class="mb-2 text-lg text-gray-400">📋</div>
                                <p class="text-gray-600">No applications submitted yet.</p>
                                <p class="mt-1 text-sm text-gray-500">Click "Set Up Donation" above to create your first
                                    application.</p>
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach ($applications as $application)
                                    <div
                                        class="p-4 transition-shadow border border-gray-200 rounded-lg hover:shadow-md">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <div class="flex items-center mb-2 space-x-3">
                                                    <span class="text-sm font-medium text-gray-900">
                                                        #{{ $application->application_number }}
                                                    </span>
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
                                                <a href="{{ route('individual.applications.show', $application->application_number) }}"
                                                    class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md text-xs font-medium text-white hover:bg-indigo-700 transition-colors">
                                                    View Details
                                                </a>
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
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
