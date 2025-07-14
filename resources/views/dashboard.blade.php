<x-app-layout>

    <div class="pt-16 bg-gradient-to-br from-neutral-50 to-neutral-100">
        <div class="relative py-8 sm:py-12">
            <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Header Section -->
                <div class="mb-8">
                    <div
                        class="p-6 text-white shadow-xl bg-gradient-to-r from-primary-600 to-primary-700 rounded-2xl sm:p-8">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div
                                    class="flex items-center justify-center w-16 h-16 bg-white/20 rounded-2xl backdrop-blur-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                    </svg>

                                </div>
                                <div>
                                    <h1 class="text-2xl font-bold sm:text-3xl font-heading">
                                        Welcome back, {{ $user->name }}!
                                    </h1>
                                    <p class="mt-1 text-primary-100">
                                        @if ($user->hasRole('admin'))
                                            Admin Dashboard - Manage donations and applications
                                        @else
                                            Ready to make a difference? Let's get started.
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="hidden sm:block">
                                <div class="text-right">
                                    <div class="text-sm text-primary-200">{{ now()->format('l') }}</div>
                                    <div class="text-lg font-semibold">{{ now()->format('M d, Y') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid gap-6 lg:grid-cols-3">
                    <!-- Main Content -->
                    <div class="space-y-6 lg:col-span-2">
                        <!-- User Information Card -->
                        <div class="overflow-hidden bg-white border shadow-sm rounded-2xl border-neutral-200">
                            <div class="px-6 py-4 bg-gradient-to-r from-neutral-800 to-neutral-700">
                                <h2 class="flex items-center gap-2 text-lg font-semibold text-white font-heading">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M12 12C14.21 12 16 10.21 16 8C16 5.79 14.21 4 12 4C9.79 4 8 5.79 8 8C8 10.21 9.79 12 12 12ZM12 14C9.33 14 4 15.34 4 18V20H20V18C20 15.34 14.67 14 12 14Z" />
                                    </svg>
                                    Account Information
                                </h2>
                            </div>
                            <div class="p-6">
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div class="space-y-3">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="flex items-center justify-center w-10 h-10 rounded-lg bg-primary-100">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                    class="w-5 h-5 text-primary-600">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                </svg>

                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-neutral-500">Full Name</p>
                                                <p class="font-semibold text-neutral-800">{{ $user->name }}</p>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-3">
                                            <div
                                                class="flex items-center justify-center w-10 h-10 rounded-lg bg-secondary-100">
                                                <svg class="w-5 h-5 text-secondary-600" fill="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path
                                                        d="M20 4H4C2.89 4 2 4.89 2 6V18C2 19.11 2.89 20 4 20H20C21.11 20 22 19.11 22 18V6C22 4.89 21.11 4 20 4ZM20 8L12 13L4 8V6L12 11L20 6V8Z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-neutral-500">Email Address</p>
                                                <p class="font-semibold text-neutral-800">{{ $user->email }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    @if (!$user->hasRole('admin'))
                                        <div class="space-y-3">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="flex items-center justify-center w-10 h-10 rounded-lg bg-success-100">
                                                    <svg class="w-5 h-5 text-success-600" fill="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path
                                                            d="M16 4C18.2 4 20 5.8 20 8C20 10.2 18.2 12 16 12C13.8 12 12 10.2 12 8C12 5.8 13.8 4 16 4ZM16 14C18.7 14 24 15.3 24 18V20H8V18C8 15.3 13.3 14 16 14ZM8 12C10.2 12 12 10.2 12 8C12 5.8 10.2 4 8 4C5.8 4 4 5.8 4 8C4 10.2 5.8 12 8 12ZM8 14C5.3 14 0 15.3 0 18V20H6V18C6 16.4 6.7 15.1 7.6 14.1C7.1 14 6.6 14 8 14Z" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-neutral-500">Account Type</p>
                                                    <p class="font-semibold capitalize text-neutral-800">
                                                        {{ $user->user_type }}</p>
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="flex items-center justify-center w-10 h-10 rounded-lg bg-primary-100">
                                                    <svg class="w-5 h-5 text-primary-600" fill="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path
                                                            d="M9 16.17L4.83 12L3.41 13.41L9 19L21 7L19.59 5.59L9 16.17Z" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-neutral-500">Status</p>
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-success-100 text-success-800">
                                                        Active
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if (!$user->hasRole('admin'))
                            <!-- Quick Actions for Regular Users -->
                            <div class="overflow-hidden bg-white border shadow-sm rounded-2xl border-neutral-200">
                                <div class="px-6 py-4 bg-gradient-to-r from-success-600 to-success-700">
                                    <h2 class="flex items-center gap-2 text-lg font-semibold text-white font-heading">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M19 13H13V19H11V13H5V11H11V5H13V11H19V13Z" />
                                        </svg>
                                        Create New Donation
                                    </h2>
                                    <p class="mt-1 text-sm text-success-100">Start raising funds for your cause</p>
                                </div>
                                <div class="p-6">
                                    <div class="grid gap-4 sm:grid-cols-2">
                                        @if ($user->user_type === \App\Enums\UserType::Individual)
                                            <a href="{{ route('individual.application') }}"
                                                class="group relative bg-gradient-to-br from-primary-50 to-primary-100 hover:from-primary-100 hover:to-primary-200 border-2 border-primary-200 hover:border-primary-300 rounded-xl p-6 transition-all duration-200 transform hover:scale-[1.02]">
                                                <div class="flex items-center gap-4">
                                                    <div
                                                        class="flex items-center justify-center w-12 h-12 transition-shadow shadow-lg bg-primary-500 rounded-xl group-hover:shadow-xl">
                                                        <svg class="w-6 h-6 text-white" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="1.5"
                                                            stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M12 4.5v15m7.5-7.5h-15" />
                                                        </svg>
                                                    </div>
                                                    <div class="flex-1">
                                                        <h3
                                                            class="font-semibold font-heading text-primary-800 group-hover:text-primary-900">
                                                            Personal Donation</h3>
                                                        <p class="mt-1 text-sm text-primary-600">Create an individual
                                                            fundraising campaign</p>
                                                    </div>
                                                    <svg class="w-5 h-5 transition-colors text-primary-400 group-hover:text-primary-600"
                                                        fill="currentColor" viewBox="0 0 24 24">
                                                        <path
                                                            d="M8.59 16.59L13.17 12L8.59 7.41L10 6L16 12L10 18L8.59 16.59Z" />
                                                    </svg>
                                                </div>
                                            </a>
                                        @else
                                            <a href="{{ route('company.application') }}"
                                                class="group relative bg-gradient-to-br from-secondary-50 to-secondary-100 hover:from-secondary-100 hover:to-secondary-200 border-2 border-secondary-200 hover:border-secondary-300 rounded-xl p-6 transition-all duration-200 transform hover:scale-[1.02]">
                                                <div class="flex items-center gap-4">
                                                    <div
                                                        class="flex items-center justify-center w-12 h-12 transition-shadow shadow-lg bg-secondary-500 rounded-xl group-hover:shadow-xl">
                                                        <svg class="w-6 h-6 text-white" fill="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path
                                                                d="M12 7V3H2V21H22V7H12ZM6 19H4V17H6V19ZM6 15H4V13H6V15ZM6 11H4V9H6V11ZM6 7H4V5H6V7ZM10 19H8V17H10V19ZM10 15H8V13H10V15ZM10 11H8V9H10V11ZM10 7H8V5H10V7ZM20 19H12V17H20V19ZM20 15H12V13H20V15ZM20 11H12V9H20V11Z" />
                                                        </svg>
                                                    </div>
                                                    <div class="flex-1">
                                                        <h3
                                                            class="font-semibold font-heading text-secondary-800 group-hover:text-secondary-900">
                                                            Company Donation</h3>
                                                        <p class="mt-1 text-sm text-secondary-600">Set up corporate
                                                            fundraising initiative</p>
                                                    </div>
                                                    <svg class="w-5 h-5 transition-colors text-secondary-400 group-hover:text-secondary-600"
                                                        fill="currentColor" viewBox="0 0 24 24">
                                                        <path
                                                            d="M8.59 16.59L13.17 12L8.59 7.41L10 6L16 12L10 18L8.59 16.59Z" />
                                                    </svg>
                                                </div>
                                            </a>
                                        @endif

                                        <!-- Additional Quick Action -->
                                        <a href="{{ route('donations') }}"
                                            class="group relative bg-gradient-to-br from-neutral-50 to-neutral-100 border-2 hover:from-neutral-100 hover:to-neutral-200 border-neutral-200 hover:border-neutral-300 rounded-xl p-6 transition-all duration-200 transform hover:scale-[1.02]">
                                            <div class="flex items-center gap-4">
                                                <div
                                                    class="flex items-center justify-center w-12 h-12 shadow-lg bg-neutral-500 rounded-xl">
                                                    <svg class="w-6 h-6 text-white" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75" />
                                                    </svg>
                                                </div>
                                                <div class="flex-1">
                                                    <h3 class="font-semibold font-heading text-neutral-800">View My
                                                        Donations</h3>
                                                    <p class="mt-1 text-sm text-neutral-600">Manage existing
                                                        fundraising campaigns</p>
                                                </div>
                                                <svg class="w-5 h-5 transition-colors text-neutral-400 group-hover:text-neutral-600"
                                                    fill="currentColor" viewBox="0 0 24 24">
                                                    <path
                                                        d="M8.59 16.59L13.17 12L8.59 7.41L10 6L16 12L10 18L8.59 16.59Z" />
                                                </svg>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Applications Section -->
                        @if (!$user->hasRole('admin'))
                            <div class="overflow-hidden bg-white border shadow-sm rounded-2xl border-neutral-200">
                                <!-- Header -->
                                <div class="px-6 py-4 bg-gradient-to-r from-primary-600 to-primary-700">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h3
                                                class="flex items-center gap-2 text-lg font-semibold text-white font-heading">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                    <path
                                                        d="M14 2H6C4.9 2 4 2.9 4 4V20C4 21.1 4.89 22 5.99 22H18C19.1 22 20 21.1 20 20V8L14 2ZM18 20H6V4H13V9H18V20Z" />
                                                </svg>
                                                Recent Applications
                                            </h3>
                                            <p class="mt-1 text-sm text-primary-100">Track your fundraising campaign
                                                applications</p>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <div class="bg-white/20 backdrop-blur-sm rounded-lg px-3 py-1.5">
                                                <span class="text-sm font-medium text-white">
                                                    {{ $recentApplications->count() }} of
                                                    {{ $totalActiveApplications }} active
                                                </span>
                                            </div>
                                            @if ($totalActiveApplications > 2)
                                                <a href="{{ route('active') }}"
                                                    class="flex items-center gap-1 px-4 py-2 text-sm font-semibold transition-colors bg-white rounded-lg text-primary-600 hover:bg-primary-50">
                                                    <span>View All</span>
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                        <path
                                                            d="M8.59 16.59L13.17 12L8.59 7.41L10 6L16 12L10 18L8.59 16.59Z" />
                                                    </svg>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="p-6">
                                    @if ($recentApplications->isEmpty())
                                        <!-- Empty State -->
                                        <div class="py-12 text-center">
                                            <div
                                                class="flex items-center justify-center w-24 h-24 mx-auto mb-4 bg-gradient-to-br from-neutral-100 to-neutral-200 rounded-2xl">
                                                <svg class="w-12 h-12 text-neutral-400" fill="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path
                                                        d="M14 2H6C4.9 2 4 2.9 4 4V20C4 21.1 4.89 22 5.99 22H18C19.1 22 20 21.1 20 20V8L14 2ZM18 20H6V4H13V9H18V20Z" />
                                                </svg>
                                            </div>
                                            <h4 class="mb-2 text-lg font-semibold font-heading text-neutral-800">No
                                                Applications Yet</h4>
                                            <p class="max-w-md mx-auto mb-4 text-neutral-600">
                                                You haven't submitted any fundraising applications yet. Get started by
                                                creating your first campaign.
                                            </p>
                                            <div class="flex justify-center gap-3">
                                                @if ($user->user_type === \App\Enums\UserType::Individual)
                                                    <a href="{{ route('individual.application') }}"
                                                        class="bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-200 transform hover:scale-[1.02] flex items-center gap-2">
                                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M19 13H13V19H11V13H5V11H11V5H13V11H19V13Z" />
                                                        </svg>
                                                        Create Personal Campaign
                                                    </a>
                                                @else
                                                    <a href="{{ route('company.application') }}"
                                                        class="bg-gradient-to-r from-secondary-500 to-secondary-600 hover:from-secondary-600 hover:to-secondary-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-200 transform hover:scale-[1.02] flex items-center gap-2">
                                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M19 13H13V19H11V13H5V11H11V5H13V11H19V13Z" />
                                                        </svg>
                                                        Create Company Campaign
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <!-- Applications List -->
                                        <div class="mb-6 space-y-4">
                                            @foreach ($recentApplications as $application)
                                                <div
                                                    class="p-6 transition-all duration-200 border-2 bg-gradient-to-r from-neutral-50 to-white border-neutral-200 hover:border-primary-300 rounded-xl hover:shadow-md group">
                                                    <div class="flex items-start justify-between">
                                                        <div class="flex-1">
                                                            <!-- Application Header -->
                                                            <div class="flex items-center gap-3 mb-3">
                                                                <div class="flex items-center gap-2">
                                                                    <div
                                                                        class="flex items-center justify-center w-8 h-8 rounded-lg bg-primary-100">
                                                                        <svg class="w-4 h-4 text-primary-600"
                                                                            fill="currentColor" viewBox="0 0 24 24">
                                                                            <path
                                                                                d="M14 2H6C4.9 2 4 2.9 4 4V20C4 21.1 4.89 22 5.99 22H18C19.1 22 20 21.1 20 20V8L14 2ZM18 20H6V4H13V9H18V20Z" />
                                                                        </svg>
                                                                    </div>
                                                                    <span
                                                                        class="text-sm font-semibold text-neutral-700">
                                                                        #{{ $application->application_number }}
                                                                    </span>
                                                                </div>
                                                                <span
                                                                    class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold border {{ $application->status->getColorClass() }}">
                                                                    {!! $application->status->getIcon() !!}
                                                                    {{ $application->status->getDisplayName() }}
                                                                </span>
                                                            </div>

                                                            <!-- Campaign Title -->
                                                            <h4
                                                                class="mb-3 text-lg font-semibold transition-colors font-heading text-neutral-800 group-hover:text-primary-700">
                                                                {{ $application->applicant->contribution_name }}
                                                            </h4>

                                                            <!-- Application Details -->
                                                            <div class="grid gap-3 text-sm sm:grid-cols-2">
                                                                <div class="flex items-center gap-2">
                                                                    <div
                                                                        class="flex items-center justify-center w-5 h-5 rounded bg-neutral-200">
                                                                        <svg class="w-3 h-3 text-neutral-600"
                                                                            fill="currentColor" viewBox="0 0 24 24">
                                                                            <path
                                                                                d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM13 17H11V15H13V17ZM13 13H11V7H13V13Z" />
                                                                        </svg>
                                                                    </div>
                                                                    <span
                                                                        class="font-medium text-neutral-500">Submitted:</span>
                                                                    <span class="font-semibold text-neutral-800">
                                                                        {{ $application->submitted_at->format('M j, Y') }}
                                                                    </span>
                                                                </div>

                                                                @if ($application->reviewed_at)
                                                                    <div class="flex items-center gap-2">
                                                                        <div
                                                                            class="flex items-center justify-center w-5 h-5 rounded bg-success-200">
                                                                            <svg class="w-3 h-3 text-success-600"
                                                                                fill="currentColor"
                                                                                viewBox="0 0 24 24">
                                                                                <path
                                                                                    d="M9 16.17L4.83 12L3.41 13.41L9 19L21 7L19.59 5.59L9 16.17Z" />
                                                                            </svg>
                                                                        </div>
                                                                        <span
                                                                            class="font-medium text-neutral-500">Reviewed:</span>
                                                                        <span class="font-semibold text-neutral-800">
                                                                            {{ $application->reviewed_at->format('M j, Y') }}
                                                                        </span>
                                                                    </div>
                                                                @endif
                                                            </div>

                                                            <!-- Description -->
                                                            @if ($application->applicant->contribution_description)
                                                                <div class="pt-4 mt-4 border-t border-neutral-200">
                                                                    <p
                                                                        class="text-sm leading-relaxed text-neutral-600 line-clamp-2">
                                                                        {{ Str::limit($application->applicant->contribution_description, 150) }}
                                                                    </p>
                                                                </div>
                                                            @endif
                                                        </div>

                                                        <!-- Action Button -->
                                                        <div class="flex-shrink-0 ml-6">
                                                            @if ($application->applicant_type === \App\Models\Individual::class)
                                                                <a href="{{ route('individual.applications.show', $application->application_number) }}"
                                                                    class="bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white px-4 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 transform hover:scale-[1.02] flex items-center gap-2 shadow-md hover:shadow-lg">
                                                                    <span>View Details</span>
                                                                    <svg class="w-4 h-4" fill="currentColor"
                                                                        viewBox="0 0 24 24">
                                                                        <path
                                                                            d="M8.59 16.59L13.17 12L8.59 7.41L10 6L16 12L10 18L8.59 16.59Z" />
                                                                    </svg>
                                                                </a>
                                                            @else
                                                                <a href="{{ route('company.applications.show', $application->application_number) }}"
                                                                    class="bg-gradient-to-r from-secondary-500 to-secondary-600 hover:from-secondary-600 hover:to-secondary-700 text-white px-4 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 transform hover:scale-[1.02] flex items-center gap-2 shadow-md hover:shadow-lg">
                                                                    <span>View Details</span>
                                                                    <svg class="w-4 h-4" fill="currentColor"
                                                                        viewBox="0 0 24 24">
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

                                        <!-- Quick Navigation Cards -->
                                        <div class="pt-6 border-t border-neutral-200">
                                            <h4
                                                class="flex items-center gap-2 mb-4 text-sm font-semibold text-neutral-700">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                    <path
                                                        d="M12 2L2 7L12 12L22 7L12 2ZM2 17L12 22L22 17M2 12L12 17L22 12" />
                                                </svg>
                                                Quick Navigation
                                            </h4>
                                            <div class="grid gap-4 sm:grid-cols-2">
                                                <!-- All Applications -->
                                                <a href="{{ route('active') }}"
                                                    class="group bg-gradient-to-br from-primary-50 to-primary-100 hover:from-primary-100 hover:to-primary-200 border-2 border-primary-200 hover:border-primary-300 rounded-xl p-4 transition-all duration-200 transform hover:scale-[1.02]">
                                                    <div class="flex items-center gap-3">
                                                        <div
                                                            class="flex items-center justify-center w-10 h-10 transition-shadow shadow-md bg-primary-500 rounded-xl group-hover:shadow-lg">
                                                            <svg class="w-5 h-5 text-white" fill="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path
                                                                    d="M14 2H6C4.9 2 4 2.9 4 4V20C4 21.1 4.89 22 5.99 22H18C19.1 22 20 21.1 20 20V8L14 2ZM18 20H6V4H13V9H18V20Z" />
                                                            </svg>
                                                        </div>
                                                        <div class="flex-1">
                                                            <h5
                                                                class="font-semibold text-primary-800 group-hover:text-primary-900">
                                                                All Applications</h5>
                                                            <p class="text-xs text-primary-600 mt-0.5">View submitted &
                                                                under review</p>
                                                        </div>
                                                        <svg class="w-4 h-4 transition-colors text-primary-400 group-hover:text-primary-600"
                                                            fill="currentColor" viewBox="0 0 24 24">
                                                            <path
                                                                d="M8.59 16.59L13.17 12L8.59 7.41L10 6L16 12L10 18L8.59 16.59Z" />
                                                        </svg>
                                                    </div>
                                                </a>

                                                <!-- Needs Attention -->
                                                <a href="{{ route('pending') }}"
                                                    class="group bg-gradient-to-br from-secondary-50 to-secondary-100 hover:from-secondary-100 hover:to-secondary-200 border-2 border-secondary-200 hover:border-secondary-300 rounded-xl p-4 transition-all duration-200 transform hover:scale-[1.02]">
                                                    <div class="flex items-center gap-3">
                                                        <div
                                                            class="flex items-center justify-center w-10 h-10 transition-shadow shadow-md bg-secondary-500 rounded-xl group-hover:shadow-lg">
                                                            <svg class="w-5 h-5 text-white" fill="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path
                                                                    d="M12 9V11H14V9H12ZM12 17H14V13H12V17ZM12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM12 20C7.59 20 4 16.41 4 12C4 7.59 7.59 4 12 4C16.41 4 20 7.59 20 12C20 16.41 16.41 20 12 20Z" />
                                                            </svg>
                                                        </div>
                                                        <div class="flex-1">
                                                            <h5
                                                                class="font-semibold text-secondary-800 group-hover:text-secondary-900">
                                                                Needs Attention</h5>
                                                            <p class="text-xs text-secondary-600 mt-0.5">Rejected,
                                                                resubmitted, or pending info</p>
                                                        </div>
                                                        <svg class="w-4 h-4 transition-colors text-secondary-400 group-hover:text-secondary-600"
                                                            fill="currentColor" viewBox="0 0 24 24">
                                                            <path
                                                                d="M8.59 16.59L13.17 12L8.59 7.41L10 6L16 12L10 18L8.59 16.59Z" />
                                                        </svg>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        @if ($user->hasRole('admin'))
                            <!-- Admin Quick Actions -->
                            <div class="overflow-hidden bg-white border shadow-sm rounded-2xl border-neutral-200">
                                <div class="px-6 py-4 bg-gradient-to-r from-danger-600 to-danger-700">
                                    <h2 class="flex items-center gap-2 text-lg font-semibold text-white font-heading">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2L2 7L12 12L22 7L12 2ZM2 17L12 22L22 17M2 12L12 17L22 12" />
                                        </svg>
                                        Admin Panel
                                    </h2>
                                    <p class="mt-1 text-sm text-danger-100">Manage applications and donations</p>
                                </div>
                                <div class="p-6 space-y-4">
                                    <a href="{{ route('admin.applications.index') }}"
                                        class="group flex items-center gap-3 p-4 bg-gradient-to-r from-primary-50 to-primary-100 hover:from-primary-100 hover:to-primary-200 border border-primary-200 hover:border-primary-300 rounded-xl transition-all duration-200 transform hover:scale-[1.02]">
                                        <div
                                            class="flex items-center justify-center w-10 h-10 rounded-lg shadow-md bg-primary-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                class="w-5 h-5 text-white">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="font-semibold text-primary-800 group-hover:text-primary-900">
                                                View Applications</h3>
                                            <p class="text-sm text-primary-600">Review pending applications</p>
                                        </div>
                                        <svg class="w-4 h-4 transition-colors text-primary-400 group-hover:text-primary-600"
                                            fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8.59 16.59L13.17 12L8.59 7.41L10 6L16 12L10 18L8.59 16.59Z" />
                                        </svg>
                                    </a>

                                    <a href="{{ route('admin.donation-links.index') }}"
                                        class="group flex items-center gap-3 p-4 bg-gradient-to-r from-success-50 to-success-100 hover:from-success-100 hover:to-success-200 border border-success-200 hover:border-success-300 rounded-xl transition-all duration-200 transform hover:scale-[1.02]">
                                        <div
                                            class="flex items-center justify-center w-10 h-10 rounded-lg shadow-md bg-success-500">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="font-semibold text-success-800 group-hover:text-success-900">
                                                View Donations</h3>
                                            <p class="text-sm text-success-600">Monitor donation activity</p>
                                        </div>
                                        <svg class="w-4 h-4 transition-colors text-success-400 group-hover:text-success-600"
                                            fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8.59 16.59L13.17 12L8.59 7.41L10 6L16 12L10 18L8.59 16.59Z" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        @endif

                        @if (!$user->hasRole('admin'))
                            <!-- Statistics Card -->
                            <div class="overflow-hidden bg-white border shadow-sm rounded-2xl border-neutral-200">
                                <div class="px-6 py-4 bg-gradient-to-r from-secondary-600 to-secondary-700">
                                    <h2 class="flex items-center gap-2 text-lg font-semibold text-white font-heading">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M16 6L18.29 8.29L13.41 13.17L9.41 9.17L2 16.59L3.41 18L9.41 12L13.41 16L19.71 9.71L22 12V6H16Z" />
                                        </svg>
                                        Quick Stats
                                    </h2>
                                </div>
                                <div class="p-6 space-y-4">
                                    <div
                                        class="flex items-center justify-between p-4 bg-gradient-to-r from-primary-50 to-primary-100 rounded-xl">
                                        <div>
                                            <p class="text-sm font-medium text-primary-600">Active Donation Campaigns
                                            </p>
                                            <p class="text-2xl font-bold text-primary-800">
                                                {{ $donationStats['userCampaigns'] ?? 0 }}
                                            </p>
                                        </div>
                                        <div
                                            class="flex items-center justify-center w-12 h-12 bg-primary-500 rounded-xl">
                                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    d="M19 3H5C3.9 3 3 3.9 3 5V19C3 20.1 3.9 21 5 21H19C20.1 21 21 20.1 21 19V5C21 3.9 20.1 3 19 3ZM19 19H5V5H19V19ZM17 12H7V10H17V12ZM15 16H7V14H15V16ZM17 8H7V6H17V8Z" />
                                            </svg>
                                        </div>
                                    </div>

                                    <div
                                        class="flex items-center justify-between p-4 bg-gradient-to-r from-success-50 to-success-100 rounded-xl">
                                        <div>
                                            <p class="text-sm font-medium text-success-600">Total Raised</p>
                                            <p class="text-2xl font-bold text-success-800">
                                                KES {{ number_format($donationStats['totalRaised'], 2) ?? 0 }}
                                            </p>
                                        </div>
                                        <div
                                            class="flex items-center justify-center w-12 h-12 bg-success-500 rounded-xl">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                class="w-6 h-6 text-white">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                                            </svg>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        @endif

                        <!-- Help & Support -->
                        <div class="overflow-hidden bg-white border shadow-sm rounded-2xl border-neutral-200">
                            <div class="px-6 py-4 bg-gradient-to-r from-neutral-600 to-neutral-700">
                                <h2 class="flex items-center gap-2 text-lg font-semibold text-white font-heading">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M11 18H13V16H11V18ZM12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM12 20C7.59 20 4 16.41 4 12C4 7.59 7.59 4 12 4C16.41 4 20 7.59 20 12C20 16.41 16.41 20 12 20ZM12 6C9.79 6 8 7.79 8 10H10C10 8.9 10.9 8 12 8C13.1 8 14 8.9 14 10C14 12 11 11.75 11 15H13C13 12.75 16 12.5 16 10C16 7.79 14.21 6 12 6Z" />
                                    </svg>
                                    Help & Support
                                </h2>
                            </div>
                            <div class="p-6 space-y-3">
                                <a href="#"
                                    class="flex items-center gap-3 p-3 transition-colors rounded-lg hover:bg-neutral-50">
                                    <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-primary-100">
                                        <svg class="w-4 h-4 text-primary-600" fill="currentColor"
                                            viewBox="0 0 24 24">
                                            <path
                                                d="M14 2H6C4.9 2 4 2.9 4 4V20C4 21.1 4.89 22 5.99 22H18C19.1 22 20 21.1 20 20V8L14 2ZM18 20H6V4H13V9H18V20Z" />
                                        </svg>
                                    </div>
                                    <span class="text-sm font-medium text-neutral-700">Documentation</span>
                                </a>

                                <a href="#"
                                    class="flex items-center gap-3 p-3 transition-colors rounded-lg hover:bg-neutral-50">
                                    <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-success-100">
                                        <svg class="w-4 h-4 text-success-600" fill="currentColor"
                                            viewBox="0 0 24 24">
                                            <path
                                                d="M20 4H4C2.89 4 2 4.89 2 6V18C2 19.11 2.89 20 4 20H20C21.11 20 22 19.11 22 18V6C22 4.89 21.11 4 20 4ZM20 8L12 13L4 8V6L12 11L20 6V8Z" />
                                        </svg>
                                    </div>
                                    <span class="text-sm font-medium text-neutral-700">Contact Support</span>
                                </a>

                                <a href="#"
                                    class="flex items-center gap-3 p-3 transition-colors rounded-lg hover:bg-neutral-50">
                                    <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-secondary-100">
                                        <svg class="w-4 h-4 text-secondary-600" fill="currentColor"
                                            viewBox="0 0 24 24">
                                            <path
                                                d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM13 17H11V15H13V17ZM13 13H11V7H13V13Z" />
                                        </svg>
                                    </div>
                                    <span class="text-sm font-medium text-neutral-700">FAQ</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
