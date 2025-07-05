<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Application Details - {{ $application->application_number }}
            </h2>
            <a href="{{ route('admin.applications.index') }}"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-gray-600 rounded hover:bg-gray-700">
                ← Back to Applications
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">

            <!-- Application Status Card -->
            <div class="mb-6 overflow-hidden bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Application Status</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            @php
                                $statusClass = match ($application->status->value) {
                                    'submitted' => 'bg-blue-100 text-blue-800',
                                    'under_review' => 'bg-yellow-100 text-yellow-800',
                                    'additional_info_required' => 'bg-orange-100 text-orange-800',
                                    'approved' => 'bg-green-100 text-green-800',
                                    'rejected' => 'bg-red-100 text-red-800',
                                    default => 'bg-gray-100 text-gray-800',
                                };

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

                            <dd class="mt-1">
                                <span id="status-badge"
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                    {{ ucwords(str_replace('_', ' ', $application->status->value)) }}
                                </span>
                                @if ($application->status->value === 'submitted')
                                    <button id="review-button"
                                        class="px-2 py-1 ml-2 text-xs font-bold text-white bg-blue-500 rounded hover:bg-blue-700"
                                        data-application-id="{{ $application->id }}">
                                        Start Review
                                    </button>
                                @endif
                                @if ($application->status->value !== 'approved' && $canApprove)
                                    <button id="approve-button"
                                        class="px-2 py-1 ml-2 text-xs font-bold text-white bg-green-500 rounded hover:bg-green-700"
                                        data-application-id="{{ $application->id }}">
                                        Approve Application
                                    </button>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Submitted</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $application->submitted_at ? $application->submitted_at->format('M d, Y H:i') : 'Not submitted' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Reviewed By</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $application->reviewer ? $application->reviewer->name : 'Not reviewed' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Reviewed At</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $application->reviewed_at ? $application->reviewed_at->format('M d, Y H:i') : 'Not reviewed' }}
                            </dd>
                        </div>
                    </div>

                    @if ($application->admin_comments)
                        <div class="mt-4">
                            <dt class="text-sm font-medium text-gray-500">Admin Comments</dt>
                            <dd class="p-3 mt-1 text-sm text-gray-900 rounded bg-gray-50">
                                {{ $application->admin_comments }}
                            </dd>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Display verification status if not all requirements are met --}}
            @if (!$canApprove && $application->status->value !== 'approved')
                <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">
                                Verification Required
                            </h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>The following requirements must be met before approval:</p>
                                <ul class="list-disc list-inside mt-1">
                                    @foreach ($verificationStatus as $status)
                                        <li>{{ $status }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Applicant Information -->
            <div class="mb-6 overflow-hidden bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">
                        {{ $application->applicant_type === 'App\\Models\\Individual' ? 'Individual' : 'Company' }}
                        Information
                    </h3>
                </div>
                <div class="px-6 py-4">
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
            <div class="mb-6 overflow-hidden bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Contribution Details</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Contribution Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $application->applicant->contribution_name }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Contribution Reason</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $application->applicant->contributionReason->name ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Target Amount</dt>
                            <dd class="mt-1 text-sm text-gray-900">KES
                                {{ number_format($application->applicant->target_amount, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Target Date</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $application->applicant->target_date ? $application->applicant->target_date->format('M d, Y') : 'Not set' }}
                            </dd>
                        </div>
                    </div>

                    @if ($application->applicant->contribution_description)
                        <div class="mt-6">
                            <dt class="text-sm font-medium text-gray-500">Description</dt>
                            <dd class="p-3 mt-1 text-sm text-gray-900 rounded bg-gray-50">
                                {{ $application->applicant->contribution_description }}
                            </dd>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Payout Mandate -->
            <div class="mb-6 overflow-hidden bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Payout Mandate Details</h3>
                </div>
                <div class="px-6 py-4">
                    @if ($application->payoutMandate)
                        <div>
                            @if ($application->payoutMandate->isSingle())
                                <p class="text-sm text-gray-700">
                                    This application uses a <strong class="text-green-700">Single Mandate</strong>
                                    setup.
                                </p>
                            @elseif ($application->payoutMandate->isDual())
                                <p class="mb-4 text-sm text-gray-700">
                                    This application uses a <strong class="text-blue-700">Dual Mandate</strong> setup.
                                    Below are the checker details:
                                </p>

                                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                    @if ($application->payoutMandate->checker && $application->payoutMandate->checker)
                                        <div>
                                            <span class="block text-sm font-medium text-gray-700">Checker Name:</span>
                                            <div class="mt-1 text-gray-900">
                                                {{ $application->payoutMandate->checker->name ?? 'N/A' }}
                                            </div>
                                        </div>
                                        <div>
                                            <span class="block text-sm font-medium text-gray-700">Checker Email:</span>
                                            <div class="mt-1 text-gray-900">
                                                {{ $application->payoutMandate->checker->email ?? 'N/A' }}
                                            </div>
                                        </div>
                                    @else
                                        @php
                                            $invitation = $application->payoutMandate->invitations()->latest()->first();
                                        @endphp
                                        <div class="text-gray-900">
                                            @if ($invitation && !$invitation->isExpired())
                                                Checker is yet to complete account creation and sign up
                                            @elseif ($invitation && $invitation->isExpired())
                                                Invitation link expired
                                            @else
                                                No checker assigned
                                            @endif
                                        </div>
                                    @endif

                                </div>

                                @if (!$application->payoutMandate->checker && (($invitation && $invitation->isExpired()) || !$invitation))
                                    <form action="{{ route('admin.invitations.create', $application->payoutMandate) }}"
                                        method="POST" class="mt-4">
                                        @csrf
                                        <button type="submit"
                                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                                            {{ $invitation && $invitation->isExpired() ? 'Resend Invitation Link' : 'Create Invitation Link' }}
                                        </button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Support Documents -->
            @if ($application->status->value !== 'submitted')
                @if ($documents->count() > 0)
                    <div class="mb-6 overflow-hidden bg-white rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Support Documents</h3>
                        </div>
                        <div class="px-6 py-4">
                            <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                                <table class="min-w-full divide-y divide-gray-300">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                                Document</th>
                                            <th
                                                class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                                Status</th>
                                            <th
                                                class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                                Uploaded</th>
                                            <th
                                                class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                                Verified At</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($documents as $document)
                                            <tr>
                                                <td class="px-6 py-4 text-sm text-gray-900">
                                                    <button
                                                        class="font-medium text-blue-600 hover:underline view-document"
                                                        data-document-id="{{ $document->id }}"
                                                        data-file-url="{{ route('admin.applications.document.serve', $document->id) }}"
                                                        data-file-type="{{ pathinfo($document->stored_filename, PATHINFO_EXTENSION) }}"
                                                        data-original-filename="{{ $document->original_filename }}">
                                                        {{ $document->original_filename }}
                                                    </button>
                                                    @if ($document->verification_notes)
                                                        <div class="mt-1 text-xs text-gray-500">
                                                            {{ $document->verification_notes }}</div>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @php
                                                        $docStatusClass = match ($document->status) {
                                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                                            'verified' => 'bg-green-100 text-green-800',
                                                            'rejected' => 'bg-red-100 text-red-800',
                                                            default => 'bg-gray-100 text-gray-800',
                                                        };
                                                    @endphp
                                                    <span id="status-badge-{{ $document->id }}"
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $docStatusClass }}">
                                                        {{ ucfirst($document->status) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                    {{ $document->created_at->format('M d, Y H:i') }}
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                    @if ($document->verified_at)
                                                        {{ $document->verified_at->format('M d, Y H:i') }}
                                                    @else
                                                        Not verified
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Modal for Viewing Document and Updating Status -->
                    <div id="document-modal"
                        class="fixed inset-0 z-50 hidden w-full h-full overflow-y-auto bg-gray-600 bg-opacity-50">
                        <div class="relative w-3/4 max-w-4xl p-5 mx-auto bg-white border rounded-md shadow-lg top-20">
                            <div class="flex items-center justify-between mb-4">
                                <h3 id="modal-title" class="text-lg font-medium text-gray-900"></h3>
                                <button id="close-modal" class="text-gray-400 hover:text-gray-600">&times;</button>
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
                                        class="block text-sm font-medium text-gray-700">Verification Notes</label>
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
                <div class="mb-6 overflow-hidden bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Start Review To View Documents</h3>
                        <p class="text-sm text-gray-500">
                            This application has been submitted and is awaiting review. Please start the review process
                            to view the documents.
                        </p>
                    </div>
                </div>
            @endif

            {{-- Admin Action Form Section --}}
            @if ($showAdminForm)
                <div class="mb-8">
                    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Admin Actions Required</h3>
                            <p class="mt-1 text-sm text-gray-600">
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

                        <div class="px-6 py-4">
                            <form action="{{ route('admin.applications.update-status', $application) }}"
                                method="POST">
                                @csrf
                                @method('PATCH')

                                <div class="space-y-4">
                                    {{-- Status Selection --}}
                                    <div>
                                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                            Update Application Status
                                        </label>
                                        <select id="status" name="status" required
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            <option value="">Select status...</option>
                                            <option value="additional_info_required">Request Additional Information
                                            </option>
                                            <option value="rejected">Reject Application</option>
                                        </select>
                                        @error('status')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Admin Comments --}}
                                    <div>
                                        <label for="admin_comments"
                                            class="block text-sm font-medium text-gray-700 mb-2">
                                            Admin Comments
                                        </label>
                                        <textarea id="admin_comments" name="admin_comments" rows="4"
                                            placeholder="Add comments for the applicant explaining the reason for this status change..."
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('admin_comments', $application->admin_comments) }}</textarea>
                                        @error('admin_comments')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                        <p class="mt-1 text-sm text-gray-500">
                                            These comments will be visible to the applicant.
                                        </p>
                                    </div>

                                    {{-- Current Issues Summary --}}
                                    <div class="bg-gray-50 rounded-md p-4">
                                        <h4 class="text-sm font-medium text-gray-900 mb-2">Current Issues Summary:</h4>
                                        <ul class="text-sm text-gray-600 space-y-1">
                                            @if ($documents->where('status', 'rejected')->count() > 0)
                                                <li class="flex items-center">
                                                    <svg class="w-4 h-4 text-red-500 mr-2" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                            clip-rule="evenodd"></path>
                                                    </svg>
                                                    {{ $documents->where('status', 'rejected')->count() }} rejected
                                                    support document(s)
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
                                                        $iconColor =
                                                            $kycStatus === 'rejected'
                                                                ? 'text-red-500'
                                                                : 'text-yellow-500';
                                                        $statusText =
                                                            $kycStatus === 'rejected'
                                                                ? 'KYC verification rejected'
                                                                : 'KYC verification pending';
                                                    @endphp
                                                    <li class="flex items-center">
                                                        <svg class="w-4 h-4 {{ $iconColor }} mr-2"
                                                            fill="currentColor" viewBox="0 0 20 20">
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
                                                        {{ $statusText }}
                                                    </li>
                                                @endif
                                            @endif

                                            @if ($application->applicant_type === 'App\\Models\\Company' && $application->status->value === 'under_review')
                                                <li class="flex items-center">
                                                    <svg class="w-4 h-4 text-yellow-500 mr-2" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                            clip-rule="evenodd"></path>
                                                    </svg>
                                                    Company application pending review
                                                </li>
                                            @endif
                                        </ul>
                                    </div>

                                    {{-- Action Buttons --}}
                                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                                        <button type="button"
                                            onclick="document.getElementById('status').value = ''; document.getElementById('admin_comments').value = '{{ $application->admin_comments }}';"
                                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            Reset
                                        </button>
                                        <button type="submit"
                                            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            Update Application
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
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
