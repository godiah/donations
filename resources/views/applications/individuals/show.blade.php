<x-app-layout>

    <div class="pt-16">
        <div class="relative py-8 sm:py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!--Header -->
                <div class="mb-8">
                    <div class="bg-gradient-to-r from-primary-600 to-primary-700 rounded-2xl p-6 text-white shadow-xl">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                </div>
                                <div class="space-y-2">
                                    <h2 class="text-2xl font-heading font-bold">Application Details</h2>
                                    <p
                                        class="text-xs font-medium text-neutral-500 bg-neutral-100 px-3 py-1 rounded-full inline-block">
                                        #{{ $application->application_number }}
                                    </p>
                                </div>
                            </div>
                            <a href="{{ route('donations') }}"
                                class="bg-white text-primary-600 hover:bg-primary-50 px-6 py-3 rounded-xl font-semibold transition-all duration-200 transform hover:scale-[1.02] flex items-center gap-2 shadow-md hover:shadow-lg">
                                <svg class="w-4 h-4 " fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20 11H7.83L13.42 5.41L12 4L4 12L12 20L13.41 18.59L7.83 13H20V11Z" />
                                </svg>
                                Back
                            </a>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-8">
                        <!-- Application Status -->
                        <div class="bg-white rounded-2xl shadow-lg border border-neutral-100 overflow-hidden">
                            <div
                                class="px-8 py-6 bg-gradient-to-r from-primary-50 to-blue-50 border-b border-neutral-100">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div
                                            class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-xl font-heading font-bold text-neutral-800">Application Status
                                        </h3>
                                    </div>
                                    <span
                                        class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium
                                    {{ $application->status->value === 'draft' ? 'bg-neutral-100 text-neutral-700' : '' }}
                                    {{ $application->status->value === 'submitted' ? 'bg-secondary-100 text-secondary-700' : '' }}
                                    {{ $application->status->value === 'under_review' ? 'bg-primary-100 text-primary-700' : '' }}
                                    {{ $application->status->value === 'additional_info_required' ? 'bg-secondary-100 text-secondary-700' : '' }}
                                    {{ $application->status->value === 'approved' ? 'bg-success-100 text-success-700' : '' }}
                                    {{ $application->status->value === 'rejected' ? 'bg-danger-100 text-danger-700' : '' }}
                                    {{ $application->status->value === 'resubmitted' ? 'bg-purple-100 text-purple-700' : '' }}">
                                        {{ ucfirst(str_replace('_', ' ', $application->status->value)) }}
                                    </span>
                                </div>
                            </div>

                            <div class="p-8">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                    <div
                                        class="bg-gradient-to-r from-neutral-50 to-white rounded-xl p-4 border border-neutral-100">
                                        <div class="flex items-center space-x-3 mb-2">
                                            <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                            </svg>
                                            <span class="text-sm font-medium text-neutral-600">Application Number</span>
                                        </div>
                                        <span
                                            class="font-heading font-bold text-neutral-800">{{ $application->application_number }}</span>
                                    </div>

                                    <div
                                        class="bg-gradient-to-r from-neutral-50 to-white rounded-xl p-4 border border-neutral-100">
                                        <div class="flex items-center space-x-3 mb-2">
                                            <svg class="w-5 h-5 text-success-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                            </svg>
                                            <span class="text-sm font-medium text-neutral-600">Submitted</span>
                                        </div>
                                        <span
                                            class="font-heading font-bold text-neutral-800">{{ $application->submitted_at->format('M j, Y g:i A') }}</span>
                                    </div>

                                    @if ($application->reviewed_at)
                                        <div
                                            class="bg-gradient-to-r from-neutral-50 to-white rounded-xl p-4 border border-neutral-100">
                                            <div class="flex items-center space-x-3 mb-2">
                                                <svg class="w-5 h-5 text-purple-500" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span class="text-sm font-medium text-neutral-600">Reviewed</span>
                                            </div>
                                            <span
                                                class="font-heading font-bold text-neutral-800">{{ $application->reviewed_at->format('M j, Y g:i A') }}</span>
                                        </div>

                                        @if ($application->reviewer)
                                            <div
                                                class="bg-gradient-to-r from-neutral-50 to-white rounded-xl p-4 border border-neutral-100">
                                                <div class="flex items-center space-x-3 mb-2">
                                                    <svg class="w-5 h-5 text-secondary-500" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                    </svg>
                                                    <span class="text-sm font-medium text-neutral-600">Reviewed
                                                        By</span>
                                                </div>
                                                <span
                                                    class="font-heading font-bold text-neutral-800">{{ $application->reviewer->name }}</span>
                                            </div>
                                        @endif
                                    @endif
                                </div>

                                @if ($application->admin_comments)
                                    <div
                                        class="bg-gradient-to-r from-secondary-50 to-secondary-50/50 rounded-xl p-6 border border-secondary-100">
                                        <div class="flex items-start space-x-3">
                                            <div
                                                class="w-8 h-8 bg-secondary-500 rounded-lg flex items-center justify-center mt-0.5">
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <h4 class="font-heading font-bold text-neutral-800 mb-2">Admin Comments
                                                </h4>
                                                <p class="text-sm text-neutral-700 leading-relaxed">
                                                    {{ $application->admin_comments }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Contribution Details -->
                        <div class="bg-white rounded-2xl shadow-lg border border-neutral-100 overflow-hidden">
                            <div
                                class="px-8 py-6 bg-gradient-to-r from-success-50 to-green-50 border-b border-neutral-100">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="w-10 h-10 bg-gradient-to-br from-success-500 to-success-600 rounded-xl flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-white">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z" />
                                        </svg>

                                    </div>
                                    <h3 class="text-xl font-heading font-bold text-neutral-800">Contribution Details
                                    </h3>
                                </div>
                            </div>

                            <div class="p-8">
                                <div class="mb-6">
                                    <h4 class="text-xl font-heading font-bold text-neutral-800 mb-3">
                                        {{ $application->applicant->contribution_name }}
                                    </h4>
                                    @if ($application->applicant->contribution_description)
                                        <div class="bg-neutral-50 rounded-xl p-4 border border-neutral-100">
                                            <p class="text-sm text-neutral-700 leading-relaxed">
                                                {{ $application->applicant->contribution_description }}
                                            </p>
                                        </div>
                                    @endif
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-4">
                                    <div
                                        class="bg-gradient-to-r from-success-50 to-white rounded-xl p-4 border border-success-100">
                                        <div class="flex items-center space-x-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                class="w-6 h-6 text-success-500">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                                            </svg>
                                            <span class="font-medium text-neutral-600">Target Amount</span>
                                        </div>
                                        <div class="text-2xl font-heading font-bold text-success-600">
                                            KSh {{ number_format($application->applicant->target_amount, 2) }}
                                        </div>
                                    </div>

                                    <div
                                        class="bg-gradient-to-r from-primary-50 to-white rounded-xl p-4 border border-primary-100">
                                        <div class="flex items-center space-x-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                class="w-6 h-6 text-primary-500">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                            </svg>
                                            <span class="font-medium text-neutral-600">Target Date</span>
                                        </div>
                                        <div class="text-lg font-heading font-bold text-neutral-800">
                                            {{ $application->applicant->target_date->format('M j, Y') }}
                                        </div>
                                    </div>

                                    <div
                                        class="bg-gradient-to-r from-purple-50 to-white rounded-xl p-4 border border-purple-100 md:col-span-2 lg:col-span-1">
                                        <div class="flex items-center space-x-3">
                                            <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="font-medium text-neutral-600">Reason</span>
                                        </div>
                                        <div class="text-lg font-heading font-bold text-neutral-800">
                                            {{ $application->applicant->contributionReason->name ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Personal Information -->
                        <div class="bg-white rounded-2xl shadow-lg border border-neutral-100 overflow-hidden">
                            <div
                                class="px-8 py-6 bg-gradient-to-r from-secondary-50 to-orange-50 border-b border-neutral-100">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="w-10 h-10 bg-gradient-to-br from-secondary-500 to-secondary-600 rounded-xl flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-heading font-bold text-neutral-800">Personal Information
                                    </h3>
                                </div>
                            </div>

                            <div class="p-8">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Left Column -->
                                    <div class="space-y-4">
                                        <div
                                            class="bg-gradient-to-r from-neutral-50 to-white rounded-xl p-4 border border-neutral-100">
                                            <div class="flex items-center space-x-3 mb-2">
                                                <svg class="w-5 h-5 text-primary-500" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                                <span class="text-sm font-medium text-neutral-600">Full Name</span>
                                            </div>
                                            <div class="font-heading font-bold text-neutral-800">
                                                {{ $application->applicant->getFullNameAttribute() }}</div>
                                        </div>

                                        <div
                                            class="bg-gradient-to-r from-neutral-50 to-white rounded-xl p-4 border border-neutral-100">
                                            <div class="flex items-center space-x-3 mb-2">
                                                <svg class="w-5 h-5 text-success-500" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                </svg>
                                                <span class="text-sm font-medium text-neutral-600">Email</span>
                                            </div>
                                            <div class="font-heading font-bold text-neutral-800">
                                                {{ $application->applicant->email }}</div>
                                        </div>

                                        <div
                                            class="bg-gradient-to-r from-neutral-50 to-white rounded-xl p-4 border border-neutral-100">
                                            <div class="flex items-center space-x-3 mb-2">
                                                <svg class="w-5 h-5 text-purple-500" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                </svg>
                                                <span class="text-sm font-medium text-neutral-600">Phone</span>
                                            </div>
                                            <div class="font-heading font-bold text-neutral-800">
                                                {{ $application->applicant->phone }}</div>
                                        </div>
                                    </div>

                                    <!-- Right Column -->
                                    <div class="space-y-4">
                                        <div
                                            class="bg-gradient-to-r from-neutral-50 to-white rounded-xl p-4 border border-neutral-100">
                                            <div class="flex items-center space-x-3 mb-2">
                                                <svg class="w-5 h-5 text-secondary-500" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 114 0v2m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                                </svg>
                                                <span class="text-sm font-medium text-neutral-600">ID Type</span>
                                            </div>
                                            <div class="font-heading font-bold text-neutral-800">
                                                {{ $application->applicant->idType->display_name ?? 'N/A' }}</div>
                                        </div>

                                        <div
                                            class="bg-gradient-to-r from-neutral-50 to-white rounded-xl p-4 border border-neutral-100">
                                            <div class="flex items-center space-x-3 mb-2">
                                                <svg class="w-5 h-5 text-primary-500" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                                </svg>
                                                <span class="text-sm font-medium text-neutral-600">ID Number</span>
                                            </div>
                                            <div class="font-heading font-bold text-neutral-800">
                                                {{ $application->applicant->id_number }}</div>
                                        </div>

                                        <div
                                            class="bg-gradient-to-r from-neutral-50 to-white rounded-xl p-4 border border-neutral-100">
                                            <div class="flex items-center space-x-3 mb-2">
                                                <svg class="w-5 h-5 text-danger-500" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <span class="text-sm font-medium text-neutral-600">KRA PIN</span>
                                            </div>
                                            <div class="font-heading font-bold text-neutral-800">
                                                {{ $application->applicant->kra_pin ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        @if ($application->applicant->additional_info)
                            <div class="bg-white rounded-2xl shadow-lg border border-neutral-100 overflow-hidden">
                                <div
                                    class="px-8 py-6 bg-gradient-to-r from-purple-50 to-blue-50 border-b border-neutral-100">
                                    <div class="flex items-center space-x-3">
                                        <div
                                            class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-xl font-heading font-bold text-neutral-800">Additional
                                            Information
                                        </h3>
                                    </div>
                                </div>
                                <div class="p-8">
                                    <div class="bg-neutral-50 rounded-xl p-6 border border-neutral-100">
                                        @if (is_array($application->applicant->additional_info))
                                            <div class="space-y-4">
                                                @foreach ($application->applicant->additional_info as $key => $value)
                                                    <div class="flex items-start space-x-3">
                                                        <span
                                                            class="font-medium text-neutral-600 min-w-0 flex-shrink-0">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                                        <span class="text-neutral-800">{{ $value }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-neutral-700 leading-relaxed">
                                                {{ $application->applicant->additional_info }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-8">
                        <!-- Enhanced Payout Mandate -->
                        @if ($application->payoutMandate)
                            <div class="bg-white rounded-2xl shadow-lg border border-neutral-100 overflow-hidden">
                                <div
                                    class="px-6 py-5 bg-gradient-to-r from-primary-50 to-blue-50 border-b border-neutral-100">
                                    <div class="flex items-center space-x-3">
                                        <div
                                            class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-heading font-bold text-neutral-800">Payout Mandate</h3>
                                    </div>
                                </div>
                                <div class="p-6">
                                    @if ($application->payoutMandate->isSingle())
                                        <div class="bg-success-50 rounded-xl p-4 border border-success-100">
                                            <div class="flex items-center space-x-3">
                                                <div
                                                    class="w-8 h-8 bg-success-500 rounded-full flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-white" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="font-medium text-success-700">Single Mandate</p>
                                                    <p class="text-xs text-success-600">Direct payout setup</p>
                                                </div>
                                            </div>
                                        </div>
                                    @elseif ($application->payoutMandate->isDual())
                                        <div class="space-y-4">
                                            <div class="bg-primary-50 rounded-xl p-4 border border-primary-100">
                                                <div class="flex items-center space-x-3 mb-3">
                                                    <div
                                                        class="w-8 h-8 bg-primary-500 rounded-full flex items-center justify-center">
                                                        <svg class="w-4 h-4 text-white" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <p class="font-medium text-primary-700">Dual Mandate</p>
                                                        <p class="text-xs text-primary-600">Requires checker approval
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="space-y-3">
                                                    <div>
                                                        <span class="text-sm font-medium text-neutral-600">Checker
                                                            Name</span>
                                                        <p class="text-sm font-medium text-neutral-800">
                                                            {{ $application->payoutMandate->checker->name ?? 'N/A' }}
                                                        </p>
                                                    </div>
                                                    <div>
                                                        <span class="text-sm font-medium text-neutral-600">Checker
                                                            Email</span>
                                                        <p class="text-sm font-medium text-neutral-800">
                                                            {{ $application->payoutMandate->checker->email ?? 'N/A' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Enhanced Support Documents -->
                        @if ($supportDocuments->isNotEmpty())
                            <div class="bg-white rounded-2xl shadow-lg border border-neutral-100 overflow-hidden">
                                <div
                                    class="px-6 py-5 bg-gradient-to-r from-secondary-50 to-orange-50 border-b border-neutral-100">
                                    <div class="flex items-center space-x-3">
                                        <div
                                            class="w-10 h-10 bg-gradient-to-br from-secondary-500 to-secondary-600 rounded-xl flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-heading font-bold text-neutral-800">Support Documents
                                        </h3>
                                    </div>
                                </div>
                                <div class="p-6">
                                    <div class="space-y-3">
                                        @foreach ($supportDocuments as $document)
                                            <div
                                                class="bg-gradient-to-r from-neutral-50 to-white rounded-xl p-4 border border-neutral-100 hover:shadow-sm transition-all duration-200">
                                                <div class="flex items-start justify-between">
                                                    <div class="flex items-start space-x-3 flex-1">
                                                        <div
                                                            class="w-8 h-8 bg-neutral-100 rounded-lg flex items-center justify-center mt-0.5">
                                                            <svg class="w-4 h-4 text-neutral-500" fill="currentColor"
                                                                viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd"
                                                                    d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                                                                    clip-rule="evenodd" />
                                                            </svg>
                                                        </div>
                                                        <div class="min-w-0 flex-1">
                                                            <p class="text-sm font-medium text-neutral-800 truncate">
                                                                {{ $document->original_filename }}
                                                            </p>
                                                            @if ($document->verification_notes)
                                                                <p class="text-xs text-neutral-500 mt-1">
                                                                    {{ $document->verification_notes }}</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ml-3 flex-shrink-0
                                                    {{ $document->status === 'verified' ? 'bg-success-100 text-success-700' : '' }}
                                                    {{ $document->status === 'pending' ? 'bg-secondary-100 text-secondary-700' : '' }}
                                                    {{ $document->status === 'rejected' ? 'bg-danger-100 text-danger-700' : '' }}">
                                                        {{ ucfirst($document->status) }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Quick Summary Card -->
                        <div
                            class="bg-gradient-to-br from-neutral-50 via-white to-neutral-50 rounded-2xl shadow-lg border border-neutral-100 overflow-hidden">
                            <div
                                class="px-6 py-5 bg-gradient-to-r from-neutral-100 to-neutral-50 border-b border-neutral-200">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="w-10 h-10 bg-gradient-to-br from-neutral-600 to-neutral-700 rounded-xl flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-heading font-bold text-neutral-800">Quick Summary</h3>
                                </div>
                            </div>
                            <div class="p-6">
                                <div class="space-y-4">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-neutral-600">Application Type</span>
                                        <span class="text-sm font-medium text-neutral-800">Individual</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-neutral-600">Documents</span>
                                        <span
                                            class="text-sm font-medium text-neutral-800">{{ $supportDocuments->count() }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-neutral-600">Payout Setup</span>
                                        <span class="text-sm font-medium text-neutral-800">
                                            {{ $application->payoutMandate ? 'Configured' : 'Not Set' }}
                                        </span>
                                    </div>
                                    @if ($application->payoutMandate)
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm font-medium text-neutral-600">Mandate Type</span>
                                            <span class="text-sm font-medium text-neutral-800">
                                                {{ $application->payoutMandate->isSingle() ? 'Single' : 'Dual' }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
