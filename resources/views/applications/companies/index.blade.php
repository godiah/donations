<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Company Donation Application
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Progress Indicator -->
                    <div class="mb-8">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <div class="step-indicator active" data-step="1">
                                    <span class="step-number">1</span>
                                    <span class="step-label">Contribution Details</span>
                                </div>
                                <div class="step-connector"></div>
                                <div class="step-indicator" data-step="2">
                                    <span class="step-number">2</span>
                                    <span class="step-label">Company Information</span>
                                </div>
                                <div class="step-connector"></div>
                                <div class="step-indicator" data-step="3">
                                    <span class="step-number">3</span>
                                    <span class="step-label">Address</span>
                                </div>
                                <div class="step-connector"></div>
                                <div class="step-indicator" data-step="4">
                                    <span class="step-number">4</span>
                                    <span class="step-label">Banking & Contact</span>
                                </div>
                                <div class="step-connector"></div>
                                <div class="step-indicator" data-step="5">
                                    <span class="step-number">5</span>
                                    <span class="step-label">Review & Submit</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Multi-step Form -->
                    <form id="companyDonationForm" enctype="multipart/form-data">
                        @csrf

                        <!-- Step 1: Contribution Details -->
                        <div class="form-step active" id="step-1">
                            <h3 class="mb-6 text-lg font-semibold text-gray-900">Contribution Details</h3>

                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label for="contribution_name" class="block mb-2 text-sm font-medium text-gray-700">
                                        Contribution Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="contribution_name" name="contribution_name"
                                        value="{{ old('contribution_name') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="e.g., Company CSR Fund">
                                    <div class="hidden mt-1 text-sm text-red-500 error-message"></div>
                                </div>

                                <div>
                                    <label for="contribution_description"
                                        class="block mb-2 text-sm font-medium text-gray-700">
                                        Contribution Description <span class="text-red-500">*</span>
                                    </label>
                                    <textarea id="contribution_description" name="contribution_description" rows="4"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Provide detailed information about the contribution purpose...">{{ old('contribution_description') }}</textarea>
                                    <div class="hidden mt-1 text-sm text-red-500 error-message"></div>
                                </div>

                                <div>
                                    <label for="contribution_reason_id"
                                        class="block mb-2 text-sm font-medium text-gray-700">
                                        Contribution Reason <span class="text-red-500">*</span>
                                    </label>
                                    <select id="contribution_reason_id" name="contribution_reason_id"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Select a reason</option>
                                        @foreach ($contributionReasons as $reason)
                                            <option value="{{ $reason->id }}"
                                                data-requires-document="{{ $reason->requires_document }}"
                                                {{ old('contribution_reason_id') == $reason->id ? 'selected' : '' }}>
                                                {{ $reason->name }} - {{ $reason->description }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="hidden mt-1 text-sm text-red-500 error-message"></div>
                                </div>

                                <!-- Document Upload Section -->
                                <div id="document-upload-section" class="hidden">
                                    <label class="block mb-2 text-sm font-medium text-gray-700">
                                        Required Support Documents <span class="text-red-500">*</span>
                                    </label>
                                    <div id="document-types-info" class="p-4 mb-4 rounded-md bg-blue-50"></div>
                                    <div class="p-6 border-2 border-gray-300 border-dashed rounded-lg">
                                        <div class="text-center">
                                            <svg class="w-12 h-12 mx-auto text-gray-400" stroke="currentColor"
                                                fill="none" viewBox="0 0 48 48">
                                                <path
                                                    d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <div class="mt-4">
                                                <label for="support_documents" class="cursor-pointer">
                                                    <span class="block mt-2 text-sm font-medium text-gray-900">
                                                        Upload support documents
                                                    </span>
                                                    <span class="block mt-1 text-sm text-gray-500">
                                                        PDF, JPG, JPEG, PNG up to 5MB each
                                                    </span>
                                                </label>
                                                <input id="support_documents" name="support_documents[]" type="file"
                                                    class="sr-only" multiple accept=".pdf,.jpg,.jpeg,.png">
                                            </div>
                                        </div>
                                    </div>
                                    <div id="uploaded-files" class="mt-4"></div>
                                    <div class="hidden mt-1 text-sm text-red-500 error-message"></div>
                                </div>
                            </div>

                            <div class="flex justify-end mt-6">
                                <button type="button"
                                    class="px-6 py-2 text-white bg-blue-600 rounded-md next-step hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    Next Step
                                </button>
                            </div>
                        </div>

                        <!-- Step 2: Company Information -->
                        <div class="form-step" id="step-2">
                            <h3 class="mb-6 text-lg font-semibold text-gray-900">Company Information</h3>

                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div class="md:col-span-2">
                                    <label for="company_name" class="block mb-2 text-sm font-medium text-gray-700">
                                        Company Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="company_name" name="company_name"
                                        value="{{ old('company_name') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <div class="hidden mt-1 text-sm text-red-500 error-message"></div>
                                </div>

                                <div>
                                    <label for="registration_certificate"
                                        class="block mb-2 text-sm font-medium text-gray-700">
                                        Registration Certificate <span class="text-red-500">*</span>
                                    </label>
                                    <input type="file" id="registration_certificate"
                                        name="registration_certificate" accept=".pdf,.jpg,.jpeg,.png"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <div class="mt-1 text-sm text-gray-500">Accepted formats: PDF, JPG, PNG. Max size:
                                        5MB</div>
                                    <div class="hidden mt-1 text-sm text-red-500 error-message"></div>
                                </div>

                                <div>
                                    <label for="pin_number" class="block mb-2 text-sm font-medium text-gray-700">
                                        PIN Number <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="pin_number" name="pin_number"
                                        value="{{ old('pin_number') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <div class="hidden mt-1 text-sm text-red-500 error-message"></div>
                                </div>

                                <div>
                                    <label for="cr12" class="block mb-2 text-sm font-medium text-gray-700">
                                        CR12 Document
                                    </label>
                                    <input type="file" id="cr12" name="cr12"
                                        accept=".pdf,.jpg,.jpeg,.png"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <div class="mt-1 text-sm text-gray-500">Optional. Accepted formats: PDF, JPG, PNG.
                                        Max size: 5MB</div>
                                    <div class="hidden mt-1 text-sm text-red-500 error-message"></div>
                                </div>

                                <div>
                                    <label for="cr12_date" class="block mb-2 text-sm font-medium text-gray-700">
                                        CR12 Date
                                    </label>
                                    <input type="date" id="cr12_date" name="cr12_date"
                                        value="{{ old('cr12_date') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <div class="hidden mt-1 text-sm text-red-500 error-message"></div>
                                </div>
                            </div>

                            <div class="flex justify-between mt-6">
                                <button type="button"
                                    class="px-6 py-2 text-gray-700 bg-gray-300 rounded-md prev-step hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                    Previous
                                </button>
                                <button type="button"
                                    class="px-6 py-2 text-white bg-blue-600 rounded-md next-step hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    Next Step
                                </button>
                            </div>
                        </div>

                        <!-- Step 3: Address Information -->
                        <div class="form-step" id="step-3">
                            <h3 class="mb-6 text-lg font-semibold text-gray-900">Address Information</h3>

                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div class="md:col-span-2">
                                    <label for="address" class="block mb-2 text-sm font-medium text-gray-700">
                                        Address <span class="text-red-500">*</span>
                                    </label>
                                    <textarea id="address" name="address" rows="3"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Enter your company's full address">{{ old('address') }}</textarea>
                                    <div class="hidden mt-1 text-sm text-red-500 error-message"></div>
                                </div>

                                <div>
                                    <label for="city" class="block mb-2 text-sm font-medium text-gray-700">
                                        City <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="city" name="city" value="{{ old('city') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <div class="hidden mt-1 text-sm text-red-500 error-message"></div>
                                </div>

                                <div>
                                    <label for="county" class="block mb-2 text-sm font-medium text-gray-700">
                                        County
                                    </label>
                                    <input type="text" id="county" name="county" value="{{ old('county') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <div class="hidden mt-1 text-sm text-red-500 error-message"></div>
                                </div>

                                <div>
                                    <label for="postal_code" class="block mb-2 text-sm font-medium text-gray-700">
                                        Postal Code
                                    </label>
                                    <input type="text" id="postal_code" name="postal_code"
                                        value="{{ old('postal_code') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <div class="hidden mt-1 text-sm text-red-500 error-message"></div>
                                </div>

                                <div>
                                    <label for="country" class="block mb-2 text-sm font-medium text-gray-700">
                                        Country <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="country" name="country"
                                        value="{{ old('country', 'Kenya') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <div class="hidden mt-1 text-sm text-red-500 error-message"></div>
                                </div>
                            </div>

                            <div class="flex justify-between mt-6">
                                <button type="button"
                                    class="px-6 py-2 text-gray-700 bg-gray-300 rounded-md prev-step hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                    Previous
                                </button>
                                <button type="button"
                                    class="px-6 py-2 text-white bg-blue-600 rounded-md next-step hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    Next Step
                                </button>
                            </div>
                        </div>

                        <!-- Step 4: Banking & Contact Information -->
                        <div class="form-step" id="step-4">
                            <h3 class="mb-6 text-lg font-semibold text-gray-900">Banking & Contact Information</h3>

                            <!-- Banking Information -->
                            <div class="mb-8">
                                <h4 class="mb-4 text-sm font-semibold text-gray-700">Banking Information</h4>
                                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                    <div>
                                        <label for="bank_id" class="block mb-2 text-sm font-medium text-gray-700">
                                            Bank <span class="text-red-500">*</span>
                                        </label>
                                        <select id="bank_id" name="bank_id"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            <option value="">Select your bank</option>
                                            @foreach ($banks as $bank)
                                                <option value="{{ $bank->id }}"
                                                    {{ old('bank_id') == $bank->id ? 'selected' : '' }}>
                                                    {{ $bank->display_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="hidden mt-1 text-sm text-red-500 error-message"></div>
                                    </div>

                                    <div>
                                        <label for="bank_account_number"
                                            class="block mb-2 text-sm font-medium text-gray-700">
                                            Bank Account Number <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" id="bank_account_number" name="bank_account_number"
                                            value="{{ old('bank_account_number') }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <div class="hidden mt-1 text-sm text-red-500 error-message"></div>
                                    </div>

                                    <div class="md:col-span-2">
                                        <label for="bank_account_proof"
                                            class="block mb-2 text-sm font-medium text-gray-700">
                                            Bank Account Proof <span class="text-red-500">*</span>
                                        </label>
                                        <input type="file" id="bank_account_proof" name="bank_account_proof"
                                            accept=".pdf,.jpg,.jpeg,.png"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <div class="mt-1 text-sm text-gray-500">Bank statement, cancelled cheque, or
                                            account confirmation letter. Max size: 5MB</div>
                                        <div class="hidden mt-1 text-sm text-red-500 error-message"></div>
                                    </div>

                                    <div class="md:col-span-2">
                                        <label for="settlement" class="block mb-2 text-sm font-medium text-gray-700">
                                            Settlement Information
                                        </label>
                                        <textarea id="settlement" name="settlement" rows="3"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="Any specific settlement instructions or requirements">{{ old('settlement') }}</textarea>
                                        <div class="hidden mt-1 text-sm text-red-500 error-message"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Persons -->
                            <div class="mb-8">
                                <h4 class="mb-4 text-sm font-semibold text-gray-700">Contact Persons</h4>
                                <div id="contact-persons-container">
                                    <div class="contact-person-item mb-6 p-4 border border-gray-200 rounded-lg">
                                        <button type="button"
                                            class="remove-contact-btn absolute top-2 right-2 text-red-600 hover:text-red-800 hidden">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                            <div>
                                                <label for="contact_persons[0][name]"
                                                    class="block mb-2 text-sm font-medium text-gray-700">
                                                    Full Name <span class="text-red-500">*</span>
                                                </label>
                                                <input type="text" name="contact_persons[0][name]"
                                                    value="{{ old('contact_persons.0.name') }}"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                <div class="hidden mt-1 text-sm text-red-500 error-message"></div>
                                            </div>

                                            <div>
                                                <label for="contact_persons[0][position]"
                                                    class="block mb-2 text-sm font-medium text-gray-700">
                                                    Position
                                                </label>
                                                <input type="text" name="contact_persons[0][position]"
                                                    value="{{ old('contact_persons.0.position') }}"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                <div class="hidden mt-1 text-sm text-red-500 error-message"></div>
                                            </div>

                                            <div>
                                                <label for="contact_persons[0][phone]"
                                                    class="block mb-2 text-sm font-medium text-gray-700">
                                                    Phone Number <span class="text-red-500">*</span>
                                                </label>
                                                <input type="tel" name="contact_persons[0][phone]"
                                                    value="{{ old('contact_persons.0.phone') }}"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                <div class="hidden mt-1 text-sm text-red-500 error-message"></div>
                                            </div>

                                            <div>
                                                <label for="contact_persons[0][email]"
                                                    class="block mb-2 text-sm font-medium text-gray-700">
                                                    Email Address <span class="text-red-500">*</span>
                                                </label>
                                                <input type="email" name="contact_persons[0][email]"
                                                    value="{{ old('contact_persons.0.email') }}"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                <div class="hidden mt-1 text-sm text-red-500 error-message"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" id="add-contact-person"
                                    class="mt-2 inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    + Add Another Contact Person
                                </button>
                            </div>

                            <!-- Financial Information -->
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 mb-8">
                                <div>
                                    <label for="target_amount" class="block mb-2 text-sm font-medium text-gray-700">
                                        Target Amount (KES) <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" id="target_amount" name="target_amount"
                                        value="{{ old('target_amount') }}" step="0.01" min="1"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <div class="hidden mt-1 text-sm text-red-500 error-message"></div>
                                </div>

                                <div>
                                    <label for="target_date" class="block mb-2 text-sm font-medium text-gray-700">
                                        Target Date <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" id="target_date" name="target_date"
                                        value="{{ old('target_date') }}"
                                        min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <div class="hidden mt-1 text-sm text-red-500 error-message"></div>
                                </div>
                            </div>

                            <!-- Additional Information -->
                            <div class="mb-6">
                                <h4 class="mb-4 text-sm font-semibold text-gray-700">Additional Information</h4>
                                <div class="grid grid-cols-1 gap-6">
                                    <div>
                                        <label for="additional_info[purpose]"
                                            class="block mb-2 text-sm font-medium text-gray-700">
                                            Purpose & Use of Funds
                                        </label>
                                        <textarea id="additional_info[purpose]" name="additional_info[purpose]" rows="3"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="Describe how the funds will be used">{{ old('additional_info.purpose') }}</textarea>
                                        <div class="hidden mt-1 text-sm text-red-500 error-message"></div>
                                    </div>

                                    <div>
                                        <label for="additional_info[timeline]"
                                            class="block mb-2 text-sm font-medium text-gray-700">
                                            Project Timeline
                                        </label>
                                        <textarea id="additional_info[timeline]" name="additional_info[timeline]" rows="2"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="Key milestones and timeline for your project">{{ old('additional_info.timeline') }}</textarea>
                                        <div class="hidden mt-1 text-sm text-red-500 error-message"></div>
                                    </div>

                                    <div>
                                        <label for="additional_info[expected_impact]"
                                            class="block mb-2 text-sm font-medium text-gray-700">
                                            Expected Impact
                                        </label>
                                        <textarea id="additional_info[expected_impact]" name="additional_info[expected_impact]" rows="3"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="Describe the expected impact of your project">{{ old('additional_info.expected_impact') }}</textarea>
                                        <div class="hidden mt-1 text-sm text-red-500 error-message"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-between mt-6">
                                <button type="button"
                                    class="px-6 py-2 text-gray-700 bg-gray-300 rounded-md prev-step hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                    Previous
                                </button>
                                <button type="button"
                                    class="px-6 py-2 text-white bg-blue-600 rounded-md next-step hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    Review Application
                                </button>
                            </div>
                        </div>

                        <!-- Step 5: Review & Submit -->
                        <div class="form-step" id="step-5">
                            <h3 class="mb-6 text-lg font-semibold text-gray-900">Review Your Application</h3>

                            <div id="application-review" class="space-y-6"></div>

                            <div class="flex justify-between mt-8">
                                <button type="button"
                                    class="px-6 py-2 text-gray-700 bg-gray-300 rounded-md prev-step hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                    Previous
                                </button>
                                <button type="submit" id="submit-application"
                                    class="px-8 py-2 text-white bg-green-600 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span class="submit-text">Submit Application</span>
                                    <span class="hidden loading-text">
                                        <svg class="inline w-5 h-5 mr-3 -ml-1 text-white animate-spin"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        Submitting...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Styles -->
    <style>
        .step-indicator {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }

        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #e5e7eb;
            color: #6b7280;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 16px;
            margin-bottom: 8px;
            transition: all 0.3s ease;
        }

        .step-indicator.active .step-number {
            background-color: #2563eb;
            color: white;
        }

        .step-indicator.completed .step-number {
            background-color: #059669;
            color: white;
        }

        .step-label {
            font-size: 12px;
            color: #6b7280;
            text-align: center;
            white-space: nowrap;
        }

        .step-indicator.active .step-label {
            color: #2563eb;
            font-weight: 600;
        }

        .step-connector {
            width: 60px;
            height: 2px;
            background-color: #e5e7eb;
            margin: 0 10px;
            margin-top: 20px;
        }

        .step-connector.completed {
            background-color: #059669;
        }

        .form-step {
            display: none;
        }

        .form-step.active {
            display: block;
        }

        .uploaded-file {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px;
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            margin-bottom: 8px;
        }

        .uploaded-file .file-info {
            flex: 1;
        }

        .uploaded-file .file-name {
            font-weight: 500;
            color: #374151;
        }

        .uploaded-file .file-size {
            font-size: 12px;
            color: #6b7280;
        }

        .uploaded-file .remove-file {
            color: #dc2626;
            cursor: pointer;
            padding: 4px;
        }

        .review-section {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .review-section h4 {
            font-weight: 600;
            color: #374151;
            margin-bottom: 12px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 8px;
        }

        .review-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .review-label {
            font-weight: 500;
            color: #6b7280;
        }

        .review-value {
            color: #374151;
            text-align: right;
        }

        .contact-person-item {
            position: relative;
        }

        .remove-contact-btn {
            position: absolute;
            top: 10px;
            right: 10px;
        }
    </style>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('companyDonationForm');
            const steps = document.querySelectorAll('.form-step');
            const stepIndicators = document.querySelectorAll('.step-indicator');
            const stepConnectors = document.querySelectorAll('.step-connector');
            const nextButtons = document.querySelectorAll('.next-step');
            const prevButtons = document.querySelectorAll('.prev-step');
            const contributionReasonSelect = document.getElementById('contribution_reason_id');
            const documentUploadSection = document.getElementById('document-upload-section');
            const documentTypesInfo = document.getElementById('document-types-info');
            const supportDocumentsInput = document.getElementById('support_documents');
            const uploadedFilesContainer = document.getElementById('uploaded-files');
            const submitButton = document.getElementById('submit-application');
            const addContactPersonBtn = document.getElementById('add-contact-person');
            const contactPersonsContainer = document.getElementById('contact-persons-container');

            let currentStep = 1;
            let uploadedFiles = [];
            let contactPersonIndex = 1;

            // Step navigation
            nextButtons.forEach(button => {
                button.addEventListener('click', function() {
                    if (validateCurrentStep()) {
                        if (currentStep < 5) {
                            currentStep++;
                            updateStepDisplay();
                        }
                    }
                });
            });

            prevButtons.forEach(button => {
                button.addEventListener('click', function() {
                    if (currentStep > 1) {
                        currentStep--;
                        updateStepDisplay();
                    }
                });
            });

            // Update step display
            function updateStepDisplay() {
                steps.forEach((step, index) => {
                    step.classList.toggle('active', index + 1 === currentStep);
                });

                stepIndicators.forEach((indicator, index) => {
                    indicator.classList.toggle('active', index + 1 === currentStep);
                    indicator.classList.toggle('completed', index + 1 < currentStep);
                });

                stepConnectors.forEach((connector, index) => {
                    connector.classList.toggle('completed', index + 1 < currentStep);
                });

                if (currentStep === 5) {
                    populateReviewSection();
                }
            }

            // Contribution reason change handler
            contributionReasonSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const requiresDocument = selectedOption && selectedOption.dataset.requiresDocument === '1';

                if (requiresDocument) {
                    fetchDocumentTypes(this.value);
                    documentUploadSection.classList.remove('hidden');
                } else {
                    documentUploadSection.classList.add('hidden');
                    uploadedFiles = [];
                    updateUploadedFilesDisplay();
                }
            });

            // Fetch document types
            async function fetchDocumentTypes(contributionReasonId) {
                try {
                    const baseUrl = "{{ url('company/application/document-types') }}";
                    const response = await fetch(`${baseUrl}/${contributionReasonId}`);
                    const documentTypes = await response.json();

                    let html =
                        '<h5 class="mb-2 font-medium text-gray-900">Required Documents:</h5><ul class="text-sm text-gray-600 list-disc list-inside">';
                    documentTypes.forEach(type => {
                        html += `<li><strong>${type.display_name}</strong> - ${type.description}</li>`;
                    });
                    html += '</ul>';

                    documentTypesInfo.innerHTML = html;
                } catch (error) {
                    console.error('Error fetching document types:', error);
                }
            }

            // File upload handler
            supportDocumentsInput.addEventListener('change', function() {
                const files = Array.from(this.files);

                files.forEach(file => {
                    if (file.size > 5 * 1024 * 1024) { // 5MB limit
                        alert(`File "${file.name}" is too large. Maximum size is 5MB.`);
                        return;
                    }

                    uploadedFiles.push(file);
                });

                updateUploadedFilesDisplay();
                this.value = ''; // Reset input
            });

            // Update uploaded files display
            function updateUploadedFilesDisplay() {
                uploadedFilesContainer.innerHTML = '';

                uploadedFiles.forEach((file, index) => {
                    const fileDiv = document.createElement('div');
                    fileDiv.className = 'uploaded-file';
                    fileDiv.innerHTML = `
                        <div class="file-info">
                            <div class="file-name">${file.name}</div>
                            <div class="file-size">${formatFileSize(file.size)}</div>
                        </div>
                        <button type="button" class="remove-file" onclick="removeFile(${index})">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    `;
                    uploadedFilesContainer.appendChild(fileDiv);
                });
            }

            // Remove file function
            window.removeFile = function(index) {
                uploadedFiles.splice(index, 1);
                updateUploadedFilesDisplay();
            };

            // Format file size
            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            // Add contact person
            addContactPersonBtn.addEventListener('click', function() {
                const contactDiv = document.createElement('div');
                contactDiv.className = 'contact-person-item mb-6 p-4 border border-gray-200 rounded-lg';
                contactDiv.innerHTML = `
                    <button type="button" class="remove-contact-btn absolute top-2 right-2 text-red-600 hover:text-red-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="contact_persons[${contactPersonIndex}][name]" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <div class="hidden mt-1 text-sm text-red-500 error-message"></div>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">Position</label>
                            <input type="text" name="contact_persons[${contactPersonIndex}][position]" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <div class="hidden mt-1 text-sm text-red-500 error-message"></div>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Phone Number <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" name="contact_persons[${contactPersonIndex}][phone]" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <div class="hidden mt-1 text-sm text-red-500 error-message"></div>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="contact_persons[${contactPersonIndex}][email]" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <div class="hidden mt-1 text-sm text-red-500 error-message"></div>
                        </div>
                    </div>
                `;

                const removeBtn = contactDiv.querySelector('.remove-contact-btn');
                removeBtn.addEventListener('click', function() {
                    contactDiv.remove();
                });

                contactPersonsContainer.appendChild(contactDiv);
                contactPersonIndex++;
            });

            // Validation functions
            function validateCurrentStep() {
                clearErrors();
                let isValid = true;

                if (currentStep === 1) {
                    isValid = validateStep1() && isValid;
                } else if (currentStep === 2) {
                    isValid = validateStep2() && isValid;
                } else if (currentStep === 3) {
                    isValid = validateStep3() && isValid;
                } else if (currentStep === 4) {
                    isValid = validateStep4() && isValid;
                }

                return isValid;
            }

            function validateStep1() {
                let isValid = true;

                if (!document.getElementById('contribution_name').value.trim()) {
                    showError('contribution_name', 'Contribution name is required');
                    isValid = false;
                }

                if (!document.getElementById('contribution_description').value.trim()) {
                    showError('contribution_description', 'Contribution description is required');
                    isValid = false;
                }

                if (!document.getElementById('contribution_reason_id').value) {
                    showError('contribution_reason_id', 'Please select a contribution reason');
                    isValid = false;
                }

                const selectedOption = contributionReasonSelect.options[contributionReasonSelect.selectedIndex];
                const requiresDocument = selectedOption && selectedOption.dataset.requiresDocument === '1';

                if (requiresDocument && uploadedFiles.length === 0) {
                    showError('support_documents', 'Please upload required support documents');
                    isValid = false;
                }

                return isValid;
            }

            function validateStep2() {
                let isValid = true;

                if (!document.getElementById('company_name').value.trim()) {
                    showError('company_name', 'Company name is required');
                    isValid = false;
                }

                if (!document.getElementById('registration_certificate').value) {
                    showError('registration_certificate', 'Registration certificate is required');
                    isValid = false;
                }

                if (!document.getElementById('pin_number').value.trim()) {
                    showError('pin_number', 'PIN number is required');
                    isValid = false;
                }

                return isValid;
            }

            function validateStep3() {
                let isValid = true;

                if (!document.getElementById('address').value.trim()) {
                    showError('address', 'Address is required');
                    isValid = false;
                }

                if (!document.getElementById('city').value.trim()) {
                    showError('city', 'City is required');
                    isValid = false;
                }

                if (!document.getElementById('country').value.trim()) {
                    showError('country', 'Country is required');
                    isValid = false;
                }

                return isValid;
            }

            function validateStep4() {
                let isValid = true;

                if (!document.getElementById('bank_id').value) {
                    showError('bank_id', 'Please select a bank');
                    isValid = false;
                }

                if (!document.getElementById('bank_account_number').value.trim()) {
                    showError('bank_account_number', 'Bank account number is required');
                    isValid = false;
                }

                if (!document.getElementById('bank_account_proof').value) {
                    showError('bank_account_proof', 'Bank account proof is required');
                    isValid = false;
                }

                const contactPersons = document.querySelectorAll('.contact-person-item');
                contactPersons.forEach((person, index) => {
                    const name = person.querySelector(`[name="contact_persons[${index}][name]"]`).value
                        .trim();
                    const phone = person.querySelector(`[name="contact_persons[${index}][phone]"]`).value
                        .trim();
                    const email = person.querySelector(`[name="contact_persons[${index}][email]"]`).value
                        .trim();

                    if (!name) {
                        showError(`contact_persons[${index}][name]`, 'Full name is required');
                        isValid = false;
                    }

                    if (!phone) {
                        showError(`contact_persons[${index}][phone]`, 'Phone number is required');
                        isValid = false;
                    }

                    if (!email) {
                        showError(`contact_persons[${index}][email]`, 'Email is required');
                        isValid = false;
                    } else if (!isValidEmail(email)) {
                        showError(`contact_persons[${index}][email]`, 'Please enter a valid email address');
                        isValid = false;
                    }
                });

                const targetAmount = parseFloat(document.getElementById('target_amount').value);
                if (!targetAmount || targetAmount <= 0) {
                    showError('target_amount', 'Please enter a valid target amount');
                    isValid = false;
                }

                const targetDate = document.getElementById('target_date').value;
                if (!targetDate) {
                    showError('target_date', 'Target date is required');
                    isValid = false;
                } else {
                    const selectedDate = new Date(targetDate);
                    const tomorrow = new Date();
                    tomorrow.setDate(tomorrow.getDate() + 1);

                    if (selectedDate <= tomorrow) {
                        showError('target_date', 'Target date must be at least one day in the future');
                        isValid = false;
                    }
                }

                return isValid;
            }

            function isValidEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }

            function showError(fieldId, message) {
                const field = document.querySelector(`[name="${fieldId}"]`) || document.getElementById(fieldId);
                const errorDiv = field.parentNode.querySelector('.error-message');
                errorDiv.textContent = message;
                errorDiv.classList.remove('hidden');
                field.classList.add('border-red-500');
            }

            function clearErrors() {
                document.querySelectorAll('.error-message').forEach(error => {
                    error.classList.add('hidden');
                });
                document.querySelectorAll('input, select, textarea').forEach(field => {
                    field.classList.remove('border-red-500');
                });
            }

            // Populate review section
            function populateReviewSection() {
                const reviewContainer = document.getElementById('application-review');
                const formData = new FormData(form);
                const contributionReasonText = contributionReasonSelect.options[contributionReasonSelect
                    .selectedIndex]?.text || '';
                const bankText = document.getElementById('bank_id').options[document.getElementById('bank_id')
                    .selectedIndex]?.text || '';

                let contactPersonsHtml = '';
                const contactPersons = document.querySelectorAll('.contact-person-item');
                contactPersons.forEach((person, index) => {
                    const name = formData.get(`contact_persons[${index}][name]`) || 'N/A';
                    const position = formData.get(`contact_persons[${index}][position]`) || 'Not provided';
                    const phone = formData.get(`contact_persons[${index}][phone]`) || 'N/A';
                    const email = formData.get(`contact_persons[${index}][email]`) || 'N/A';
                    contactPersonsHtml += `
                        <div class="review-item">
                            <span class="review-label">Contact ${index + 1} Name:</span>
                            <span class="review-value">${name}</span>
                        </div>
                        <div class="review-item">
                            <span class="review-label">Position:</span>
                            <span class="review-value">${position}</span>
                        </div>
                        <div class="review-item">
                            <span class="review-label">Phone:</span>
                            <span class="review-value">${phone}</span>
                        </div>
                        <div class="review-item">
                            <span class="review-label">Email:</span>
                            <span class="review-value">${email}</span>
                        </div>
                    `;
                });

                const html = `
                    <div class="review-section">
                        <h4>Contribution Details</h4>
                        <div class="review-item">
                            <span class="review-label">Contribution Name:</span>
                            <span class="review-value">${formData.get('contribution_name') || 'N/A'}</span>
                        </div>
                        <div class="review-item">
                            <span class="review-label">Description:</span>
                            <span class="review-value">${formData.get('contribution_description') || 'N/A'}</span>
                        </div>
                        <div class="review-item">
                            <span class="review-label">Reason:</span>
                            <span class="review-value">${contributionReasonText}</span>
                        </div>
                        <div class="review-item">
                            <span class="review-label">Support Documents:</span>
                            <span class="review-value">${uploadedFiles.length} file(s) uploaded</span>
                        </div>
                    </div>

                    <div class="review-section">
                        <h4>Company Information</h4>
                        <div class="review-item">
                            <span class="review-label">Company Name:</span>
                            <span class="review-value">${formData.get('company_name') || 'N/A'}</span>
                        </div>
                        <div class="review-item">
                            <span class="review-label">Registration Certificate:</span>
                            <span class="review-value">${formData.get('registration_certificate') ? 'Uploaded' : 'Not uploaded'}</span>
                        </div>
                        <div class="review-item">
                            <span class="review-label">PIN Number:</span>
                            <span class="review-value">${formData.get('pin_number') || 'N/A'}</span>
                        </div>
                        <div class="review-item">
                            <span class="review-label">CR12 Document:</span>
                            <span class="review-value">${formData.get('cr12') ? 'Uploaded' : 'Not provided'}</span>
                        </div>
                        <div class="review-item">
                            <span class="review-label">CR12 Date:</span>
                            <span class="review-value">${formData.get('cr12_date') || 'Not provided'}</span>
                        </div>
                    </div>

                    <div class="review-section">
                        <h4>Address Information</h4>
                        <div class="review-item">
                            <span class="review-label">Address:</span>
                            <span class="review-value">${formData.get('address') || 'N/A'}</span>
                        </div>
                        <div class="review-item">
                            <span class="review-label">City:</span>
                            <span class="review-value">${formData.get('city') || 'N/A'}</span>
                        </div>
                        <div class="review-item">
                            <span class="review-label">County:</span>
                            <span class="review-value">${formData.get('county') || 'Not provided'}</span>
                        </div>
                        <div class="review-item">
                            <span class="review-label">Postal Code:</span>
                            <span class="review-value">${formData.get('postal_code') || 'Not provided'}</span>
                        </div>
                        <div class="review-item">
                            <span class="review-label">Country:</span>
                            <span class="review-value">${formData.get('country') || 'N/A'}</span>
                        </div>
                    </div>

                    <div class="review-section">
                        <h4>Banking & Contact Information</h4>
                        <div class="review-item">
                            <span class="review-label">Bank:</span>
                            <span class="review-value">${bankText}</span>
                        </div>
                        <div class="review-item">
                            <span class="review-label">Bank Account Number:</span>
                            <span class="review-value">${formData.get('bank_account_number') || 'N/A'}</span>
                        </div>
                        <div class="review-item">
                            <span class="review-label">Bank Account Proof:</span>
                            <span class="review-value">${formData.get('bank_account_proof') ? 'Uploaded' : 'Not uploaded'}</span>
                        </div>
                        <div class="review-item">
                            <span class="review-label">Settlement Information:</span>
                            <span class="review-value">${formData.get('settlement') || 'Not provided'}</span>
                        </div>
                        ${contactPersonsHtml}
                        <div class="review-item">
                            <span class="review-label">Target Amount:</span>
                            <span class="review-value">KES ${parseFloat(formData.get('target_amount') || 0).toLocaleString()}</span>
                        </div>
                        <div class="review-item">
                            <span class="review-label">Target Date:</span>
                            <span class="review-value">${formData.get('target_date') || 'N/A'}</span>
                        </div>
                        <div class="review-item">
                            <span class="review-label">Purpose & Use of Funds:</span>
                            <span class="review-value">${formData.get('additional_info[purpose]') || 'Not provided'}</span>
                        </div>
                        <div class="review-item">
                            <span class="review-label">Project Timeline:</span>
                            <span class="review-value">${formData.get('additional_info[timeline]') || 'Not provided'}</span>
                        </div>
                        <div class="review-item">
                            <span class="review-label">Expected Impact:</span>
                            <span class="review-value">${formData.get('additional_info[expected_impact]') || 'Not provided'}</span>
                        </div>
                    </div>
                `;

                reviewContainer.innerHTML = html;
            }

            // Form submission
            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                if (!validateCurrentStep()) {
                    return;
                }

                const submitText = submitButton.querySelector('.submit-text');
                const loadingText = submitButton.querySelector('.loading-text');

                submitButton.disabled = true;
                submitText.classList.add('hidden');
                loadingText.classList.remove('hidden');

                try {
                    const formData = new FormData(form);

                    uploadedFiles.forEach((file, index) => {
                        formData.append(`support_documents[${index}]`, file);
                    });

                    const response = await fetch('{{ route('company.store') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('[name=_token]').value
                        }
                    });

                    const result = await response.json();

                    if (result.success) {
                        window.location.href = result.redirect_url;
                    } else {
                        if (result.errors) {
                            Object.keys(result.errors).forEach(field => {
                                showError(field, result.errors[field][0]);
                            });

                            currentStep = 1;
                            updateStepDisplay();
                        } else {
                            alert(result.message ||
                                'An error occurred while submitting your application.');
                        }
                    }
                } catch (error) {
                    console.error('Error submitting form:', error);
                    alert('An error occurred while submitting your application. Please try again.');
                } finally {
                    submitButton.disabled = false;
                    submitText.classList.remove('hidden');
                    loadingText.classList.add('hidden');
                }
            });

            // Handle old input restoration for document types
            @if (old('contribution_reason_id'))
                fetchDocumentTypes({{ old('contribution_reason_id') }});
            @endif
        });
    </script>
</x-app-layout>
