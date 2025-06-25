<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Individual Donation Application
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Progress Indicator -->
                    <div class="mb-8">
                        <div class="flex items-center mx-auto mb-4 justify-evenly">
                            <div class="flex items-center">
                                <div class="step-indicator active" data-step="1">
                                    <span class="step-number">1</span>
                                    <span class="step-label">Contribution Details</span>
                                </div>
                                <div class="step-connector"></div>
                                <div class="step-indicator" data-step="2">
                                    <span class="step-number">2</span>
                                    <span class="step-label">Personal Information</span>
                                </div>
                                <div class="step-connector"></div>
                                <div class="step-indicator" data-step="3">
                                    <span class="step-number">3</span>
                                    <span class="step-label">Identification & Financial</span>
                                </div>
                                <div class="step-connector"></div>
                                <div class="step-indicator" data-step="4">
                                    <span class="step-number">4</span>
                                    <span class="step-label">Review & Submit</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Multi-step Form -->
                    <form id="individualDonationForm" enctype="multipart/form-data">
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
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="e.g., John's Wedding Fund, Mary's Medical Treatment">
                                    <div class="hidden mt-1 text-sm text-red-500 error-message"></div>
                                </div>

                                <div>
                                    <label for="contribution_description"
                                        class="block mb-2 text-sm font-medium text-gray-700">
                                        Contribution Description <span class="text-red-500">*</span>
                                    </label>
                                    <textarea id="contribution_description" name="contribution_description" rows="4"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Provide detailed information about the contribution purpose..."></textarea>
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
                                                data-requires-document="{{ $reason->requires_document }}">
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
                                    <div id="document-types-info" class="p-4 mb-4 rounded-md bg-blue-50">
                                        <!-- Document types will be populated here -->
                                    </div>
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

                        <!-- Step 2: Personal Information -->
                        <div class="form-step" id="step-2">
                            <h3 class="mb-0.5 text-lg font-semibold text-gray-900">Personal Information</h3>
                            <p class="mb-6 text-sm text-gray-600">The provided email and phone number will be used for
                                communication purposes.</p>

                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <!-- Name Fields -->
                                <div class="md:col-span-2">
                                    <div class="p-4 mb-4 border border-blue-200 rounded-md bg-blue-50">
                                        <p class="text-sm text-blue-800">
                                            <strong>Important:</strong> Please provide your names exactly as they appear
                                            on your National ID, Passport, or Alien Registration Card. This information
                                            will be verified against official records.
                                        </p>
                                    </div>

                                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                        <div>
                                            <label for="first_name"
                                                class="block mb-2 text-sm font-medium text-gray-700">
                                                First Name <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" id="first_name" name="first_name"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                placeholder="Enter first name">
                                            <div class="hidden mt-1 text-sm text-red-500 error-message"></div>
                                        </div>

                                        <div>
                                            <label for="middle_name"
                                                class="block mb-2 text-sm font-medium text-gray-700">
                                                Middle Name
                                            </label>
                                            <input type="text" id="middle_name" name="middle_name"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                placeholder="Enter middle name (optional)">
                                            <div class="hidden mt-1 text-sm text-red-500 error-message"></div>
                                        </div>

                                        <div>
                                            <label for="last_name"
                                                class="block mb-2 text-sm font-medium text-gray-700">
                                                Last Name <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" id="last_name" name="last_name"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                placeholder="Enter last name">
                                            <div class="hidden mt-1 text-sm text-red-500 error-message"></div>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label for="email" class="block mb-2 text-sm font-medium text-gray-700">
                                        Email Address <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" id="email" name="email"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="beneficiary@example.com">
                                    <div class="hidden mt-1 text-sm text-red-500 error-message"></div>
                                </div>

                                <div>
                                    <label for="phone" class="block mb-2 text-sm font-medium text-gray-700">
                                        Phone Number <span class="text-red-500">*</span>
                                    </label>
                                    <input type="tel" id="phone" name="phone"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="+254 700 000 000">
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

                        <!-- Step 3: Identification & Financial -->
                        <div class="form-step" id="step-3">
                            <h3 class="mb-6 text-lg font-semibold text-gray-900">Identification & Financial Details
                            </h3>

                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div>
                                    <label for="id_type_id" class="block mb-2 text-sm font-medium text-gray-700">
                                        ID Type <span class="text-red-500">*</span>
                                    </label>
                                    <select id="id_type_id" name="id_type_id"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Select ID type</option>
                                        @foreach ($idTypes as $idType)
                                            <option value="{{ $idType->id }}" data-type="{{ $idType->type }}">
                                                {{ $idType->display_name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="hidden mt-1 text-sm text-red-500 error-message"></div>
                                </div>

                                <div class="relative">
                                    <label for="id_number" class="block mb-2 text-sm font-medium text-gray-700">
                                        ID Number <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="text" id="id_number" name="id_number"
                                            class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="Enter ID number">

                                        <!-- KYC Verification Status Indicator -->
                                        {{-- <div id="kyc-status" class="absolute inset-y-0 right-0 flex items-center pr-3"
                                            style="display: none;">
                                            <!-- Loading Spinner -->
                                            <div id="kyc-loading" class="hidden">
                                                <svg class="w-4 h-4 text-blue-500 animate-spin" fill="none"
                                                    viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                                        stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor"
                                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                    </path>
                                                </svg>
                                            </div>

                                            <!-- Success Icon -->
                                            <div id="kyc-success" class="hidden">
                                                <svg class="w-4 h-4 text-green-500" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>

                                            <!-- Error Icon -->
                                            <div id="kyc-error" class="hidden">
                                                <svg class="w-4 h-4 text-red-500" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </div>
                                        </div> --}}
                                    </div>

                                    <!-- KYC Status Messages -->
                                    {{-- <div id="kyc-message" class="mt-1 text-sm" style="display: none;">
                                        <div id="kyc-success-message" class="hidden text-green-600">
                                            <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Identity verified successfully
                                        </div>
                                        <div id="kyc-error-message" class="hidden text-red-600">
                                            <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span id="kyc-error-text">Identity verification failed. Please check your
                                                details.</span>
                                        </div>
                                        <div id="kyc-loading-message" class="hidden text-blue-600">
                                            Verifying identity... Please wait.
                                        </div>
                                    </div> --}}

                                    <div class="hidden mt-1 text-sm text-red-500 error-message"></div>
                                </div>

                                <div>
                                    <label for="kra_pin" class="block mb-2 text-sm font-medium text-gray-700">
                                        KRA PIN
                                    </label>
                                    <input type="text" id="kra_pin" name="kra_pin"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="A000000000A (Optional)">
                                    <div class="hidden mt-1 text-sm text-red-500 error-message"></div>
                                </div>

                                <div>
                                    <label for="target_amount" class="block mb-2 text-sm font-medium text-gray-700">
                                        Target Amount (KES) <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" id="target_amount" name="target_amount" step="0.01"
                                        min="1"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="10000.00">
                                    <div class="hidden mt-1 text-sm text-red-500 error-message"></div>
                                </div>

                                <div class="md:col-span-2">
                                    <label for="target_date" class="block mb-2 text-sm font-medium text-gray-700">
                                        Target Date <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" id="target_date" name="target_date"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                    <div class="hidden mt-1 text-sm text-red-500 error-message"></div>
                                </div>

                                <div class="md:col-span-2">
                                    <h4 class="pb-2 mb-4 font-semibold text-gray-900 border-b text-md">Payout Mandate
                                    </h4>

                                    <div class="space-y-4">
                                        <!-- Mandate Type Selection -->
                                        <div>
                                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                                Payout Authorization Type <span class="text-red-500">*</span>
                                            </label>
                                            <div class="space-y-3">
                                                <div class="flex items-start">
                                                    <input type="radio" id="mandate_single" name="mandate_type"
                                                        value="single"
                                                        class="w-4 h-4 mt-1 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-2">
                                                    <div class="ml-3">
                                                        <label for="mandate_single"
                                                            class="text-sm font-medium text-gray-700 cursor-pointer">
                                                            Single Mandate
                                                        </label>
                                                        <p class="mt-1 text-xs text-gray-500">You will be both the
                                                            maker and approver of payouts</p>
                                                    </div>
                                                </div>

                                                <div class="flex items-start">
                                                    <input type="radio" id="mandate_dual" name="mandate_type"
                                                        value="dual"
                                                        class="w-4 h-4 mt-1 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-2">
                                                    <div class="ml-3">
                                                        <label for="mandate_dual"
                                                            class="text-sm font-medium text-gray-700 cursor-pointer">
                                                            Dual Mandate
                                                        </label>
                                                        <p class="mt-1 text-xs text-gray-500">You create payout
                                                            requests, another person approves them</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="hidden mt-1 text-sm text-red-500 error-message"
                                                id="mandate_type_error"></div>
                                        </div>

                                        <!-- Checker Details (shown only for dual mandate) -->
                                        <div id="checker_details"
                                            class="p-4 space-y-4 border border-blue-200 rounded-lg bg-blue-50"
                                            style="display: none;">
                                            <div class="mb-3">
                                                <div class="flex items-center mb-2 space-x-2">
                                                    <svg class="w-5 h-5 text-blue-600" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                                        </path>
                                                    </svg>
                                                    <p class="text-sm font-medium text-blue-800">Checker Authorization
                                                    </p>
                                                </div>
                                                <p class="text-xs text-blue-700">The checker will be authorized to
                                                    approve payouts for this application. They will receive an
                                                    invitation to complete their account setup.</p>
                                            </div>

                                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                                <div>
                                                    <label for="checker_name"
                                                        class="block mb-2 text-sm font-medium text-gray-700">
                                                        Checker Full Name <span class="text-red-500">*</span>
                                                    </label>
                                                    <input type="text" id="checker_name" name="checker_name"
                                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                        placeholder="Enter checker's full name">
                                                    <div class="hidden mt-1 text-sm text-red-500 error-message"
                                                        id="checker_name_error"></div>
                                                </div>

                                                <div>
                                                    <label for="checker_email"
                                                        class="block mb-2 text-sm font-medium text-gray-700">
                                                        Checker Email Address <span class="text-red-500">*</span>
                                                    </label>
                                                    <input type="email" id="checker_email" name="checker_email"
                                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                        placeholder="checker@example.com">
                                                    <div class="hidden mt-1 text-sm text-red-500 error-message"
                                                        id="checker_email_error"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="md:col-span-2">
                                    <label for="additional_info" class="block mb-2 text-sm font-medium text-gray-700">
                                        Additional Information
                                    </label>
                                    <textarea id="additional_info" name="additional_info" rows="4"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Any additional information you'd like to provide..."></textarea>
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
                                    Review Application
                                </button>
                            </div>
                        </div>

                        <!-- Step 4: Review & Submit -->
                        <div class="form-step" id="step-4">
                            <div class="review-header">
                                <h3>Review Your Application</h3>
                                <p>Please review all information before submitting your application</p>
                            </div>

                            <div class="review-summary">
                                <p class="review-summary-text">✓ All sections completed - Ready for submission</p>
                            </div>

                            <div id="application-review" class="space-y-6">
                                <!-- Review content will be populated by JavaScript -->
                            </div>

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
            justify-content: between;
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
    </style>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('individualDonationForm');
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
            const mandateRadios = document.querySelectorAll('input[name="mandate_type"]');
            const checkerDetails = document.getElementById('checker_details');
            const submitButton = document.getElementById('submit-application');

            let currentStep = 1;
            let uploadedFiles = [];

            // Step navigation
            nextButtons.forEach(button => {
                button.addEventListener('click', function() {
                    if (validateCurrentStep()) {
                        if (currentStep < 4) {
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

                if (currentStep === 4) {
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
                    const baseUrl = "{{ url('individual/application/document-types') }}";
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

            // Mandate
            mandateRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'dual') {
                        checkerDetails.style.display = 'block';
                        // Add required attribute to checker fields
                        document.getElementById('checker_name').setAttribute('required',
                            'required');
                        document.getElementById('checker_email').setAttribute('required',
                            'required');
                    } else {
                        checkerDetails.style.display = 'none';
                        // Remove required attribute from checker fields
                        document.getElementById('checker_name').removeAttribute('required');
                        document.getElementById('checker_email').removeAttribute('required');
                        // Clear checker fields
                        document.getElementById('checker_name').value = '';
                        document.getElementById('checker_email').value = '';
                        // Clear any error messages
                        hideError('checker_name');
                        hideError('checker_email');
                    }
                });
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
                }

                return isValid;
            }

            function validateStep1() {
                let isValid = true;

                // Contribution name
                if (!document.getElementById('contribution_name').value.trim()) {
                    showError('contribution_name', 'Contribution name is required');
                    isValid = false;
                }

                // Contribution description
                if (!document.getElementById('contribution_description').value.trim()) {
                    showError('contribution_description', 'Contribution description is required');
                    isValid = false;
                }

                // Contribution reason
                if (!document.getElementById('contribution_reason_id').value) {
                    showError('contribution_reason_id', 'Please select a contribution reason');
                    isValid = false;
                }

                // Check if documents are required
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

                // First name
                if (!document.getElementById('first_name').value.trim()) {
                    showError('first_name', 'First name is required');
                    isValid = false;
                }

                // Last name
                if (!document.getElementById('last_name').value.trim()) {
                    showError('last_name', 'Last name is required');
                    isValid = false;
                }

                // Email
                const email = document.getElementById('email').value.trim();
                if (!email) {
                    showError('email', 'Email is required');
                    isValid = false;
                } else if (!isValidEmail(email)) {
                    showError('email', 'Please enter a valid email address');
                    isValid = false;
                }

                // Phone
                if (!document.getElementById('phone').value.trim()) {
                    showError('phone', 'Phone number is required');
                    isValid = false;
                }

                return isValid;
            }

            function validateStep3() {
                let isValid = true;

                // ID type
                if (!document.getElementById('id_type_id').value) {
                    showError('id_type_id', 'Please select an ID type');
                    isValid = false;
                }

                // ID number
                if (!document.getElementById('id_number').value.trim()) {
                    showError('id_number', 'ID number is required');
                    isValid = false;
                }

                // Target amount
                const targetAmount = parseFloat(document.getElementById('target_amount').value);
                if (!targetAmount || targetAmount <= 0) {
                    showError('target_amount', 'Please enter a valid target amount');
                    isValid = false;
                }

                // Target date
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

                // Payout mandate validation
                const selectedMandateType = document.querySelector('input[name="mandate_type"]:checked');
                if (!selectedMandateType) {
                    showError('mandate_type', 'Please select a payout authorization type');
                    isValid = false;
                } else {
                    hideError('mandate_type');

                    // If dual mandate is selected, validate checker details
                    if (selectedMandateType.value === 'dual') {
                        const checkerName = document.getElementById('checker_name').value.trim();
                        const checkerEmail = document.getElementById('checker_email').value.trim();

                        if (!checkerName) {
                            showError('checker_name', 'Checker name is required for dual mandate');
                            isValid = false;
                        } else {
                            hideError('checker_name');
                        }

                        if (!checkerEmail) {
                            showError('checker_email', 'Checker email is required for dual mandate');
                            isValid = false;
                        } else if (!isValidEmail(checkerEmail)) {
                            showError('checker_email', 'Please enter a valid email address');
                            isValid = false;
                        } else {
                            hideError('checker_email');
                        }
                    }
                }

                return isValid;
            }

            // Utility functions
            function isValidEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }

            function showError(fieldId, message) {
                const field = document.getElementById(fieldId);
                const errorElement = document.getElementById(fieldId + '_error') ||
                    field.parentNode.querySelector('.error-message');

                if (field) {
                    field.classList.add('border-red-500');
                    field.classList.remove('border-gray-300');
                }

                if (errorElement) {
                    errorElement.textContent = message;
                    errorElement.classList.remove('hidden');
                }
            }

            function clearErrors() {
                document.querySelectorAll('.error-message').forEach(error => {
                    error.classList.add('hidden');
                });
                document.querySelectorAll('input, select, textarea').forEach(field => {
                    field.classList.remove('border-red-500');
                });
            }

            function hideError(fieldId) {
                const field = document.getElementById(fieldId);
                const errorElement = document.getElementById(fieldId + '_error') ||
                    field.parentNode.querySelector('.error-message');

                if (field) {
                    field.classList.remove('border-red-500');
                    field.classList.add('border-gray-300');
                }

                if (errorElement) {
                    errorElement.classList.add('hidden');
                    errorElement.textContent = '';
                }
            }

            // Populate review section
            function populateReviewSection() {
                const reviewContainer = document.getElementById('application-review');

                // Get form data
                const formData = new FormData(form);
                const contributionReasonText = contributionReasonSelect.options[contributionReasonSelect
                    .selectedIndex]?.text || '';
                const idTypeText = document.getElementById('id_type_id')?.options[document.getElementById(
                    'id_type_id')?.selectedIndex]?.text || '';

                const html = `
                <div class="review-section">
                    <h4>
                        <svg class="inline-block section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Contribution Details
                    </h4>
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
                    <h4>
                        <svg class="inline-block section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Personal Information
                    </h4>
                    <div class="review-item">
                        <span class="review-label">Full Name:</span>
                        <span class="review-value">${formData.get('full_name') || 'N/A'}</span>
                    </div>
                    <div class="review-item">
                        <span class="review-label">Email:</span>
                        <span class="review-value">${formData.get('email') || 'N/A'}</span>
                    </div>
                    <div class="review-item">
                        <span class="review-label">Phone:</span>
                        <span class="review-value">${formData.get('phone') || 'N/A'}</span>
                    </div>
                    <div class="review-item">
                        <span class="review-label">Emergency Contact:</span>
                        <span class="review-value">${formData.get('emergency_contact_name') || 'N/A'}</span>
                    </div>
                    <div class="review-item">
                        <span class="review-label">Emergency Phone:</span>
                        <span class="review-value">${formData.get('emergency_contact_phone') || 'N/A'}</span>
                    </div>
                </div>

                <div class="review-section">
                    <h4>
                        <svg class="inline-block section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        Identification & Financial
                    </h4>
                    <div class="review-item">
                        <span class="review-label">ID Type:</span>
                        <span class="review-value">${idTypeText}</span>
                    </div>
                    <div class="review-item">
                        <span class="review-label">ID Number:</span>
                        <span class="review-value">${formData.get('id_number') || 'N/A'}</span>
                    </div>
                    <div class="review-item">
                        <span class="review-label">KRA PIN:</span>
                        <span class="review-value">${formData.get('kra_pin') || 'Not provided'}</span>
                    </div>
                    <div class="review-item">
                        <span class="review-label">Target Amount:</span>
                        <span class="review-value">KES ${parseFloat(formData.get('target_amount') || 0).toLocaleString()}</span>
                    </div>
                    <div class="review-item">
                        <span class="review-label">Target Date:</span>
                        <span class="review-value">${formData.get('target_date') || 'N/A'}</span>
                    </div>
                    <div class="review-item">
                        <span class="review-label">Additional Info:</span>
                        <span class="review-value">${formData.get('additional_info') || 'None provided'}</span>
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

                // Show loading state
                submitButton.disabled = true;
                submitText.classList.add('hidden');
                loadingText.classList.remove('hidden');

                try {
                    const formData = new FormData(form);

                    // Add uploaded files to form data
                    uploadedFiles.forEach((file, index) => {
                        formData.append(`support_documents[${index}]`, file);
                    });

                    const response = await fetch('{{ route('individual.store') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('[name=_token]').value
                        }
                    });

                    const result = await response.json();

                    if (result.success) {
                        // Success - redirect to success page
                        window.location.href = result.redirect_url;
                    } else {
                        // Handle validation errors
                        if (result.errors) {
                            Object.keys(result.errors).forEach(field => {
                                showError(field, result.errors[field][0]);
                            });

                            // Go back to the first step with errors
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
                    // Reset loading state
                    submitButton.disabled = false;
                    submitText.classList.remove('hidden');
                    loadingText.classList.add('hidden');
                }
            });
        });
    </script>
</x-app-layout>
