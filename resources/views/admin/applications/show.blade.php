<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="py-2 space-y-1">
                <h2 class="text-2xl font-bold font-heading text-neutral-800">
                    Application Details
                </h2>
                <p class="text-sm font-medium text-neutral-500">
                    {{ $application->application_number }}
                </p>
            </div>
            <a href="{{ route('admin.applications.index') }}"
                class="inline-flex items-center px-4 py-2 font-medium text-white transition-all duration-200 shadow-lg bg-gradient-to-r from-primary-500 to-primary-600 rounded-xl hover:from-primary-600 hover:to-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 hover:shadow-xl">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M15.41 7.41L14 6L8 12L14 18L15.41 16.59L10.83 12L15.41 7.41Z" />
                </svg>
                Back to Applications
            </a>
        </div>
    </x-slot>

    <div class="pt-6 pb-8">
        <div class="px-4 mx-auto space-y-8 max-w-7xl sm:px-6 lg:px-8">

            <!-- Quick Status Overview Cards -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                <!-- Status Card -->
                <div class="p-6 info-card rounded-xl card-hover">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="mb-1 text-sm font-medium text-neutral-500">Status</p>
                            @php
                                $statusClass = match ($application->status->value) {
                                    'submitted' => 'bg-primary-100 text-primary-800 border-primary-200',
                                    'under_review' => 'bg-secondary-100 text-secondary-800 border-secondary-200',
                                    'additional_info_required' => 'bg-orange-100 text-orange-800 border-orange-200',
                                    'approved' => 'bg-success-100 text-success-800 border-success-200',
                                    'rejected' => 'bg-danger-100 text-danger-800 border-danger-200',
                                    default => 'bg-neutral-100 text-neutral-800 border-neutral-200',
                                };
                            @endphp
                            <span id="status-badge"
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border {{ $statusClass }}">
                                {{ ucwords(str_replace('_', ' ', $application->status->value)) }}
                            </span>
                        </div>
                        <div class="flex items-center justify-center w-12 h-12 rounded-full bg-primary-100">
                            <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Submitted Date Card -->
                <div class="p-6 info-card rounded-xl card-hover">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="mb-1 text-sm font-medium text-neutral-500">Submitted</p>
                            <p class="text-lg font-semibold text-neutral-800">
                                {{ $application->submitted_at ? $application->submitted_at->format('M d, Y') : 'Not submitted' }}
                            </p>
                            @if ($application->submitted_at)
                                <p class="text-xs text-neutral-500">{{ $application->submitted_at->format('H:i') }}</p>
                            @endif
                        </div>
                        <div class="flex items-center justify-center w-12 h-12 rounded-full bg-secondary-100">
                            <svg class="w-6 h-6 text-secondary-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Target Amount Card -->
                <div class="p-6 info-card rounded-xl card-hover">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="mb-1 text-sm font-medium text-neutral-500">Target Amount</p>
                            @if ($application->applicant->target_amount)
                                <p class="text-lg font-semibold text-neutral-800">
                                    KES {{ number_format($application->applicant->target_amount, 2) }}
                                </p>
                            @else
                                <p class="text-lg font-semibold text-neutral-800">
                                    No Target Set
                                </p>
                            @endif
                        </div>
                        <div class="flex items-center justify-center w-12 h-12 rounded-full bg-success-100">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-success-600">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Reviewer Card -->
                <div class="p-6 info-card rounded-xl card-hover">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="mb-1 text-sm font-medium text-neutral-500">Reviewer</p>
                            <p class="text-lg font-semibold text-neutral-800">
                                {{ $application->reviewer ? $application->reviewer->name : 'Unassigned' }}
                            </p>
                            @if ($application->reviewed_at)
                                <p class="text-xs text-neutral-500">{{ $application->reviewed_at->format('M d, Y') }}
                                </p>
                            @endif
                        </div>
                        <div class="flex items-center justify-center w-12 h-12 bg-purple-100 rounded-full">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            @php
                // Count total and verified support documents
                $supportDocuments = $application->applicant->supportDocuments;
                $totalDocuments = $supportDocuments->count();
                $verifiedDocuments = $supportDocuments->where('status', 'verified')->count();
                $allDocumentsVerified = $totalDocuments > 0 && $totalDocuments === $verifiedDocuments;

                // Check KYC verification for individual applications
                $kycVerified = true; // Default for companies
                if ($application->applicant_type === 'App\\Models\\Individual') {
                    $latestKyc = $application->applicant
                        ->kycVerifications()
                        ->where('application_id', $application->id)
                        ->latest()
                        ->first();
                    $kycVerified = $latestKyc && $latestKyc->status === 'verified';
                }

                // Determine if application can be approved
                $canApprove = $allDocumentsVerified && $kycVerified;

                // Create verification status message
                $verificationStatus = [];
                if (!$allDocumentsVerified) {
                    $verificationStatus[] = "Support Documents: {$verifiedDocuments}/{$totalDocuments} verified";
                }
                if ($application->applicant_type === 'App\\Models\\Individual' && !$kycVerified) {
                    $verificationStatus[] = 'National ID: Not verified';
                }
            @endphp

            <!-- Action Buttons Row -->
            <div
                class="flex flex-wrap items-center justify-between gap-4 p-6 bg-white border shadow-sm rounded-xl border-neutral-200">
                <div class="flex flex-wrap gap-3">
                    @if ($application->status->value === 'submitted')
                        <button id="review-button"
                            class="inline-flex items-center px-6 py-3 font-medium text-white transition-all duration-200 rounded-lg shadow-sm bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                            data-application-id="{{ $application->id }}">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                </path>
                            </svg>
                            Start Review
                        </button>
                    @endif

                    @if ($application->status->value !== 'approved' && $canApprove)
                        <button id="approve-button"
                            class="inline-flex items-center px-6 py-3 font-medium text-white transition-all duration-200 rounded-lg shadow-sm bg-success-600 hover:bg-success-700 focus:outline-none focus:ring-2 focus:ring-success-500 focus:ring-offset-2"
                            data-application-id="{{ $application->id }}">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            Approve Application
                        </button>
                    @endif
                </div>

                @if ($application->admin_comments)
                    <div
                        class="flex items-center px-4 py-2 space-x-2 text-sm rounded-lg text-neutral-600 bg-neutral-50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-1l-4 4z">
                            </path>
                        </svg>
                        <span>Admin comments available</span>
                    </div>
                @endif
            </div>

            <!-- Verification Status Warning -->
            @if (!$canApprove && $application->status->value !== 'approved')
                <div class="p-6 bg-white border shadow-sm rounded-xl border-neutral-200">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center w-12 h-12 rounded-full bg-secondary-100">
                                <svg class="w-6 h-6 text-secondary-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h3 class="mb-2 text-lg font-semibold font-heading text-secondary-800">
                                Verification Required
                            </h3>
                            <p class="mb-4 text-secondary-700">
                                The following requirements must be met before approval:
                            </p>
                            <ul class="space-y-2 text-sm text-secondary-700">
                                @foreach ($verificationStatus as $status)
                                    <li class="flex items-center space-x-2">
                                        <svg class="w-4 h-4 text-secondary-500" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        <span>{{ $status }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">

                <!-- Left Column - Main Information -->
                <div class="space-y-8 lg:col-span-2">

                    <!-- Applicant Information -->
                    <div class="overflow-hidden bg-white border shadow-sm rounded-xl border-neutral-200">
                        <div class="px-6 py-4 border-b bg-gradient-to-r from-primary-50 to-blue-50 border-neutral-200">
                            <div class="flex items-center space-x-3">
                                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-primary-100">
                                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                        </path>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-semibold font-heading text-neutral-800">
                                    {{ $application->applicant_type === 'App\\Models\\Individual' ? 'Individual' : 'Company' }}
                                    Information
                                </h3>
                            </div>
                        </div>
                        <div class="p-6">
                            @if ($application->applicant_type === 'App\\Models\\Individual')
                                @include('admin.applications.partials.individual-details', [
                                    'individual' => $application->applicant,
                                ])
                            @else
                                @include('admin.applications.partials.company-details', [
                                    'company' => $application->applicant,
                                ])
                            @endif
                        </div>
                    </div>

                    <!-- Contribution Details -->
                    <div class="overflow-hidden bg-white border shadow-sm rounded-xl border-neutral-200">
                        <div
                            class="px-6 py-4 border-b bg-gradient-to-r from-success-50 to-green-50 border-neutral-200">
                            <div class="flex items-center space-x-3">
                                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-success-100">
                                    <svg class="w-5 h-5 text-success-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                        </path>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-semibold font-heading text-neutral-800">Contribution Details
                                </h3>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div class="space-y-1">
                                    <dt class="text-sm font-medium text-neutral-500">Contribution Name</dt>
                                    <dd class="text-base font-semibold text-neutral-800">
                                        {{ $application->applicant->contribution_name }}</dd>
                                </div>
                                <div class="space-y-1">
                                    <dt class="text-sm font-medium text-neutral-500">Contribution Reason</dt>
                                    <dd class="text-base text-neutral-800">
                                        {{ $application->applicant->contributionReason->name ?? 'N/A' }}</dd>
                                </div>
                                <div class="space-y-1">
                                    <dt class="text-sm font-medium text-neutral-500">Target Amount</dt>
                                    <dd class="text-xl font-bold text-success-600">KES
                                        {{ number_format($application->applicant->target_amount, 2) }}</dd>
                                </div>
                                <div class="space-y-1">
                                    <dt class="text-sm font-medium text-neutral-500">Target Date</dt>
                                    <dd class="text-base text-neutral-800">
                                        {{ $application->applicant->target_date ? $application->applicant->target_date->format('M d, Y') : 'Not set' }}
                                    </dd>
                                </div>
                            </div>

                            @if ($application->applicant->contribution_description)
                                <div class="pt-6 mt-6 border-t border-neutral-200">
                                    <dt class="mb-3 text-sm font-medium text-neutral-500">Description</dt>
                                    <dd class="p-4 text-sm leading-relaxed rounded-lg bg-neutral-50 text-neutral-700">
                                        {{ $application->applicant->contribution_description }}
                                    </dd>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Support Documents -->
                    @if ($application->status->value !== 'submitted')
                        @if ($documents->count() > 0)
                            <div class="overflow-hidden bg-white border shadow-sm rounded-xl border-neutral-200">
                                <div
                                    class="px-6 py-4 border-b bg-gradient-to-r from-purple-50 to-indigo-50 border-neutral-200">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div
                                                class="flex items-center justify-center w-10 h-10 bg-purple-100 rounded-full">
                                                <svg class="w-5 h-5 text-purple-600" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                    </path>
                                                </svg>
                                            </div>
                                            <h3 class="text-xl font-semibold font-heading text-neutral-800">Support
                                                Documents</h3>
                                        </div>
                                        <span class="px-3 py-1 text-sm bg-white rounded-full text-neutral-500">
                                            {{ $documents->count() }}
                                            {{ Str::plural('document', $documents->count()) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full">
                                        <thead class="bg-neutral-50">
                                            <tr>
                                                <th
                                                    class="px-6 py-4 text-xs font-medium tracking-wider text-left uppercase text-neutral-500">
                                                    Document</th>
                                                <th
                                                    class="px-6 py-4 text-xs font-medium tracking-wider text-left uppercase text-neutral-500">
                                                    Status</th>
                                                <th
                                                    class="px-6 py-4 text-xs font-medium tracking-wider text-left uppercase text-neutral-500">
                                                    Uploaded</th>
                                                <th
                                                    class="px-6 py-4 text-xs font-medium tracking-wider text-left uppercase text-neutral-500">
                                                    Verified</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-neutral-200">
                                            @foreach ($documents as $document)
                                                <tr class="transition-colors duration-150 hover:bg-neutral-50">
                                                    <td class="px-6 py-4">
                                                        <div class="flex items-center space-x-3">
                                                            <div
                                                                class="flex items-center justify-center w-8 h-8 rounded-lg bg-primary-100">
                                                                <svg class="w-4 h-4 text-primary-600" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                                    </path>
                                                                </svg>
                                                            </div>
                                                            <div>
                                                                <button
                                                                    class="font-medium text-primary-600 hover:text-primary-800 hover:underline view-document"
                                                                    data-document-id="{{ $document->id }}"
                                                                    data-file-url="{{ route('admin.applications.document.serve', $document->id) }}"
                                                                    data-file-type="{{ pathinfo($document->stored_filename, PATHINFO_EXTENSION) }}"
                                                                    data-original-filename="{{ $document->original_filename }}">
                                                                    {{ $document->original_filename }}
                                                                </button>
                                                                @if ($document->verification_notes)
                                                                    <div class="mt-1 text-xs text-neutral-500">
                                                                        {{ $document->verification_notes }}</div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        @php
                                                            $docStatusClass = match ($document->status) {
                                                                'pending'
                                                                    => 'bg-secondary-100 text-secondary-800 border-secondary-200',
                                                                'verified'
                                                                    => 'bg-success-100 text-success-800 border-success-200',
                                                                'rejected'
                                                                    => 'bg-danger-100 text-danger-800 border-danger-200',
                                                                default
                                                                    => 'bg-neutral-100 text-neutral-800 border-neutral-200',
                                                            };
                                                        @endphp
                                                        <span id="status-badge-{{ $document->id }}"
                                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium border {{ $docStatusClass }}">
                                                            {{ ucfirst($document->status) }}
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 text-sm text-neutral-500 whitespace-nowrap">
                                                        {{ $document->created_at->format('M d, Y') }}
                                                        <div class="text-xs text-neutral-400">
                                                            {{ $document->created_at->format('H:i') }}</div>
                                                    </td>
                                                    <td class="px-6 py-4 text-sm text-neutral-500 whitespace-nowrap">
                                                        @if ($document->verified_at)
                                                            {{ $document->verified_at->format('M d, Y') }}
                                                            <div class="text-xs text-neutral-400">
                                                                {{ $document->verified_at->format('H:i') }}</div>
                                                        @else
                                                            <span class="text-neutral-400">Not verified</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- Modal for Viewing Document and Updating Status -->
                            <div id="document-modal"
                                class="fixed inset-0 z-50 hidden w-full h-full overflow-y-auto bg-gray-600 bg-opacity-50">
                                <div
                                    class="relative w-3/4 max-w-4xl p-5 mx-auto bg-white border rounded-md shadow-lg top-20">
                                    <div class="flex items-center justify-between mb-4">
                                        <h3 id="modal-title" class="text-lg font-medium text-gray-900"></h3>
                                        <button id="close-modal"
                                            class="text-gray-400 hover:text-gray-600">&times;</button>
                                    </div>
                                    <div id="document-preview" class="mb-4 overflow-auto max-h-96">
                                        <!-- Document preview will be loaded here -->
                                    </div>
                                    <form id="document-status-form" class="space-y-4">
                                        <div>
                                            <label for="status"
                                                class="block text-sm font-medium text-gray-700">Status</label>
                                            <select id="status" name="status"
                                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                <option value="verified">Verified</option>
                                                <option value="rejected">Rejected</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label for="verification_notes"
                                                class="block text-sm font-medium text-gray-700">Verification
                                                Notes</label>
                                            <textarea id="verification_notes" name="verification_notes" rows="4"
                                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                                        </div>
                                        <div class="flex justify-end space-x-2">
                                            <button type="button" id="cancel-button"
                                                class="px-4 py-2 text-gray-800 bg-gray-200 rounded hover:bg-gray-300">Cancel</button>
                                            <button type="submit" id="submit-button"
                                                class="px-4 py-2 text-white bg-blue-500 rounded hover:bg-blue-700">Update
                                                Status</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- JavaScript for Modal and AJAX -->
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const modal = document.getElementById('document-modal');
                                    const documentPreview = document.getElementById('document-preview');
                                    const modalTitle = document.getElementById('modal-title');
                                    const closeModal = document.getElementById('close-modal');
                                    const cancelButton = document.getElementById('cancel-button');
                                    const statusForm = document.getElementById('document-status-form');
                                    let currentDocumentId;


                                    // Open modal and load document
                                    document.querySelectorAll('.view-document').forEach(button => {
                                        button.addEventListener('click', function() {
                                            currentDocumentId = this.getAttribute('data-document-id');
                                            const fileUrl = this.getAttribute('data-file-url');
                                            const fileType = this.getAttribute('data-file-type').toLowerCase();
                                            const originalFilename = this.getAttribute('data-original-filename');

                                            modalTitle.textContent = originalFilename;
                                            documentPreview.innerHTML = '';

                                            if (fileType === 'pdf') {
                                                documentPreview.innerHTML =
                                                    `<embed src="${fileUrl}" type="application/pdf" width="100%" height="400px" />`;
                                            } else if (['jpg', 'jpeg', 'png', 'gif'].includes(fileType)) {
                                                documentPreview.innerHTML =
                                                    `<img src="${fileUrl}" class="h-auto max-w-full" />`;
                                            } else {
                                                documentPreview.innerHTML =
                                                    `<p class="text-red-500">Unsupported file type</p>`;
                                            }

                                            modal.classList.remove('hidden');
                                        });
                                    });

                                    // Close modal
                                    closeModal.addEventListener('click', () => modal.classList.add('hidden'));
                                    cancelButton.addEventListener('click', () => modal.classList.add('hidden'));

                                    // Handle form submission
                                    statusForm.addEventListener('submit', function(e) {
                                        e.preventDefault();
                                        const status = document.getElementById('status').value;
                                        const verificationNotes = document.getElementById('verification_notes').value;

                                        fetch(`/admin/applications/documents/${currentDocumentId}/update-status`, {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                                        .getAttribute('content')
                                                },
                                                body: JSON.stringify({
                                                    status: status,
                                                    verification_notes: verificationNotes
                                                })
                                            })
                                            .then(response => response.json())
                                            .then(data => {
                                                if (data.success) {
                                                    // Update status badge
                                                    const statusBadge = document.getElementById(
                                                        `status-badge-${currentDocumentId}`);
                                                    statusBadge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
                                                    statusBadge.className = `inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                                        status === 'verified' ? 'bg-green-100 text-green-800' :
                                        status === 'rejected' ? 'bg-red-100 text-red-800' :
                                        'bg-gray-100 text-gray-800'
                                    }`;

                                                    modal.classList.add('hidden');
                                                    Swal.fire({
                                                        icon: 'success',
                                                        title: 'Document status updated',
                                                        text: data.message,
                                                        timer: 2000
                                                    });
                                                }
                                            })
                                            .catch(error => {
                                                console.error('Error:', error);
                                                Swal.fire({
                                                    icon: 'error',
                                                    title: 'Error',
                                                    text: 'Failed to update document status'
                                                });
                                            });
                                    });
                                });
                            </script>
                        @endif
                    @else
                        <div class="overflow-hidden bg-white border shadow-sm rounded-xl border-neutral-200">
                            <div
                                class="px-6 py-4 border-b bg-gradient-to-r from-neutral-50 to-gray-50 border-neutral-200">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="flex items-center justify-center w-10 h-10 rounded-full bg-neutral-100">
                                        <svg class="w-5 h-5 text-neutral-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                            </path>
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-semibold font-heading text-neutral-800">Documents Awaiting
                                        Review</h3>
                                </div>
                            </div>
                            <div class="p-6 text-center">
                                <svg class="w-16 h-16 mx-auto mb-4 text-neutral-300" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                <h4 class="mb-2 text-lg font-medium text-neutral-600">Start Review to View Documents
                                </h4>
                                <p class="max-w-md mx-auto text-neutral-500">
                                    This application has been submitted and is awaiting review. Please start the review
                                    process to view the uploaded documents.
                                </p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Right Column - Sidebar -->
                <div class="space-y-6">
                    <!-- Application Timeline -->
                    <div class="overflow-hidden bg-white border shadow-sm rounded-xl border-neutral-200">
                        <div
                            class="px-6 py-4 border-b bg-gradient-to-r from-indigo-50 to-purple-50 border-neutral-200">
                            <div class="flex items-center space-x-3">
                                <div class="flex items-center justify-center w-8 h-8 bg-indigo-100 rounded-full">
                                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold font-heading text-neutral-800">Application Timeline
                                </h3>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <!-- Submitted -->
                                <div class="flex items-start space-x-3">
                                    <div
                                        class="flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-full bg-primary-100">
                                        <svg class="w-4 h-4 text-primary-600" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-neutral-800">Application Submitted</p>
                                        <p class="text-xs text-neutral-500">
                                            {{ $application->submitted_at ? $application->submitted_at->format('M d, Y H:i') : 'Not submitted' }}
                                        </p>
                                    </div>
                                </div>

                                @if ($application->reviewed_at)
                                    <!-- Under Review -->
                                    <div class="flex items-start space-x-3">
                                        <div
                                            class="flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-full bg-secondary-100">
                                            <svg class="w-4 h-4 text-secondary-600" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-neutral-800">Review Started</p>
                                            <p class="text-xs text-neutral-500">
                                                {{ $application->reviewed_at->format('M d, Y H:i') }}</p>
                                            <p class="text-xs text-neutral-600">by {{ $application->reviewer->name }}
                                            </p>
                                        </div>
                                    </div>
                                @endif

                                @if ($application->status->value === 'approved')
                                    <!-- Approved -->
                                    <div class="flex items-start space-x-3">
                                        <div
                                            class="flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-full bg-success-100">
                                            <svg class="w-4 h-4 text-success-600" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-neutral-800">Application Approved</p>
                                            <p class="text-xs text-neutral-500">Recently approved</p>
                                        </div>
                                    </div>
                                @elseif($application->status->value === 'rejected')
                                    <!-- Rejected -->
                                    <div class="flex items-start space-x-3">
                                        <div
                                            class="flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-full bg-danger-100">
                                            <svg class="w-4 h-4 text-danger-600" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-neutral-800">Application Rejected</p>
                                            <p class="text-xs text-neutral-500">Check admin comments</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Payout Mandate -->
                    <div class="overflow-hidden bg-white border shadow-sm rounded-xl border-neutral-200">
                        <div class="px-6 py-4 border-b bg-gradient-to-r from-cyan-50 to-blue-50 border-neutral-200">
                            <div class="flex items-center space-x-3">
                                <div class="flex items-center justify-center w-8 h-8 rounded-full bg-cyan-100">
                                    <svg class="w-4 h-4 text-cyan-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                                        </path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold font-heading text-neutral-800">Payout Mandate</h3>
                            </div>
                        </div>
                        <div class="p-6">
                            @if ($application->payoutMandate)
                                @if ($application->payoutMandate->isSingle())
                                    <div class="flex items-center mb-4 space-x-3">
                                        <div
                                            class="flex items-center justify-center w-10 h-10 rounded-full bg-success-100">
                                            <svg class="w-5 h-5 text-success-600" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-medium text-success-700">Single Mandate</p>
                                            <p class="text-sm text-neutral-600">Direct authorization setup</p>
                                        </div>
                                    </div>
                                @elseif ($application->payoutMandate->isDual())
                                    <div class="space-y-4">
                                        <div class="flex items-center space-x-3">
                                            <div
                                                class="flex items-center justify-center w-10 h-10 rounded-full bg-primary-100">
                                                <svg class="w-5 h-5 text-primary-600" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path
                                                        d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z">
                                                    </path>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="font-medium text-primary-700">Dual Mandate</p>
                                                <p class="text-sm text-neutral-600">Requires checker approval</p>
                                            </div>
                                        </div>

                                        @if ($application->payoutMandate->checker)
                                            <div class="p-4 rounded-lg bg-neutral-50">
                                                <h4 class="mb-2 font-medium text-neutral-800">Checker Details</h4>
                                                <div class="space-y-1 text-sm">
                                                    <p><span class="text-neutral-500">Name:</span>
                                                        {{ $application->payoutMandate->checker->name }}</p>
                                                    <p><span class="text-neutral-500">Email:</span>
                                                        {{ $application->payoutMandate->checker->email }}</p>
                                                </div>
                                            </div>
                                        @else
                                            @php
                                                $invitation = $application->payoutMandate
                                                    ->invitations()
                                                    ->latest()
                                                    ->first();
                                            @endphp
                                            <div class="p-4 rounded-lg bg-secondary-50">
                                                <h4 class="mb-2 font-medium text-secondary-800">Checker Status</h4>
                                                <p class="mb-3 text-sm text-secondary-700">
                                                    @if ($invitation && !$invitation->isExpired())
                                                        Checker invitation sent - awaiting account creation
                                                    @elseif ($invitation && $invitation->isExpired())
                                                        Invitation link has expired
                                                    @else
                                                        No checker assigned yet
                                                    @endif
                                                </p>

                                                @if (!$application->payoutMandate->checker && (($invitation && $invitation->isExpired()) || !$invitation))
                                                    <form
                                                        action="{{ route('admin.invitations.create', $application->payoutMandate) }}"
                                                        method="POST">
                                                        @csrf
                                                        <button type="submit"
                                                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors duration-200 rounded-lg bg-primary-600 hover:bg-primary-700">
                                                            <svg class="w-4 h-4 mr-2" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                                                                </path>
                                                            </svg>
                                                            {{ $invitation && $invitation->isExpired() ? 'Resend Invitation' : 'Create Invitation' }}
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            @else
                                <div class="py-4 text-center">
                                    <svg class="w-12 h-12 mx-auto mb-2 text-neutral-300" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                                        </path>
                                    </svg>
                                    <p class="text-sm text-neutral-500">No payout mandate configured</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Admin Comments -->
                    @if ($application->admin_comments)
                        <div class="overflow-hidden bg-white border shadow-sm rounded-xl border-neutral-200">
                            <div
                                class="px-6 py-4 border-b bg-gradient-to-r from-orange-50 to-red-50 border-neutral-200">
                                <div class="flex items-center space-x-3">
                                    <div class="flex items-center justify-center w-8 h-8 bg-orange-100 rounded-full">
                                        <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-1l-4 4z">
                                            </path>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-semibold font-heading text-neutral-800">Admin Comments</h3>
                                </div>
                            </div>
                            <div class="p-6">
                                <div class="p-4 border border-orange-200 rounded-lg bg-orange-50">
                                    <p class="text-sm leading-relaxed text-orange-900">
                                        {{ $application->admin_comments }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Admin Action Form Section -->
            @if ($showAdminForm)
                <div class="overflow-hidden bg-white border shadow-sm rounded-xl border-neutral-200">
                    <div class="px-6 py-4 border-b bg-gradient-to-r from-red-50 to-orange-50 border-neutral-200">
                        <div class="flex items-center space-x-3">
                            <div class="flex items-center justify-center w-10 h-10 bg-red-100 rounded-full">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold font-heading text-neutral-800">Admin Actions Required
                                </h3>
                                <p class="mt-1 text-sm text-neutral-600">
                                    @php
                                        $reasons = [];
                                        $rejectedDocs = $documents->where('status', 'rejected');
                                        if ($rejectedDocs->count() > 0) {
                                            $reasons[] = $rejectedDocs->count() . ' support document(s) rejected';
                                        }

                                        if ($application->applicant_type === 'App\\Models\\Individual') {
                                            $hasVerifiedKyc = $application->applicant
                                                ->kycVerifications()
                                                ->where('application_id', $application->id)
                                                ->where('status', 'verified')
                                                ->exists();

                                            if (!$hasVerifiedKyc) {
                                                $latestKyc = $application->applicant
                                                    ->kycVerifications()
                                                    ->where('application_id', $application->id)
                                                    ->latest()
                                                    ->first();

                                                if ($latestKyc && $latestKyc->status === 'rejected') {
                                                    $reasons[] = 'KYC verification rejected';
                                                } else {
                                                    $reasons[] = 'KYC verification pending';
                                                }
                                            }
                                        }

                                        if (
                                            $application->applicant_type === 'App\\Models\\Company' &&
                                            $application->status->value === 'under_review'
                                        ) {
                                            $reasons[] = 'Company application under review';
                                        }

                                        if (!in_array($application->status->value, ['submitted', 'approved'])) {
                                            $reasons[] = 'Application status is ' . $application->status->value;
                                        }
                                    @endphp
                                    Action required: {{ implode(', ', $reasons) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <form action="{{ route('admin.applications.update-status', $application) }}" method="POST"
                            class="space-y-6">
                            @csrf
                            @method('PATCH')

                            <!-- Status Selection -->
                            <div>
                                <label for="status" class="block mb-3 text-sm font-medium text-neutral-700">
                                    Update Application Status
                                </label>
                                <select id="status" name="status" required
                                    class="block w-full text-sm transition-colors duration-200 rounded-lg shadow-sm border-neutral-300 focus:border-primary-500 focus:ring-primary-500">
                                    <option value="">Select new status...</option>
                                    <option value="additional_info_required">Request Additional Information</option>
                                    <option value="rejected">Reject Application</option>
                                </select>
                                @error('status')
                                    <p class="mt-2 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Admin Comments -->
                            <div>
                                <label for="admin_comments" class="block mb-3 text-sm font-medium text-neutral-700">
                                    Admin Comments
                                </label>
                                <textarea id="admin_comments" name="admin_comments" rows="4"
                                    placeholder="Add comments for the applicant explaining the reason for this status change..."
                                    class="block w-full text-sm transition-colors duration-200 rounded-lg shadow-sm border-neutral-300 focus:border-primary-500 focus:ring-primary-500">{{ old('admin_comments', $application->admin_comments) }}</textarea>
                                @error('admin_comments')
                                    <p class="mt-2 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-2 text-sm text-neutral-500">
                                    These comments will be visible to the applicant.
                                </p>
                            </div>

                            <!-- Current Issues Summary -->
                            <div
                                class="p-6 border rounded-lg bg-gradient-to-r from-neutral-50 to-gray-50 border-neutral-200">
                                <h4 class="flex items-center mb-4 text-sm font-medium text-neutral-800">
                                    <svg class="w-4 h-4 mr-2 text-neutral-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                        </path>
                                    </svg>
                                    Current Issues Summary
                                </h4>
                                <ul class="space-y-3">
                                    @if ($documents->where('status', 'rejected')->count() > 0)
                                        <li class="flex items-center space-x-3">
                                            <div
                                                class="flex items-center justify-center flex-shrink-0 w-6 h-6 rounded-full bg-danger-100">
                                                <svg class="w-3 h-3 text-danger-600" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                            </div>
                                            <span class="text-sm text-danger-700">
                                                {{ $documents->where('status', 'rejected')->count() }} rejected support
                                                document(s)
                                            </span>
                                        </li>
                                    @endif

                                    @if ($application->applicant_type === 'App\\Models\\Individual')
                                        @php
                                            $hasVerifiedKyc = $application->applicant
                                                ->kycVerifications()
                                                ->where('application_id', $application->id)
                                                ->where('status', 'verified')
                                                ->exists();
                                        @endphp
                                        @if (!$hasVerifiedKyc)
                                            @php
                                                $latestKyc = $application->applicant
                                                    ->kycVerifications()
                                                    ->where('application_id', $application->id)
                                                    ->latest()
                                                    ->first();
                                                $kycStatus = $latestKyc ? $latestKyc->status : 'pending';
                                                $iconBgColor =
                                                    $kycStatus === 'rejected' ? 'bg-danger-100' : 'bg-secondary-100';
                                                $iconColor =
                                                    $kycStatus === 'rejected'
                                                        ? 'text-danger-600'
                                                        : 'text-secondary-600';
                                                $textColor =
                                                    $kycStatus === 'rejected'
                                                        ? 'text-danger-700'
                                                        : 'text-secondary-700';
                                                $statusText =
                                                    $kycStatus === 'rejected'
                                                        ? 'KYC verification rejected'
                                                        : 'KYC verification pending';
                                            @endphp
                                            <li class="flex items-center space-x-3">
                                                <div
                                                    class="w-6 h-6 {{ $iconBgColor }} rounded-full flex items-center justify-center flex-shrink-0">
                                                    <svg class="w-3 h-3 {{ $iconColor }}" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        @if ($kycStatus === 'rejected')
                                                            <path fill-rule="evenodd"
                                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                                clip-rule="evenodd"></path>
                                                        @else
                                                            <path fill-rule="evenodd"
                                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                                clip-rule="evenodd"></path>
                                                        @endif
                                                    </svg>
                                                </div>
                                                <span class="text-sm {{ $textColor }}">{{ $statusText }}</span>
                                            </li>
                                        @endif
                                    @endif

                                    @if ($application->applicant_type === 'App\\Models\\Company' && $application->status->value === 'under_review')
                                        <li class="flex items-center space-x-3">
                                            <div
                                                class="flex items-center justify-center flex-shrink-0 w-6 h-6 rounded-full bg-secondary-100">
                                                <svg class="w-3 h-3 text-secondary-600" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                            </div>
                                            <span class="text-sm text-secondary-700">Company application pending
                                                review</span>
                                        </li>
                                    @endif
                                </ul>
                            </div>

                            <!-- Action Buttons -->
                            <div
                                class="flex flex-col gap-3 pt-6 border-t sm:flex-row sm:justify-end border-neutral-200">
                                <button type="button"
                                    onclick="document.getElementById('status').value = ''; document.getElementById('admin_comments').value = '{{ addslashes($application->admin_comments) }}';"
                                    class="inline-flex items-center justify-center px-6 py-3 font-medium transition-all duration-200 bg-white border rounded-lg border-neutral-300 text-neutral-700 hover:bg-neutral-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                        </path>
                                    </svg>
                                    Reset Form
                                </button>
                                <button type="submit"
                                    class="inline-flex items-center justify-center px-6 py-3 font-medium text-white transition-all duration-200 border border-transparent rounded-lg shadow-sm bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                    </svg>
                                    Update Application
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Start Review button handler
            const reviewButton = document.getElementById('review-button');
            if (reviewButton) {
                reviewButton.addEventListener('click', function() {
                    const applicationId = this.getAttribute('data-application-id');

                    fetch('/admin/applications/' + applicationId + '/start-review', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content')
                            },
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const statusBadge = document.getElementById('status-badge');
                                statusBadge.textContent = 'Under Review';
                                statusBadge.className =
                                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800';
                                reviewButton.remove();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Application under review',
                                    text: data.message,
                                    timer: 2000
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to update application status'
                            });
                        });
                });
            }

            // Approve button handler
            const approveButton = document.getElementById('approve-button');
            if (approveButton) {
                approveButton.addEventListener('click', function() {
                    const applicationId = this.getAttribute('data-application-id');

                    // Show confirmation dialog
                    Swal.fire({
                        title: 'Approve Application?',
                        text: 'This will approve the application and send confirmation notifications to the applicant.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#10b981',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes, Approve',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Disable button to prevent double-clicks
                            approveButton.disabled = true;
                            approveButton.textContent = 'Processing...';

                            fetch('/admin/applications/' + applicationId + '/approve', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector(
                                                'meta[name="csrf-token"]')
                                            .getAttribute('content')
                                    },
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        // Update status badge
                                        const statusBadge = document.getElementById(
                                            'status-badge');
                                        if (statusBadge) {
                                            statusBadge.textContent = 'Approved';
                                            statusBadge.className =
                                                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800';
                                        }

                                        // Remove approve button
                                        approveButton.remove();

                                        // Remove verification status warning if present
                                        const warningDiv = document.querySelector(
                                            '.bg-yellow-50');
                                        if (warningDiv) {
                                            warningDiv.remove();
                                        }

                                        // Show success message
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Application Approved!',
                                            text: data.message,
                                            timer: 3000,
                                            showConfirmButton: false
                                        });
                                    } else {
                                        // Re-enable button on error
                                        approveButton.disabled = false;
                                        approveButton.textContent = 'Approve Application';

                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Approval Failed',
                                            text: data.message ||
                                                'Unable to approve application'
                                        });
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);

                                    // Re-enable button on error
                                    approveButton.disabled = false;
                                    approveButton.textContent = 'Approve Application';

                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Network Error',
                                        text: 'Failed to approve application. Please check your connection and try again.'
                                    });
                                });
                        }
                    });
                });
            }

        });
    </script>
</x-app-layout>
