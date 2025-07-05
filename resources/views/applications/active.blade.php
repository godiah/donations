<x-app-layout>

    <div class="pt-16 bg-gradient-to-br from-neutral-50 to-neutral-100">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8 sm:py-12 space-y-6">
            <div class="bg-gradient-to-r from-primary-600 to-primary-700 rounded-2xl p-6 text-white shadow-xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>

                        </div>
                        <div>
                            <h2 class="text-2xl font-heading font-bold">All Active Applications</h2>
                            <p class="text-primary-100 mt-1">Track and manage your fundraising applications</p>
                        </div>
                    </div>
                    <a href="{{ route('pending') }}"
                        class="bg-white text-primary-600 hover:bg-primary-50 px-6 py-3 rounded-xl font-semibold transition-all duration-200 transform hover:scale-[1.02] flex items-center gap-2 shadow-md hover:shadow-lg">
                        Applications in Workflow
                        <svg class="w-4 h-4 rotate-180" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20 11H7.83L13.42 5.41L12 4L4 12L12 20L13.41 18.59L7.83 13H20V11Z" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="bg-white rounded-2xl shadow-sm border border-neutral-200 overflow-hidden">
                <div class="bg-gradient-to-r from-neutral-800 to-neutral-700 px-6 py-4">
                    <h3 class="text-lg font-heading font-semibold text-white flex items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M14 12L10 8V11H2V13H10V16L14 12ZM20 3H4C2.9 3 2 3.9 2 5V8H4V5H20V19H4V16H2V19C2 20.1 2.9 21 4 21H20C21.1 21 22 20.1 22 19V5C22 3.9 21.1 3 20 3Z" />
                        </svg>
                        Filter Applications
                    </h3>
                    <p class="text-neutral-300 text-sm mt-1">Filter by application status</p>
                </div>

                <div class="p-6">
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="text-sm font-medium text-neutral-700">Status:</span>

                        <div class="flex flex-wrap gap-2">
                            @php
                                //use App\Enums\ApplicationStatus;
                                $currentStatus = request('status');
                                $displayedStatuses = [
                                    App\Enums\ApplicationStatus::Submitted,
                                    App\Enums\ApplicationStatus::UnderReview,
                                ];
                            @endphp

                            <!-- All Applications -->
                            <a href="{{ route('active') }}"
                                class="flex items-center px-3 py-1.5 rounded-full text-sm font-medium transition-colors {{ !$currentStatus ? 'bg-primary-600 text-white' : 'bg-neutral-100 text-neutral-600 hover:bg-neutral-200' }}">
                                <span class="flex items-center gap-2">
                                    {!! App\Enums\ApplicationStatus::getAllIcon() !!}
                                    <span>All Applications</span>
                                    @if (!$currentStatus)
                                        <span
                                            class="ml-2 bg-white/30 text-xs px-2 py-0.5 rounded-full">{{ $applications->total() }}</span>
                                    @endif
                                </span>
                            </a>

                            <!-- Specific Status Filters -->
                            @foreach ($displayedStatuses as $status)
                                <a href="{{ route('active', ['status' => $status->value]) }}"
                                    class="flex items-center px-3 py-1.5 rounded-full text-sm font-medium transition-colors {{ $currentStatus === $status->value ? $status->getActiveColorClass() : 'bg-neutral-100 text-neutral-600 hover:bg-neutral-200' }}">
                                    <span class="flex items-center gap-2">
                                        {!! $status->getIcon() !!}
                                        <span>{{ $status->getDisplayName() }}</span>
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Applications List -->
            <div class="bg-white rounded-2xl shadow-sm border border-neutral-200 overflow-hidden">
                @if ($applications->count() > 0)
                    <div class="bg-gradient-to-r from-success-600 to-success-700 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-heading font-semibold text-white">
                                    Applications Found
                                </h3>
                                <p class="text-success-100 text-sm mt-1">
                                    Showing {{ $applications->count() }} of {{ $applications->total() }} applications
                                </p>
                            </div>
                            <div class="bg-white/20 backdrop-blur-sm rounded-lg px-4 py-2">
                                <span class="text-white font-semibold">{{ $applications->total() }} Total</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-4">
                        @foreach ($applications as $application)
                            <div
                                class="bg-gradient-to-r from-neutral-50 to-white border-2 border-neutral-200 hover:border-primary-300 rounded-xl p-6 transition-all duration-200 hover:shadow-lg group">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <!-- Application Header -->
                                        <div class="flex items-center gap-4 mb-4">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center group-hover:bg-primary-200 transition-colors">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                        class="w-6 h-6 text-primary-600">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <div class="text-sm text-neutral-500">Application ID</div>
                                                    <span class="text-lg font-heading font-bold text-neutral-800">
                                                        #{{ $application->application_number }}
                                                    </span>
                                                </div>
                                            </div>
                                            <span
                                                class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold border {{ $application->status->getColorClass() }}">
                                                {!! $application->status->getIcon() !!}
                                                {{ $application->status->getDisplayName() }}
                                            </span>
                                        </div>

                                        <!-- Campaign Title -->
                                        <h3
                                            class="text-lg font-heading font-bold text-neutral-800 mb-1 group-hover:text-primary-700 transition-colors">
                                            {{ $application->applicant->contribution_name }}
                                        </h3>

                                        <!-- Application Details Grid -->
                                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 mb-2">
                                            <div class="flex items-center gap-3 p-3 bg-neutral-50 rounded-lg">
                                                <div
                                                    class="w-8 h-8 bg-primary-200 rounded-lg flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-primary-600" fill="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path
                                                            d="M19 3H18V1H16V3H8V1H6V3H5C3.89 3 3 3.9 3 5V19C3 20.1 3.89 21 5 21H19C20.1 21 21 20.1 21 19V5C21 3.9 20.11 3 19 3ZM19 19H5V8H19V19ZM7 10H12V15H7V10Z" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <div class="text-xs text-neutral-500 font-medium">Submitted</div>
                                                    <div class="text-sm font-semibold text-neutral-800">
                                                        {{ $application->submitted_at->format('M j, Y') }}
                                                    </div>
                                                </div>
                                            </div>

                                            @if ($application->reviewed_at)
                                                <div class="flex items-center gap-3 p-3 bg-success-50 rounded-lg">
                                                    <div
                                                        class="w-8 h-8 bg-success-200 rounded-lg flex items-center justify-center">
                                                        <svg class="w-4 h-4 text-success-600" fill="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path
                                                                d="M9 16.17L4.83 12L3.41 13.41L9 19L21 7L19.59 5.59L9 16.17Z" />
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <div class="text-xs text-success-600 font-medium">Reviewed</div>
                                                        <div class="text-sm font-semibold text-success-800">
                                                            {{ $application->reviewed_at->format('M j, Y') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            <div class="flex items-center gap-3 p-3 bg-secondary-50 rounded-lg">
                                                <div
                                                    class="w-8 h-8 bg-secondary-200 rounded-lg flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-secondary-600" fill="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path
                                                            d="M16 4C18.2 4 20 5.8 20 8C20 10.2 18.2 12 16 12C13.8 12 12 10.2 12 8C12 5.8 13.8 4 16 4ZM16 14C18.7 14 24 15.3 24 18V20H8V18C8 15.3 13.3 14 16 14ZM8 12C10.2 12 12 10.2 12 8C12 5.8 10.2 4 8 4C5.8 4 4 5.8 4 8C4 10.2 5.8 12 8 12ZM8 14C5.3 14 0 15.3 0 18V20H6V18C6 16.4 6.7 15.1 7.6 14.1C7.1 14 6.6 14 8 14Z" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <div class="text-xs text-secondary-600 font-medium">Type</div>
                                                    <div class="text-sm font-semibold text-secondary-800">
                                                        {{ $application->applicant_type === \App\Models\Individual::class ? 'Individual' : 'Company' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Description -->
                                        @if ($application->applicant->contribution_description)
                                            <div class="border-t border-neutral-200 pt-4">
                                                <h4
                                                    class="text-sm font-semibold text-neutral-700 mb-2 flex items-center gap-2">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                        <path
                                                            d="M14 2H6C4.9 2 4 2.9 4 4V20C4 21.1 4.89 22 5.99 22H18C19.1 22 20 21.1 20 20V8L14 2ZM18 20H6V4H13V9H18V20Z" />
                                                    </svg>
                                                    Description
                                                </h4>
                                                <p class="text-sm text-neutral-600 leading-relaxed line-clamp-3">
                                                    {{ $application->applicant->contribution_description }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Action Button -->
                                    <div class="ml-6 flex-shrink-0">
                                        @if ($application->applicant_type === \App\Models\Individual::class)
                                            <a href="{{ route('individual.applications.show', $application->application_number) }}"
                                                class="bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white px-4 py-2 rounded-xl font-semibold transition-all duration-200 transform hover:scale-[1.02] flex items-center gap-2 shadow-md hover:shadow-lg">
                                                <span>View Details</span>
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                    <path
                                                        d="M8.59 16.59L13.17 12L8.59 7.41L10 6L16 12L10 18L8.59 16.59Z" />
                                                </svg>
                                            </a>
                                        @else
                                            <a href="{{ route('company.applications.show', $application->application_number) }}"
                                                class="bg-gradient-to-r from-secondary-500 to-secondary-600 hover:from-secondary-600 hover:to-secondary-700 text-white px-4 py-2 rounded-xl font-semibold transition-all duration-200 transform hover:scale-[1.02] flex items-center gap-2 shadow-md hover:shadow-lg">
                                                <span>View Details</span>
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                    <path
                                                        d="M8.59 16.59L13.17 12L8.59 7.41L10 6L16 12L10 18L8.59 16.59Z" />
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if ($applications->hasPages())
                        <div class="border-t border-neutral-200 px-6 py-4">
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-neutral-600">
                                    Showing {{ $applications->firstItem() }} to {{ $applications->lastItem() }} of
                                    {{ $applications->total() }} results
                                </div>
                                <div class="pagination-wrapper">
                                    {{ $applications->appends(request()->query())->links() }}
                                </div>
                            </div>
                        </div>
                    @endif
                @else
                    <!-- Empty State -->
                    <div class="text-center py-16">
                        <div
                            class="w-24 h-24 bg-gradient-to-br from-neutral-100 to-neutral-200 rounded-2xl flex items-center justify-center mx-auto mb-6">
                            <svg class="w-12 h-12 text-neutral-400" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M14 2H6C4.9 2 4 2.9 4 4V20C4 21.1 4.89 22 5.99 22H18C19.1 22 20 21.1 20 20V8L14 2ZM18 20H6V4H13V9H18V20Z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-heading font-bold text-neutral-800 mb-2">No Applications Found</h3>
                        <p class="text-neutral-600 mb-6 max-w-md mx-auto">
                            @if (request('status'))
                                No applications with "{{ ucfirst(str_replace('_', ' ', request('status'))) }}" status
                                found.
                            @else
                                You don't have any submitted or under review applications yet.
                            @endif
                        </p>
                        <div class="flex justify-center gap-3">
                            <a href="{{ route('dashboard') }}"
                                class="bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-200 transform hover:scale-[1.02] flex items-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20 11H7.83L13.42 5.41L12 4L4 12L12 20L13.41 18.59L7.83 13H20V11Z" />
                                </svg>
                                Back to Dashboard
                            </a>
                            <a href="{{ route('active') }}"
                                class="bg-neutral-100 hover:bg-neutral-200 text-neutral-700 px-6 py-3 rounded-xl font-semibold transition-all duration-200 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M17.65 6.35C16.2 4.9 14.21 4 12 4C7.58 4 4 7.58 4 12C4 16.42 7.58 20 12 20C16.42 20 20 16.42 20 12H18C18 15.31 15.31 18 12 18C8.69 18 6 15.31 6 12C6 8.69 8.69 6 12 6C13.66 6 15.14 6.69 16.22 7.78L13 11H20V4L17.65 6.35Z" />
                                </svg>
                                Clear Filters
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

<style>
    /* Filter Badge Styles */
    .filter-badge {
        @apply inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 transform hover:scale-[1.02];
    }

    .filter-badge-active {
        @apply bg-gradient-to-r from-primary-500 to-primary-600 text-white shadow-md;
    }

    .filter-badge-active-secondary {
        @apply bg-gradient-to-r from-secondary-500 to-secondary-600 text-white shadow-md;
    }

    .filter-badge-active-warning {
        @apply bg-gradient-to-r from-primary-500 to-primary-600 text-white shadow-md;
    }

    .filter-badge-inactive {
        @apply bg-neutral-100 text-neutral-600 hover:bg-neutral-200 hover:text-neutral-800 border border-neutral-200 hover:border-neutral-300;
    }

    /* Pagination Styles */
    .pagination-wrapper .pagination {
        @apply flex items-center gap-1;
    }

    .pagination-wrapper .page-link {
        @apply px-3 py-2 text-sm font-medium text-neutral-600 bg-white border border-neutral-200 rounded-lg hover:bg-neutral-50 hover:text-primary-600 transition-colors;
    }

    .pagination-wrapper .page-item.active .page-link {
        @apply bg-gradient-to-r from-primary-500 to-primary-600 text-white border-primary-500;
    }

    .pagination-wrapper .page-item.disabled .page-link {
        @apply text-neutral-400 cursor-not-allowed hover:bg-white hover:text-neutral-400;
    }
</style>
