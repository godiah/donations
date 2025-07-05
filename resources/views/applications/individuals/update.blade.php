<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Update Application Details - #{{ $application->application_number }}
            </h2>
            <a href="{{ route('individual.applications.show', $application->application_number) }}"
                class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 border border-transparent rounded-md text-sm font-medium text-white transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Details
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
            <!-- Application Status (Read-only) -->
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Application Status</h3>
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                            {{ ucfirst(str_replace('_', ' ', $application->status->value)) }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 gap-4 text-sm md:grid-cols-2">
                        <div>
                            <span class="font-medium text-gray-700">Application Number:</span>
                            <span class="text-gray-900">{{ $application->application_number }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Submitted:</span>
                            <span class="text-gray-900">{{ $application->submitted_at->format('M j, Y g:i A') }}</span>
                        </div>
                    </div>

                    @if ($application->admin_comments)
                        <div class="p-4 mt-4 rounded-lg bg-orange-50 border border-orange-200">
                            <h4 class="mb-2 font-medium text-orange-800">Additional Information Required:</h4>
                            <p class="text-sm text-orange-700">{{ $application->admin_comments }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Contribution Details (Read-only) -->
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="mb-4 text-lg font-semibold text-gray-800">Contribution Details</h3>

                    <div class="space-y-4">
                        <div>
                            <h4 class="mb-2 font-medium text-gray-700">{{ $application->applicant->contribution_name }}
                            </h4>
                            @if ($application->applicant->contribution_description)
                                <p class="text-sm text-gray-600">{{ $application->applicant->contribution_description }}
                                </p>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 gap-4 text-sm md:grid-cols-2 lg:grid-cols-3">
                            <div>
                                <span class="font-medium text-gray-700">Target Amount:</span>
                                <div class="text-lg font-semibold text-green-600">KSh
                                    {{ number_format($application->applicant->target_amount, 2) }}</div>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Amount Raised:</span>
                                <div class="text-lg font-semibold text-blue-600">KSh
                                    {{ number_format($application->applicant->amount_raised ?? 0, 2) }}</div>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Target Date:</span>
                                <div class="text-gray-900">{{ $application->applicant->target_date->format('M j, Y') }}
                                </div>
                            </div>
                        </div>

                        <div>
                            <span class="font-medium text-gray-700">Contribution Reason:</span>
                            <span
                                class="text-gray-900">{{ $application->applicant->contributionReason->name ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Update Form -->
            <form id="updateForm"
                action="{{ route('individual.applications.update.store', $application->application_number) }}"
                method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <!-- Personal Information (Editable) -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="mb-4 text-lg font-semibold text-gray-800">Personal Information</h3>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div class="space-y-4">
                                <!-- First Name -->
                                <div>
                                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First
                                        Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="first_name" id="first_name"
                                        value="{{ old('first_name', $application->applicant->first_name) }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                        required>
                                    <div class="text-red-500 text-xs mt-1 hidden" data-error="first_name"></div>
                                </div>

                                <!-- Middle Name -->
                                <div>
                                    <label for="middle_name" class="block text-sm font-medium text-gray-700 mb-1">Middle
                                        Name</label>
                                    <input type="text" name="middle_name" id="middle_name"
                                        value="{{ old('middle_name', $application->applicant->middle_name) }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <div class="text-red-500 text-xs mt-1 hidden" data-error="middle_name"></div>
                                </div>

                                <!-- Last Name -->
                                <div>
                                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last
                                        Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="last_name" id="last_name"
                                        value="{{ old('last_name', $application->applicant->last_name) }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                        required>
                                    <div class="text-red-500 text-xs mt-1 hidden" data-error="last_name"></div>
                                </div>

                                <!-- Email -->
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email
                                        Address</label>
                                    <input type="email" name="email" id="email"
                                        value="{{ old('email', $application->applicant->email) }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <div class="text-red-500 text-xs mt-1 hidden" data-error="email"></div>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <!-- Phone -->
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone
                                        Number <span class="text-red-500">*</span></label>
                                    <input type="tel" name="phone" id="phone"
                                        value="{{ old('phone', $application->applicant->phone) }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="+254712345678" required>
                                    <div class="text-red-500 text-xs mt-1 hidden" data-error="phone"></div>
                                </div>

                                <!-- ID Type -->
                                <div>
                                    <label for="id_type_id" class="block text-sm font-medium text-gray-700 mb-1">ID
                                        Type <span class="text-red-500">*</span></label>
                                    <select name="id_type_id" id="id_type_id"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                        required>
                                        <option value="">Select ID Type</option>
                                        @foreach ($idTypes as $idType)
                                            <option value="{{ $idType->id }}"
                                                {{ old('id_type_id', $application->applicant->id_type_id) == $idType->id ? 'selected' : '' }}>
                                                {{ $idType->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="text-red-500 text-xs mt-1 hidden" data-error="id_type_id"></div>
                                </div>

                                <!-- ID Number -->
                                <div>
                                    <label for="id_number" class="block text-sm font-medium text-gray-700 mb-1">ID
                                        Number <span class="text-red-500">*</span></label>
                                    <input type="text" name="id_number" id="id_number"
                                        value="{{ old('id_number', $application->applicant->id_number) }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                        required>
                                    <div class="text-red-500 text-xs mt-1 hidden" data-error="id_number"></div>
                                </div>

                                <!-- KRA PIN -->
                                <div>
                                    <label for="kra_pin" class="block text-sm font-medium text-gray-700 mb-1">KRA
                                        PIN</label>
                                    <input type="text" name="kra_pin" id="kra_pin"
                                        value="{{ old('kra_pin', $application->applicant->kra_pin) }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="A123456789Z">
                                    <div class="text-red-500 text-xs mt-1 hidden" data-error="kra_pin"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Support Documents -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="mb-4 text-lg font-semibold text-gray-800">Support Documents</h3>

                        <!-- Existing Documents -->
                        @if ($supportDocuments->isNotEmpty())
                            <div class="mb-6">
                                <h4 class="mb-3 font-medium text-gray-700">Current Documents</h4>
                                <div class="space-y-3">
                                    @foreach ($supportDocuments as $document)
                                        <div
                                            class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                            <div class="flex items-center space-x-3">
                                                <div class="flex-shrink-0">
                                                    <svg class="w-5 h-5 text-gray-400" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                                                            clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $document->original_filename }}</div>
                                                    @if ($document->verification_notes)
                                                        <div class="text-xs text-gray-500">
                                                            {{ $document->verification_notes }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <span
                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                            {{ $document->status === 'verified' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $document->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $document->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                                    {{ ucfirst($document->status) }}
                                                </span>
                                                <label class="flex items-center">
                                                    <input type="checkbox" name="remove_documents[]"
                                                        value="{{ $document->id }}"
                                                        class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                                                    <span class="ml-2 text-xs text-red-600">Remove</span>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Upload New Documents -->
                        <div>
                            <label for="support_documents" class="block text-sm font-medium text-gray-700 mb-2">Upload
                                New Documents</label>
                            <div
                                class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"
                                        viewBox="0 0 48 48">
                                        <path
                                            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="support_documents"
                                            class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                            <span>Upload files</span>
                                            <input id="support_documents" name="support_documents[]" type="file"
                                                class="sr-only" multiple accept=".pdf,.jpg,.jpeg,.png">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PDF, PNG, JPG up to 5MB each</p>
                                </div>
                            </div>
                            <div class="text-red-500 text-xs mt-1 hidden" data-error="support_documents"></div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('individual.applications.show', $application->application_number) }}"
                        class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" id="submitBtn"
                        class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 border border-transparent rounded-md text-sm font-medium text-white transition-colors">
                        <span class="submit-text">Update Application</span>
                        <svg class="submit-spinner animate-spin -mr-1 ml-3 h-4 w-4 text-white hidden"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('updateForm');
                const submitBtn = document.getElementById('submitBtn');
                const submitText = submitBtn.querySelector('.submit-text');
                const submitSpinner = submitBtn.querySelector('.submit-spinner');

                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    // Clear previous errors
                    document.querySelectorAll('[data-error]').forEach(el => {
                        el.classList.add('hidden');
                        el.textContent = '';
                    });

                    // Show loading state
                    submitBtn.disabled = true;
                    submitText.textContent = 'Updating...';
                    submitSpinner.classList.remove('hidden');

                    const formData = new FormData(form);

                    fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Show success message and redirect
                                alert(data.message);
                                if (data.redirect_url) {
                                    window.location.href = data.redirect_url;
                                }
                            } else {
                                // Handle validation errors
                                if (data.errors) {
                                    Object.keys(data.errors).forEach(field => {
                                        const errorEl = document.querySelector(
                                            `[data-error="${field}"]`);
                                        if (errorEl) {
                                            errorEl.textContent = data.errors[field][0];
                                            errorEl.classList.remove('hidden');
                                        }
                                    });
                                }
                                alert(data.message || 'Please correct the errors and try again.');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred while updating your application. Please try again.');
                        })
                        .finally(() => {
                            // Reset loading state
                            submitBtn.disabled = false;
                            submitText.textContent = 'Update Application';
                            submitSpinner.classList.add('hidden');
                        });
                });

                // File upload preview
                const fileInput = document.getElementById('support_documents');
                const filePreview = document.createElement('div');
                filePreview.className = 'mt-3 space-y-2';
                fileInput.parentNode.parentNode.appendChild(filePreview);

                fileInput.addEventListener('change', function(e) {
                    filePreview.innerHTML = '';
                    if (e.target.files.length > 0) {
                        const fileList = document.createElement('div');
                        fileList.className = 'text-sm text-gray-600';
                        fileList.innerHTML = '<strong>Selected files:</strong>';

                        Array.from(e.target.files).forEach(file => {
                            const fileItem = document.createElement('div');
                            fileItem.className =
                                'flex items-center justify-between p-2 bg-gray-50 rounded border';
                            fileItem.innerHTML = `
                    <span>${file.name}</span>
                    <span class="text-xs text-gray-500">${(file.size / 1024 / 1024).toFixed(2)} MB</span>
                `;
                            fileList.appendChild(fileItem);
                        });

                        filePreview.appendChild(fileList);
                    }
                });
            });
        </script>
    </div>
</x-app-layout>
