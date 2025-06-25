<div class="grid grid-cols-1 gap-6 md:grid-cols-2">
    <div>
        <dt class="text-sm font-medium text-gray-500">Company Name</dt>
        <dd class="mt-1 text-sm text-gray-900">{{ $company->company_name }}</dd>
    </div>
    <div>
        <dt class="text-sm font-medium text-gray-500">Registration Certificate</dt>
        <dd class="mt-1 text-sm text-gray-900">
            @if ($company->registration_certificate)
                <span class="text-green-600">Uploaded</span>
                <button class="ml-2 text-blue-600 hover:underline view-company-document"
                    data-company-id="{{ $company->id }}" data-field="registration_certificate"
                    data-file-url="{{ route('admin.applications.company.document-serve', [$company->id, 'registration_certificate']) }}"
                    data-file-type="{{ pathinfo($company->registration_certificate, PATHINFO_EXTENSION) }}"
                    data-document-name="Registration Certificate">
                    View
                </button>
            @else
                <span class="text-red-600">Not uploaded</span>
            @endif
        </dd>
    </div>

    <div>
        <dt class="text-sm font-medium text-gray-500">PIN Number</dt>
        <dd class="mt-1 text-sm text-gray-900">{{ $company->pin_number }}</dd>
    </div>
    <div>
        <dt class="text-sm font-medium text-gray-500">CR12</dt>
        <dd class="mt-1 text-sm text-gray-900">
            @if ($company->cr12)
                <span class="text-green-600">Uploaded</span>
                <button class="ml-2 text-blue-600 hover:underline view-company-document"
                    data-company-id="{{ $company->id }}" data-field="cr12"
                    data-file-url="{{ route('admin.applications.company.document-serve', [$company->id, 'cr12']) }}"
                    data-file-type="{{ pathinfo($company->cr12, PATHINFO_EXTENSION) }}"
                    data-document-name="CR12 (Directors List)">
                    View
                </button>
            @else
                <span class="text-red-600">Not uploaded</span>
            @endif
            @if ($company->cr12_date)
                <div class="text-xs text-gray-500">Date: {{ $company->cr12_date->format('M d, Y') }}</div>
            @endif
        </dd>
    </div>
    <div class="md:col-span-2">
        <dt class="text-sm font-medium text-gray-500">Address</dt>
        <dd class="mt-1 text-sm text-gray-900">
            {{ $company->address }}
            @if ($company->city || $company->county || $company->postal_code)
                <div class="mt-1 text-xs text-gray-500">
                    {{ collect([$company->city, $company->county, $company->postal_code, $company->country])->filter()->implode(', ') }}
                </div>
            @endif
        </dd>
    </div>
    <div>
        <dt class="text-sm font-medium text-gray-500">Bank</dt>
        <dd class="mt-1 text-sm text-gray-900">{{ $company->bank->display_name ?? 'N/A' }}</dd>
    </div>
    <div>
        <dt class="text-sm font-medium text-gray-500">Bank Account</dt>
        <dd class="mt-1 text-sm text-gray-900">
            {{ $company->bank_account_number }}
            <div class="text-xs text-gray-500">
                Proof:
                @if ($company->bank_account_proof)
                    <span class="text-green-600">Uploaded</span>
                    <button class="ml-2 text-blue-600 hover:underline view-company-document"
                        data-company-id="{{ $company->id }}" data-field="bank_account_proof"
                        data-file-url="{{ route('admin.applications.company.document-serve', [$company->id, 'bank_account_proof']) }}"
                        data-file-type="{{ pathinfo($company->bank_account_proof, PATHINFO_EXTENSION) }}"
                        data-document-name="Bank Account Proof">
                        View
                    </button>
                @else
                    <span class="text-red-600">Not uploaded</span>
                @endif
            </div>
        </dd>
    </div>
    <div>
        <dt class="text-sm font-medium text-gray-500">Settlement</dt>
        <dd class="mt-1 text-sm text-gray-900">{{ $company->settlement ?? 'Not specified' }}</dd>
    </div>
    <div>
        <dt class="text-sm font-medium text-gray-500">Amount Raised</dt>
        <dd class="mt-1 text-sm text-gray-900">
            KES {{ number_format($company->amount_raised ?? 0, 2) }}
            @if ($company->fees_charged)
                <div class="text-xs text-gray-500">Fees: KES {{ number_format($company->fees_charged, 2) }}</div>
            @endif
        </dd>
    </div>
