<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Update Application Details - #{{ $application->application_number }}
            </h2>
            <a href="{{ route('company.applications.show', $application->application_number) }}"
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
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-8 bg-white">
                    <!-- Header -->
                    <div class="mb-4">
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
                                <span
                                    class="text-gray-900">{{ $application->submitted_at->format('M j, Y g:i A') }}</span>
                            </div>
                        </div>

                        @if ($application->admin_comments)
                            <div class="p-4 mt-4 rounded-lg bg-orange-50 border border-orange-200">
                                <h4 class="mb-2 font-medium text-orange-800">Additional Information Required:</h4>
                                <p class="text-sm text-orange-700">{{ $application->admin_comments }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Update Form -->
                    <form id="updateForm" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <!-- Company Information (Updatable) -->
                        <div class="mb-8">
                            <h4 class="mb-4 text-lg font-semibold text-gray-900">Company Information</h4>
                            <div class="p-6 rounded-lg bg-blue-50 border border-blue-200">
                                <p class="mb-4 text-sm text-blue-600">You can update the following company information
                                    fields:</p>

                                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                    <!-- PIN Number -->
                                    <div>
                                        <label for="pin_number" class="block text-sm font-medium text-gray-700">PIN
                                            Number</label>
                                        <p class="mb-2 text-xs text-gray-500">Current:
                                            {{ $application->applicant->pin_number ?? 'Not provided' }}</p>
                                        <input type="text" id="pin_number" name="pin_number"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            placeholder="Enter new PIN number">
                                    </div>

                                    <!-- CR12 Date -->
                                    <div>
                                        <label for="cr12_date" class="block text-sm font-medium text-gray-700">CR12
                                            Date</label>
                                        <p class="mb-2 text-xs text-gray-500">
                                            Current:
                                            {{ $application->applicant->cr12_date ? \Carbon\Carbon::parse($application->applicant->cr12_date)->format('Y-m-d') : 'Not provided' }}
                                        </p>
                                        <input type="date" id="cr12_date" name="cr12_date" max="{{ date('Y-m-d') }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    </div>

                                    <!-- Registration Certificate -->
                                    <div>
                                        <label for="registration_certificate"
                                            class="block text-sm font-medium text-gray-700">Registration
                                            Certificate</label>
                                        <p class="mb-2 text-xs text-gray-500">
                                            Current:
                                            {{ $application->applicant->registration_certificate ? 'File uploaded' : 'Not provided' }}
                                        </p>
                                        <input type="file" id="registration_certificate"
                                            name="registration_certificate" accept=".pdf,.jpg,.jpeg,.png"
                                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                        <p class="mt-1 text-xs text-gray-500">PDF, JPG, PNG - Max 5MB</p>
                                    </div>

                                    <!-- CR12 Document -->
                                    <div>
                                        <label for="cr12" class="block text-sm font-medium text-gray-700">CR12
                                            Document</label>
                                        <p class="mb-2 text-xs text-gray-500">
                                            Current:
                                            {{ $application->applicant->cr12 ? 'File uploaded' : 'Not provided' }}
                                        </p>
                                        <input type="file" id="cr12" name="cr12"
                                            accept=".pdf,.jpg,.jpeg,.png"
                                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                        <p class="mt-1 text-xs text-gray-500">PDF, JPG, PNG - Max 5MB</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Banking Information (Updatable) -->
                        <div class="mb-8">
                            <h4 class="mb-4 text-lg font-semibold text-gray-900">Banking Information</h4>
                            <div class="p-6 rounded-lg bg-green-50 border border-green-200">
                                <p class="mb-4 text-sm text-green-600">You can update the following banking information
                                    fields:</p>

                                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                    <!-- Bank -->
                                    <div>
                                        <label for="bank_id"
                                            class="block text-sm font-medium text-gray-700">Bank</label>
                                        <p class="mb-2 text-xs text-gray-500">
                                            Current:
                                            {{ $application->applicant->bank->display_name ?? 'Not provided' }}
                                        </p>
                                        <select id="bank_id" name="bank_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            <option value="">Select a bank (leave unchanged if not updating)
                                            </option>
                                            @foreach ($banks as $bank)
                                                <option value="{{ $bank->id }}">{{ $bank->display_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Bank Account Number -->
                                    <div>
                                        <label for="bank_account_number"
                                            class="block text-sm font-medium text-gray-700">Bank Account Number</label>
                                        <p class="mb-2 text-xs text-gray-500">
                                            Current:
                                            {{ $application->applicant->bank_account_number ?? 'Not provided' }}
                                        </p>
                                        <input type="text" id="bank_account_number" name="bank_account_number"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            placeholder="Enter new account number">
                                    </div>

                                    <!-- Bank Account Proof -->
                                    <div>
                                        <label for="bank_account_proof"
                                            class="block text-sm font-medium text-gray-700">Bank Account Proof</label>
                                        <p class="mb-2 text-xs text-gray-500">
                                            Current:
                                            {{ $application->applicant->bank_account_proof ? 'File uploaded' : 'Not provided' }}
                                        </p>
                                        <input type="file" id="bank_account_proof" name="bank_account_proof"
                                            accept=".pdf,.jpg,.jpeg,.png"
                                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                        <p class="mt-1 text-xs text-gray-500">PDF, JPG, PNG - Max 5MB</p>
                                    </div>

                                    <!-- Settlement Information -->
                                    <div>
                                        <label for="settlement"
                                            class="block text-sm font-medium text-gray-700">Settlement
                                            Information</label>
                                        <p class="mb-2 text-xs text-gray-500">
                                            Current: {{ $application->applicant->settlement ?? 'Not provided' }}
                                        </p>
                                        <textarea id="settlement" name="settlement" rows="3"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            placeholder="Enter settlement information"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Support Documents (Updatable) -->
                        <div class="mb-8">
                            <h4 class="mb-4 text-lg font-semibold text-gray-900">Support Documents</h4>
                            <div class="p-6 rounded-lg bg-yellow-50 border border-yellow-200">
                                <p class="mb-4 text-sm text-yellow-600">You can upload additional support documents:
                                </p>

                                <!-- Current Support Documents -->
                                <div class="mb-6">
                                    <h5 class="text-sm font-medium text-gray-700 mb-3">Current Documents:</h5>
                                    @forelse ($supportDocuments as $document)
                                        <div
                                            class="flex items-center justify-between py-3 px-4 bg-white rounded-lg border mb-2">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ $document->original_filename }}</p>
                                                <span
                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                            {{ $document->status === 'verified' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $document->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $document->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                                    {{ ucfirst($document->status) }}
                                                </span>
                                                @if ($document->verification_notes)
                                                    <p class="text-xs text-gray-500 mt-1">
                                                        {{ $document->verification_notes }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-sm text-gray-500">No documents currently uploaded.</p>
                                    @endforelse
                                </div>

                                <!-- New Support Documents -->
                                <div>
                                    <label for="support_documents" class="block text-sm font-medium text-gray-700">Add
                                        New Support Documents</label>
                                    <input type="file" id="support_documents" name="support_documents[]" multiple
                                        accept=".pdf,.jpg,.jpeg,.png"
                                        class="mt-2 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                    <p class="mt-1 text-xs text-gray-500">PDF, JPG, PNG - Max 5MB each</p>
                                </div>
                            </div>
                        </div>

                        <!-- Read-only sections for context -->

                        <!-- Contribution Details (Read-only) -->
                        <div class="mb-8">
                            <h4 class="mb-4 text-lg font-semibold text-gray-900">Contribution Details <span
                                    class="text-sm font-normal text-gray-500">(View Only)</span></h4>
                            <div class="p-6 rounded-lg bg-gray-50">
                                <div class="grid grid-cols-1 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500">Contribution
                                            Name</label>
                                        <p class="mt-1 text-gray-900">
                                            {{ $application->applicant->contribution_name ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500">Description</label>
                                        <p class="mt-1 text-gray-900">
                                            {{ $application->applicant->contribution_description ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500">Reason</label>
                                        <p class="mt-1 text-gray-900">
                                            {{ $application->applicant->contributionReason->name ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Persons (Read-only) -->
                        <div class="mb-8">
                            <h4 class="mb-4 text-lg font-semibold text-gray-900">Contact Persons <span
                                    class="text-sm font-normal text-gray-500">(View Only)</span></h4>
                            <div class="p-6 rounded-lg bg-gray-50">
                                @forelse ($application->applicant->contact_persons as $index => $person)
                                    <div
                                        class="mb-4 {{ $index < count($application->applicant->contact_persons) - 1 ? 'border-b pb-4' : '' }}">
                                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-500">Name</label>
                                                <p class="mt-1 text-gray-900">{{ $person['name'] ?? 'N/A' }}</p>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-500">Position</label>
                                                <p class="mt-1 text-gray-900">{{ $person['position'] ?? 'N/A' }}</p>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-500">Phone</label>
                                                <p class="mt-1 text-gray-900">{{ $person['phone'] ?? 'N/A' }}</p>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-500">Email</label>
                                                <p class="mt-1 text-gray-900">{{ $person['email'] ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-gray-900">No contact persons provided.</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end space-x-4 pt-6 border-t">
                            <a href="{{ route('company.applications.show', $application->application_number) }}"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Cancel
                            </a>
                            <button type="submit" id="submitButton"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                <span id="submitText">Update Application</span>
                                <svg id="loadingSpinner" class="hidden animate-spin -mr-1 ml-3 h-4 w-4 text-white"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Error/Success Messages -->
        <div id="messageContainer" class="fixed top-4 right-4 z-50"></div>
    </div>

    <script>
        // Form submission
        document.getElementById('updateForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const submitButton = document.getElementById('submitButton');
            const submitText = document.getElementById('submitText');
            const loadingSpinner = document.getElementById('loadingSpinner');

            // Disable submit button and show loading
            submitButton.disabled = true;
            submitText.textContent = 'Updating...';
            loadingSpinner.classList.remove('hidden');

            const formData = new FormData(this);

            fetch(`{{ route('company.applications.update.store', $application->application_number) }}`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showMessage('success', data.message);
                        setTimeout(() => {
                            if (data.redirect_url) {
                                window.location.href = data.redirect_url;
                            }
                        }, 2000);
                    } else {
                        showMessage('error', data.message || 'An error occurred');
                        if (data.errors) {
                            displayValidationErrors(data.errors);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showMessage('error', 'An unexpected error occurred');
                })
                .finally(() => {
                    // Re-enable submit button
                    submitButton.disabled = false;
                    submitText.textContent = 'Update Application';
                    loadingSpinner.classList.add('hidden');
                });
        });

        function showMessage(type, message) {
            const container = document.getElementById('messageContainer');
            const alertClass = type === 'success' ? 'bg-green-500' : 'bg-red-500';

            const messageDiv = document.createElement('div');
            messageDiv.className =
                `${alertClass} text-white px-6 py-4 rounded-lg shadow-lg mb-4 transition-all duration-300`;
            messageDiv.textContent = message;

            container.appendChild(messageDiv);

            setTimeout(() => {
                messageDiv.remove();
            }, 5000);
        }

        function displayValidationErrors(errors) {
            // Clear previous error messages
            document.querySelectorAll('.error-message').forEach(el => el.remove());

            Object.keys(errors).forEach(field => {
                const input = document.querySelector(`[name="${field}"]`);
                if (input) {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'error-message text-red-600 text-sm mt-1';
                    errorDiv.textContent = errors[field][0];
                    input.parentNode.appendChild(errorDiv);
                }
            });
        }
    </script>

</x-app-layout>