</div>

<!-- Modal for Viewing Company Documents -->
<div id="company-document-modal"
    class="fixed inset-0 z-50 hidden w-full h-full overflow-y-auto bg-gray-600 bg-opacity-50">
    <div class="relative w-3/4 max-w-4xl p-5 mx-auto bg-white border rounded-md shadow-lg top-20">
        <div class="flex items-center justify-between mb-4">
            <h3 id="company-modal-title" class="text-lg font-medium text-gray-900"></h3>
            <button id="close-company-modal" class="text-2xl text-gray-400 hover:text-gray-600">&times;</button>
        </div>
        <div id="company-document-preview" class="mb-4 overflow-auto max-h-96">
            <!-- Document preview will be loaded here -->
        </div>
        <div class="flex justify-end">
            <button type="button" id="company-cancel-button"
                class="px-4 py-2 text-gray-800 bg-gray-200 rounded hover:bg-gray-300">
                Close
            </button>
        </div>
    </div>
</div>

@if ($company->contact_persons && count($company->contact_persons) > 0)
    <div class="mt-6">
        <dt class="text-sm font-medium text-gray-500">Contact Persons</dt>
        <dd class="mt-1">
            <div class="p-3 rounded bg-gray-50">
                @foreach ($company->contact_persons as $contact)
                    <div class="mb-2 last:mb-0">
                        <div class="text-sm font-medium text-gray-900">{{ $contact['name'] ?? 'N/A' }}</div>
                        @if (isset($contact['phone']))
                            <div class="text-xs text-gray-500">Phone: {{ $contact['phone'] }}</div>
                        @endif
                        @if (isset($contact['email']))
                            <div class="text-xs text-gray-500">Email: {{ $contact['email'] }}</div>
                        @endif
                        @if (isset($contact['position']))
                            <div class="text-xs text-gray-500">Position: {{ $contact['position'] }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        </dd>
    </div>
@endif

@if ($company->additional_info)
    <div class="mt-6">
        <dt class="text-sm font-medium text-gray-500">Additional Information</dt>
        <dd class="p-3 mt-1 text-sm text-gray-900 rounded bg-gray-50">
            @if (is_array($company->additional_info))
                @foreach ($company->additional_info as $key => $value)
                    <div class="mb-2">
                        <strong>{{ ucwords(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}
                    </div>
                @endforeach
            @else
                {{ $company->additional_info }}
            @endif
        </dd>
    </div>
@endif

<!-- JavaScript for Company Document Modal -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('company-document-modal');
        const documentPreview = document.getElementById('company-document-preview');
        const modalTitle = document.getElementById('company-modal-title');
        const closeModal = document.getElementById('close-company-modal');
        const cancelButton = document.getElementById('company-cancel-button');

        // Open modal and load document
        document.querySelectorAll('.view-company-document').forEach(button => {
            button.addEventListener('click', function() {
                const fileUrl = this.getAttribute('data-file-url');
                const fileType = this.getAttribute('data-file-type').toLowerCase();
                const documentName = this.getAttribute('data-document-name');

                modalTitle.textContent = documentName;
                documentPreview.innerHTML = '<div class="py-4 text-center">Loading...</div>';

                // Show modal
                modal.classList.remove('hidden');

                // Load document based on file type
                if (fileType === 'pdf') {
                    documentPreview.innerHTML =
                        `<embed src="${fileUrl}" type="application/pdf" width="100%" height="500px" />`;
                } else if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(fileType)) {
                    documentPreview.innerHTML =
                        `<img src="${fileUrl}" class="h-auto max-w-full mx-auto" alt="${documentName}" />`;
                } else {
                    documentPreview.innerHTML =
                        `<div class="py-8 text-center">
                            <p class="mb-4 text-gray-500">Preview not available for this file type</p>
                            <a href="${fileUrl}" target="_blank" 
                               class="inline-flex items-center px-4 py-2 text-white bg-blue-500 rounded hover:bg-blue-600">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                Download File
                            </a>
                        </div>`;
                }
            });
        });

        // Close modal handlers
        closeModal.addEventListener('click', () => modal.classList.add('hidden'));
        cancelButton.addEventListener('click', () => modal.classList.add('hidden'));

        // Close modal when clicking outside
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.classList.add('hidden');
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                modal.classList.add('hidden');
            }
        });
    });
</script>
