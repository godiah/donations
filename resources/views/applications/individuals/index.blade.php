<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="py-2 space-y-1">
                <h2 class="text-2xl font-bold font-heading text-neutral-800">
                    Donation Application
                </h2>
                <p class="text-sm font-medium text-neutral-500">
                    Create your fundraising campaign in just a few simple steps
                </p>
            </div>
            <div class="items-center hidden space-x-2 text-sm sm:flex text-neutral-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                <span>Secure & Encrypted</span>
            </div>
        </div>
    </x-slot>

    <div class="pt-6 pb-8">
        <div class="relative">
            <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Progress Indicator -->
                <div class="mb-8">
                    <div class="flex items-center justify-between max-w-3xl mx-auto">
                        <div class="flex items-center w-full space-x-4">
                            <!-- Step 1 -->
                            <div class="flex items-center">
                                <div class="step-circle active" data-step="1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span class="step-num">1</span>
                                </div>
                                <div class="step-label">
                                    <span class="font-medium">Contribution</span>
                                    <span class="text-xs text-neutral-500">Details & Purpose</span>
                                </div>
                            </div>

                            <div class="step-line"></div>

                            <!-- Step 2 -->
                            <div class="flex items-center">
                                <div class="step-circle" data-step="2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span class="step-num">2</span>
                                </div>
                                <div class="step-label">
                                    <span class="font-medium">Personal</span>
                                    <span class="text-xs text-neutral-500">Information</span>
                                </div>
                            </div>

                            <div class="step-line"></div>

                            <!-- Step 3 -->
                            <div class="flex items-center">
                                <div class="step-circle" data-step="3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 114 0v2m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                    </svg>
                                    <span class="step-num">3</span>
                                </div>
                                <div class="step-label">
                                    <span class="font-medium">Financial</span>
                                    <span class="text-xs text-neutral-500">& Verification</span>
                                </div>
                            </div>

                            <div class="step-line"></div>

                            <!-- Step 4 -->
                            <div class="flex items-center">
                                <div class="step-circle" data-step="4">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="step-num">4</span>
                                </div>
                                <div class="step-label">
                                    <span class="font-medium">Review</span>
                                    <span class="text-xs text-neutral-500">& Submit</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Container -->
                <div class="overflow-hidden bg-white border shadow-xl rounded-2xl border-neutral-100">
                    <form id="individualDonationForm" enctype="multipart/form-data">
                        @csrf

                        <!-- Step 1: Contribution Details -->
                        <div class="form-step active" id="step-1">
                            <div class="p-8">
                                <div class="mb-8 text-center">
                                    <div
                                        class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-primary-500 to-primary-600 rounded-2xl">
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <h3 class="mb-2 text-2xl font-bold font-heading text-neutral-800">Tell us about
                                        your
                                        cause</h3>
                                    <p class="text-neutral-600">Help people understand what you're raising money for
                                    </p>
                                </div>

                                <div class="max-w-5xl mx-auto space-y-6">
                                    <!-- Contribution Name -->
                                    <div class="form-group">
                                        <label for="contribution_name" class="form-label required">
                                            Contribution Name
                                        </label>
                                        <input type="text" id="contribution_name" name="contribution_name"
                                            class="form-input" placeholder="e.g., Help Sarah's Cancer Treatment Fund">
                                        <div class="hidden form-error"></div>
                                        {{-- <p class="form-hint">Make it clear and compelling - this is what donors will
                                            see
                                            first</p> --}}
                                    </div>

                                    <!-- Contribution Description -->
                                    <div class="form-group">
                                        <label for="contribution_description" class="form-label required">
                                            Contribution Description
                                        </label>
                                        <textarea id="contribution_description" name="contribution_description" rows="4"
                                            class="resize-none form-input" placeholder="Tell your story..."></textarea>
                                        <div class="hidden form-error"></div>
                                        {{-- <p class="form-hint">Share your story to connect with potential donors
                                            emotionally
                                        </p> --}}
                                    </div>

                                    <!-- Contribution Reason -->
                                    <div class="form-group">
                                        <label for="contribution_reason_id" class="form-label required">
                                            Contribution Reason
                                        </label>
                                        <select id="contribution_reason_id" name="contribution_reason_id"
                                            class="form-input">
                                            <option value="">Choose a category that best fits your cause</option>
                                            @foreach ($contributionReasons as $reason)
                                                <option value="{{ $reason->id }}"
                                                    data-requires-document="{{ $reason->requires_document }}">
                                                    {{ $reason->name }} - {{ $reason->description }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="hidden form-error"></div>
                                    </div>

                                    <!--  Document Upload Section -->
                                    <div id="document-upload-section" class="hidden form-group">
                                        <label class="form-label required">Supporting Documents</label>

                                        <div id="document-types-info"
                                            class="p-4 mb-4 border bg-primary-50 border-primary-200 rounded-xl">
                                            <!-- Document types will be populated here -->
                                        </div>

                                        <div class="upload-area"
                                            onclick="document.getElementById('support_documents').click()">
                                            <div class="upload-content">
                                                <div
                                                    class="flex items-center justify-center w-12 h-12 mx-auto mb-3 bg-gradient-to-br from-secondary-500 to-secondary-600 rounded-xl">
                                                    <svg class="w-6 h-6 text-white" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                    </svg>
                                                </div>
                                                <h4 class="mb-1 font-medium text-neutral-800">Upload Documents</h4>
                                                <p class="mb-2 text-sm text-neutral-600">Click to browse or drag and
                                                    drop
                                                    files</p>
                                                <p class="text-xs text-neutral-500">PDF, JPG, JPEG, PNG up to 5MB each
                                                </p>
                                            </div>
                                            <input id="support_documents" name="support_documents[]" type="file"
                                                class="hidden" multiple accept=".pdf,.jpg,.jpeg,.png">
                                        </div>

                                        <div id="uploaded-files" class="mt-4"></div>
                                        <div class="hidden form-error"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="px-8 py-6 border-t border-neutral-100 bg-neutral-50">
                                <div class="flex justify-end">
                                    <button type="button" class="btn-primary next-step">
                                        Continue
                                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Personal Information -->
                        <div class="form-step" id="step-2">
                            <div class="p-8">
                                <div class="mb-8 text-center">
                                    <div
                                        class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-success-500 to-success-600 rounded-2xl">
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <h3 class="mb-2 text-2xl font-bold font-heading text-neutral-800">Your Information
                                    </h3>
                                    <p class="text-neutral-600">We need your details for verification and communication
                                    </p>
                                </div>

                                <div class="max-w-5xl mx-auto space-y-6">
                                    <!-- Important Notice -->
                                    {{-- <div class="info-card">
                                        <div class="flex items-start space-x-3">
                                            <div
                                                class="w-8 h-8 bg-secondary-500 rounded-lg flex items-center justify-center mt-0.5">
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <h4 class="mb-1 font-medium text-neutral-800">Important</h4>
                                                <p class="text-sm text-neutral-600">Please provide your names exactly
                                                    as
                                                    they appear on your National ID, Passport, or Alien Registration
                                                    Card.
                                                </p>
                                            </div>
                                        </div>
                                    </div> --}}

                                    <!-- Name Fields -->
                                    <div>
                                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                            <div class="form-group">
                                                <label for="first_name" class="form-label required">First Name</label>
                                                <input type="text" id="first_name" name="first_name"
                                                    class="form-input" placeholder="John">
                                                <div class="hidden form-error"></div>
                                            </div>
                                            <div class="form-group">
                                                <label for="middle_name" class="form-label">Middle Name</label>
                                                <input type="text" id="middle_name" name="middle_name"
                                                    class="form-input" placeholder="Optional">
                                                <div class="hidden form-error"></div>
                                            </div>
                                            <div class="form-group">
                                                <label for="last_name" class="form-label required">Last Name</label>
                                                <input type="text" id="last_name" name="last_name"
                                                    class="form-input" placeholder="Doe">
                                                <div class="hidden form-error"></div>
                                            </div>
                                        </div>
                                        <p class="mt-1 text-xs text-neutral-500">Please provide your names exactly as
                                            they appear on your National ID, Passport, or Alien Registration
                                            Card.</p>
                                    </div>


                                    <!-- Contact Information -->
                                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                        <div class="form-group">
                                            <label for="email" class="form-label required">Email Address</label>
                                            <input type="email" id="email" name="email" class="form-input"
                                                placeholder="john@example.com">
                                            <div class="hidden form-error"></div>
                                            <p class="form-hint">We'll use this to send updates about your campaign</p>
                                        </div>
                                        <div class="form-group">
                                            <label for="phone" class="form-label required">Phone Number</label>
                                            <input type="tel" id="phone" name="phone" class="form-input"
                                                placeholder="+254 700 000 000">
                                            <div class="hidden form-error"></div>
                                            <p class="form-hint">For important notifications and verification</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="px-8 py-6 border-t border-neutral-100 bg-neutral-50">
                                <div class="flex justify-between">
                                    <button type="button" class="btn-secondary prev-step">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 19l-7-7 7-7" />
                                        </svg>
                                        Back
                                    </button>
                                    <button type="button" class="btn-primary next-step">
                                        Continue
                                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Financial & Verification -->
                        <div class="form-step" id="step-3">
                            <div class="p-8">
                                <div class="mb-8 text-center">
                                    <div
                                        class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl">
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                    </div>
                                    <h3 class="mb-2 text-2xl font-bold font-heading text-neutral-800">Verification &
                                        Goals
                                    </h3>
                                    <p class="text-neutral-600">Final details to verify your identity and set your
                                        fundraising goals</p>
                                </div>

                                <div class="max-w-5xl mx-auto space-y-8">
                                    <!-- Identification Section -->
                                    <div class="section-card">
                                        <h4 class="section-title">
                                            <svg class="w-5 h-5 mr-2 text-secondary-500" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 114 0v2m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                            </svg>
                                            Identity Verification
                                        </h4>

                                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                            <div class="form-group">
                                                <label for="id_type_id" class="form-label required">ID Type</label>
                                                <select id="id_type_id" name="id_type_id" class="form-input">
                                                    <option value="">Select your ID type</option>
                                                    @foreach ($idTypes as $idType)
                                                        <option value="{{ $idType->id }}"
                                                            data-type="{{ $idType->type }}">
                                                            {{ $idType->display_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="hidden form-error"></div>
                                            </div>

                                            <div class="form-group">
                                                <label for="id_number" class="form-label required">ID Number</label>
                                                <input type="text" id="id_number" name="id_number"
                                                    class="form-input" placeholder="Enter your ID number">
                                                <div class="hidden form-error"></div>
                                            </div>

                                            <div class="form-group md:col-span-2">
                                                <label for="kra_pin" class="form-label">KRA PIN (Optional)</label>
                                                <input type="text" id="kra_pin" name="kra_pin"
                                                    class="form-input" placeholder="A000000000A">
                                                <div class="hidden form-error"></div>
                                                <p class="form-hint">Providing your KRA PIN helps with verification but
                                                    is
                                                    optional</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Financial Goals Section -->
                                    <div class="section-card">
                                        <h4 class="section-title">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                class="w-5 h-5 mr-2 text-success-500">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
                                            </svg>
                                            Fundraising Goals (Optional)
                                        </h4>

                                        <div class="mb-4 info-card">
                                            <div class="flex items-start space-x-3">
                                                <div
                                                    class="w-8 h-8 bg-primary-500 rounded-lg flex items-center justify-center mt-0.5">
                                                    <svg class="w-4 h-4 text-white" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <h5 class="mb-1 font-medium text-neutral-800">Note</h5>
                                                    <p class="text-sm text-neutral-600">Setting a target amount and
                                                        date
                                                        helps donors understand your goals, but you can still receive
                                                        donations without them.</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                            <div class="form-group">
                                                <label for="target_amount" class="form-label">Target Amount
                                                    (KES)</label>
                                                <input type="number" id="target_amount" name="target_amount"
                                                    step="0.01" min="1" class="form-input"
                                                    placeholder="10,000.00">
                                                <div class="hidden form-error"></div>
                                                <p class="form-hint">Optional: Set a fundraising goal</p>
                                            </div>

                                            <div class="form-group">
                                                <label for="target_date" class="form-label">Target Date</label>
                                                <input type="date" id="target_date" name="target_date"
                                                    class="form-input" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                                <div class="hidden form-error"></div>
                                                <p class="form-hint">Optional: When you hope to reach your goal</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Payout Authorization Section -->
                                    <div class="section-card">
                                        <h4 class="section-title">
                                            <svg class="w-5 h-5 mr-2 text-purple-500" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                            </svg>
                                            Payout Authorization
                                        </h4>

                                        <div class="mandate-options">
                                            <div class="mandate-option">
                                                <input type="radio" id="mandate_single" name="mandate_type"
                                                    value="single" class="mandate-radio">
                                                <label for="mandate_single" class="mandate-label">
                                                    <div class="mandate-header">
                                                        <div
                                                            class="flex items-center justify-center w-10 h-10 bg-success-100 rounded-xl">
                                                            <svg class="w-5 h-5 text-success-600" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                            </svg>
                                                        </div>
                                                        <div>
                                                            <h5 class="font-medium text-neutral-800">Single Mandate
                                                            </h5>
                                                            <p class="text-sm text-neutral-600">You manage all payouts
                                                                yourself</p>
                                                        </div>
                                                    </div>
                                                    <p class="mandate-description">Recommended for personal fundraising
                                                        campaigns where you handle everything independently.</p>
                                                </label>
                                            </div>

                                            <div class="mandate-option">
                                                <input type="radio" id="mandate_dual" name="mandate_type"
                                                    value="dual" class="mandate-radio">
                                                <label for="mandate_dual" class="mandate-label">
                                                    <div class="mandate-header">
                                                        <div
                                                            class="flex items-center justify-center w-10 h-10 bg-primary-100 rounded-xl">
                                                            <svg class="w-5 h-5 text-primary-600" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                            </svg>
                                                        </div>
                                                        <div>
                                                            <h5 class="font-medium text-neutral-800">Dual Mandate</h5>
                                                            <p class="text-sm text-neutral-600">Another person approves
                                                                your payouts</p>
                                                        </div>
                                                    </div>
                                                    <p class="mandate-description">Good for transparency and additional
                                                        oversight. A trusted person reviews and approves your withdrawal
                                                        requests.</p>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="hidden form-error" id="mandate_type_error"></div>

                                        <!-- Checker Details (shown only for dual mandate) -->
                                        <div id="checker_details" class="hidden checker-section">
                                            <div class="mb-4 info-card">
                                                <div class="flex items-start space-x-3">
                                                    <div
                                                        class="w-8 h-8 bg-primary-500 rounded-lg flex items-center justify-center mt-0.5">
                                                        <svg class="w-4 h-4 text-white" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <h5 class="mb-1 font-medium text-neutral-800">Checker
                                                            Authorization
                                                        </h5>
                                                        <p class="text-sm text-neutral-600">The person you designate
                                                            will
                                                            receive an invitation to approve payouts for this campaign.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                                <div class="form-group">
                                                    <label for="checker_name" class="form-label required">Checker Full
                                                        Name</label>
                                                    <input type="text" id="checker_name" name="checker_name"
                                                        class="form-input" placeholder="Full name of checker">
                                                    <div class="hidden form-error" id="checker_name_error"></div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="checker_email" class="form-label required">Checker
                                                        Email</label>
                                                    <input type="email" id="checker_email" name="checker_email"
                                                        class="form-input" placeholder="checker@example.com">
                                                    <div class="hidden form-error" id="checker_email_error"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Additional Information -->
                                    <div class="form-group">
                                        <label for="additional_info" class="form-label">Additional Information</label>
                                        <textarea id="additional_info" name="additional_info" rows="3" class="resize-none form-input"
                                            placeholder="Any additional details you'd like to share..."></textarea>
                                        <div class="hidden form-error"></div>
                                        <p class="form-hint">Optional: Share any other relevant information about your
                                            campaign</p>
                                    </div>
                                </div>
                            </div>

                            <div class="px-8 py-6 border-t border-neutral-100 bg-neutral-50">
                                <div class="flex justify-between">
                                    <button type="button" class="btn-secondary prev-step">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 19l-7-7 7-7" />
                                        </svg>
                                        Back
                                    </button>
                                    <button type="button" class="btn-primary next-step">
                                        Review Application
                                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Step 4: Review & Submit -->
                        <div class="form-step" id="step-4">
                            <div class="p-8">
                                <div class="mb-8 text-center">
                                    <div
                                        class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-success-500 to-success-600 rounded-2xl">
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <h3 class="mb-2 text-2xl font-bold font-heading text-neutral-800">Review Your
                                        Application</h3>
                                    <p class="text-neutral-600">Double-check everything before submitting your
                                        fundraising
                                        campaign</p>
                                </div>

                                <!-- Review Summary -->
                                <div class="max-w-5xl mx-auto">
                                    <div class="mb-8 review-status">
                                        <div class="flex items-center justify-center space-x-2 text-success-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="font-medium">All sections completed - Ready for
                                                submission</span>
                                        </div>
                                    </div>

                                    <div id="application-review" class="space-y-6">
                                        <!-- Review content will be populated by JavaScript -->
                                    </div>
                                </div>
                            </div>

                            <div class="px-8 py-6 border-t border-neutral-100 bg-neutral-50">
                                <div class="flex justify-between">
                                    <button type="button" class="btn-secondary prev-step">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 19l-7-7 7-7" />
                                        </svg>
                                        Back
                                    </button>
                                    <button type="submit" id="submit-application" class="btn-success">
                                        <span class="submit-text">Submit Application</span>
                                        <span class="hidden loading-text">
                                            <svg class="inline w-4 h-4 mr-2 animate-spin" fill="none"
                                                viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                                    stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                </path>
                                            </svg>
                                            Submitting...
                                        </span>
                                        <svg class="w-4 h-4 ml-2 submit-icon" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('individualDonationForm');
            const steps = document.querySelectorAll('.form-step');
            const stepCircles = document.querySelectorAll('.step-circle');
            const stepLines = document.querySelectorAll('.step-line');
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

            // Initialize form
            updateStepDisplay();

            // Step navigation
            nextButtons.forEach(button => {
                button.addEventListener('click', function() {
                    if (validateCurrentStep()) {
                        if (currentStep < 4) {
                            currentStep++;
                            updateStepDisplay();
                            scrollToTop();
                        }
                    }
                });
            });

            prevButtons.forEach(button => {
                button.addEventListener('click', function() {
                    if (currentStep > 1) {
                        currentStep--;
                        updateStepDisplay();
                        scrollToTop();
                    }
                });
            });

            // Update step display with animations
            function updateStepDisplay() {
                steps.forEach((step, index) => {
                    step.classList.toggle('active', index + 1 === currentStep);
                });

                stepCircles.forEach((circle, index) => {
                    circle.classList.toggle('active', index + 1 === currentStep);
                    circle.classList.toggle('completed', index + 1 < currentStep);
                });

                stepLines.forEach((line, index) => {
                    line.classList.toggle('completed', index + 1 < currentStep);
                });

                if (currentStep === 4) {
                    populateReviewSection();
                }

                // Update page title
                const stepTitles = ['Contribution Details', 'Personal Information', 'Financial & Verification',
                    'Review & Submit'
                ];
                document.title = `${stepTitles[currentStep - 1]} - Individual Donation Application`;
            }

            // Scroll to top of form
            function scrollToTop() {
                document.querySelector('.max-w-4xl').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }

            // Handle validation errors by going to appropriate step
            function goToErrorStep(fieldName) {
                let targetStep = 1;

                // Step 1 fields
                if (['contribution_name', 'contribution_description', 'contribution_reason_id', 'support_documents']
                    .includes(fieldName)) {
                    targetStep = 1;
                }
                // Step 2 fields
                else if (['first_name', 'middle_name', 'last_name', 'email', 'phone'].includes(fieldName)) {
                    targetStep = 2;
                }
                // Step 3 fields
                else if (['id_type_id', 'id_number', 'kra_pin', 'target_amount', 'target_date', 'mandate_type',
                        'checker_name', 'checker_email', 'additional_info'
                    ].includes(fieldName)) {
                    targetStep = 3;
                }

                if (targetStep !== currentStep) {
                    currentStep = targetStep;
                    updateStepDisplay();
                    scrollToTop();
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

            // Fetch document types with better error handling
            async function fetchDocumentTypes(contributionReasonId) {
                try {
                    const baseUrl = "{{ url('individual/application/document-types') }}";
                    const response = await fetch(`${baseUrl}/${contributionReasonId}`);

                    if (!response.ok) {
                        throw new Error('Failed to fetch document types');
                    }

                    const documentTypes = await response.json();

                    let html = `
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 bg-primary-500 rounded-lg flex items-center justify-center mt-0.5">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div>
                                <h5 class="mb-2 font-medium text-neutral-800">Required Documents:</h5>
                                <ul class="space-y-1 text-sm text-neutral-600">
                    `;

                    documentTypes.forEach(type => {
                        html += `<li class="flex items-start space-x-2">
                            <span class="mt-1 text-primary-500">•</span>
                            <div>
                                <span class="font-medium">${type.display_name}</span>
                                <p class="text-xs text-neutral-500">${type.description}</p>
                            </div>
                        </li>`;
                    });

                    html += '</ul></div></div>';
                    documentTypesInfo.innerHTML = html;
                } catch (error) {
                    console.error('Error fetching document types:', error);
                    documentTypesInfo.innerHTML = `
                        <div class="text-sm text-danger-600">
                            <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Error loading document requirements. Please try again.
                        </div>
                    `;
                }
            }

            // Enhanced file upload handler
            supportDocumentsInput.addEventListener('change', function() {
                const files = Array.from(this.files);
                const maxSize = 5 * 1024 * 1024; // 5MB
                const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];

                files.forEach(file => {
                    if (file.size > maxSize) {
                        showError('support_documents',
                            `File "${file.name}" is too large. Maximum size is 5MB.`);
                        return;
                    }

                    if (!allowedTypes.includes(file.type)) {
                        showError('support_documents',
                            `File "${file.name}" is not a supported format. Please use PDF, JPG, JPEG, or PNG.`
                        );
                        return;
                    }

                    uploadedFiles.push(file);
                    hideError('support_documents');
                });

                updateUploadedFilesDisplay();
                this.value = ''; // Reset input
            });

            // Enhanced uploaded files display
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
                        <button type="button" class="remove-file" onclick="removeFile(${index})" title="Remove file">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
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

            // Enhanced mandate type handling
            mandateRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'dual') {
                        checkerDetails.classList.remove('hidden');
                        document.getElementById('checker_name').setAttribute('required',
                            'required');
                        document.getElementById('checker_email').setAttribute('required',
                            'required');
                    } else {
                        checkerDetails.classList.add('hidden');
                        document.getElementById('checker_name').removeAttribute('required');
                        document.getElementById('checker_email').removeAttribute('required');
                        document.getElementById('checker_name').value = '';
                        document.getElementById('checker_email').value = '';
                        hideError('checker_name');
                        hideError('checker_email');
                    }
                });
            });

            // Enhanced validation functions
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
                const contributionName = document.getElementById('contribution_name').value.trim();
                if (!contributionName) {
                    showError('contribution_name', 'Campaign title is required');
                    isValid = false;
                } else if (contributionName.length < 10) {
                    showError('contribution_name', 'Campaign title should be at least 10 characters long');
                    isValid = false;
                }

                // Contribution description
                const contributionDescription = document.getElementById('contribution_description').value.trim();
                if (!contributionDescription) {
                    showError('contribution_description', 'Campaign story is required');
                    isValid = false;
                } else if (contributionDescription.length < 50) {
                    showError('contribution_description',
                        'Please provide a more detailed story (at least 50 characters)');
                    isValid = false;
                }

                // Contribution reason
                if (!document.getElementById('contribution_reason_id').value) {
                    showError('contribution_reason_id', 'Please select a campaign category');
                    isValid = false;
                }

                // Check if documents are required
                const selectedOption = contributionReasonSelect.options[contributionReasonSelect.selectedIndex];
                const requiresDocument = selectedOption && selectedOption.dataset.requiresDocument === '1';

                if (requiresDocument && uploadedFiles.length === 0) {
                    showError('support_documents', 'Please upload the required supporting documents');
                    isValid = false;
                }

                return isValid;
            }

            function validateStep2() {
                let isValid = true;

                // First name
                const firstName = document.getElementById('first_name').value.trim();
                if (!firstName) {
                    showError('first_name', 'First name is required');
                    isValid = false;
                } else if (firstName.length < 2) {
                    showError('first_name', 'First name must be at least 2 characters long');
                    isValid = false;
                }

                // Last name
                const lastName = document.getElementById('last_name').value.trim();
                if (!lastName) {
                    showError('last_name', 'Last name is required');
                    isValid = false;
                } else if (lastName.length < 2) {
                    showError('last_name', 'Last name must be at least 2 characters long');
                    isValid = false;
                }

                // Email
                const email = document.getElementById('email').value.trim();
                if (!email) {
                    showError('email', 'Email address is required');
                    isValid = false;
                } else if (!isValidEmail(email)) {
                    showError('email', 'Please enter a valid email address');
                    isValid = false;
                }

                // Phone
                const phone = document.getElementById('phone').value.trim();
                if (!phone) {
                    showError('phone', 'Phone number is required');
                    isValid = false;
                } else if (!isValidPhone(phone)) {
                    showError('phone', 'Please enter a valid phone number');
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
                const idNumber = document.getElementById('id_number').value.trim();
                if (!idNumber) {
                    showError('id_number', 'ID number is required');
                    isValid = false;
                } else if (idNumber.length < 6) {
                    showError('id_number', 'Please enter a valid ID number');
                    isValid = false;
                }

                // Target amount (optional but validate if provided)
                const targetAmount = document.getElementById('target_amount').value;
                if (targetAmount && (isNaN(targetAmount) || parseFloat(targetAmount) <= 0)) {
                    showError('target_amount', 'Please enter a valid target amount');
                    isValid = false;
                }

                // Target date (optional but validate if provided)
                const targetDate = document.getElementById('target_date').value;
                if (targetDate) {
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
                        } else if (checkerName.length < 3) {
                            showError('checker_name', 'Checker name must be at least 3 characters long');
                            isValid = false;
                        }

                        if (!checkerEmail) {
                            showError('checker_email', 'Checker email is required for dual mandate');
                            isValid = false;
                        } else if (!isValidEmail(checkerEmail)) {
                            showError('checker_email', 'Please enter a valid email address for the checker');
                            isValid = false;
                        } else if (checkerEmail === document.getElementById('email').value.trim()) {
                            showError('checker_email', 'Checker email must be different from your email');
                            isValid = false;
                        }
                    }
                }

                return isValid;
            }

            // Enhanced utility functions
            function isValidEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }

            function isValidPhone(phone) {
                // Basic phone validation - adjust regex based on your requirements
                const phoneRegex = /^[\+]?[0-9\s\-\(\)]{10,}$/;
                return phoneRegex.test(phone);
            }

            function showError(fieldId, message) {
                const field = document.getElementById(fieldId);
                const errorElement = document.getElementById(fieldId + '_error') ||
                    field?.parentNode.querySelector('.form-error');

                if (field) {
                    field.classList.add('error');
                    field.classList.remove('border-neutral-300');
                }

                if (errorElement) {
                    errorElement.textContent = message;
                    errorElement.classList.remove('hidden');
                }
            }

            function hideError(fieldId) {
                const field = document.getElementById(fieldId);
                const errorElement = document.getElementById(fieldId + '_error') ||
                    field?.parentNode.querySelector('.form-error');

                if (field) {
                    field.classList.remove('error');
                    field.classList.add('border-neutral-300');
                }

                if (errorElement) {
                    errorElement.classList.add('hidden');
                    errorElement.textContent = '';
                }
            }

            function clearErrors() {
                document.querySelectorAll('.form-error').forEach(error => {
                    error.classList.add('hidden');
                    error.textContent = '';
                });
                document.querySelectorAll('.form-input').forEach(field => {
                    field.classList.remove('error');
                });
            }

            // Enhanced populate review section
            function populateReviewSection() {
                const reviewContainer = document.getElementById('application-review');
                const formData = new FormData(form);

                // Get display texts
                const contributionReasonText = contributionReasonSelect.options[contributionReasonSelect
                    .selectedIndex]?.text || '';
                const idTypeText = document.getElementById('id_type_id')?.options[document.getElementById(
                    'id_type_id')?.selectedIndex]?.text || '';

                // Build full name
                const fullName = [
                    formData.get('first_name'),
                    formData.get('middle_name'),
                    formData.get('last_name')
                ].filter(name => name && name.trim()).join(' ');

                // Get mandate type display
                const mandateType = document.querySelector('input[name="mandate_type"]:checked')?.value;
                const mandateDisplay = mandateType === 'single' ? 'Single Mandate (Self-managed)' :
                    mandateType === 'dual' ? 'Dual Mandate (Requires approval)' : 'Not selected';

                const html = `
                    <div class="review-section">
                        <h4>
                            <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Campaign Details
                        </h4>
                        <div class="space-y-3">
                            <div class="review-item">
                                <span class="review-label">Campaign Title:</span>
                                <span class="review-value">${formData.get('contribution_name') || 'Not specified'}</span>
                            </div>
                            <div class="review-item">
                                <span class="review-label">Category:</span>
                                <span class="review-value">${contributionReasonText || 'Not selected'}</span>
                            </div>
                            <div class="review-item">
                                <span class="review-label">Support Documents:</span>
                                <span class="review-value">${uploadedFiles.length} file(s) uploaded</span>
                            </div>
                        </div>
                    </div>

                    <div class="review-section">
                        <h4>
                            <svg class="w-5 h-5 text-success-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Personal Information
                        </h4>
                        <div class="space-y-3">
                            <div class="review-item">
                                <span class="review-label">Full Name:</span>
                                <span class="review-value">${fullName || 'Not specified'}</span>
                            </div>
                            <div class="review-item">
                                <span class="review-label">Email:</span>
                                <span class="review-value">${formData.get('email') || 'Not specified'}</span>
                            </div>
                            <div class="review-item">
                                <span class="review-label">Phone:</span>
                                <span class="review-value">${formData.get('phone') || 'Not specified'}</span>
                            </div>
                        </div>
                    </div>

                    <div class="review-section">
                        <h4>
                            <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            Verification & Financial Details
                        </h4>
                        <div class="space-y-3">
                            <div class="review-item">
                                <span class="review-label">ID Type:</span>
                                <span class="review-value">${idTypeText || 'Not selected'}</span>
                            </div>
                            <div class="review-item">
                                <span class="review-label">ID Number:</span>
                                <span class="review-value">${formData.get('id_number') || 'Not specified'}</span>
                            </div>
                            <div class="review-item">
                                <span class="review-label">KRA PIN:</span>
                                <span class="review-value">${formData.get('kra_pin') || 'Not provided'}</span>
                            </div>
                            <div class="review-item">
                                <span class="review-label">Target Amount:</span>
                                <span class="review-value">${formData.get('target_amount') ? `KES ${parseFloat(formData.get('target_amount')).toLocaleString()}` : 'Not set'}</span>
                            </div>
                            <div class="review-item">
                                <span class="review-label">Target Date:</span>
                                <span class="review-value">${formData.get('target_date') ? new Date(formData.get('target_date')).toLocaleDateString() : 'Not set'}</span>
                            </div>
                            <div class="review-item">
                                <span class="review-label">Payout Authorization:</span>
                                <span class="review-value">${mandateDisplay}</span>
                            </div>
                            ${mandateType === 'dual' ? `
                                                                        <div class="review-item">
                                                                            <span class="review-label">Checker Name:</span>
                                                                            <span class="review-value">${formData.get('checker_name') || 'Not specified'}</span>
                                                                        </div>
                                                                        <div class="review-item">
                                                                            <span class="review-label">Checker Email:</span>
                                                                            <span class="review-value">${formData.get('checker_email') || 'Not specified'}</span>
                                                                        </div>
                                                                    ` : ''}
                            <div class="review-item">
                                <span class="review-label">Additional Info:</span>
                                <span class="review-value">${formData.get('additional_info') || 'None provided'}</span>
                            </div>
                        </div>
                    </div>
                `;

                reviewContainer.innerHTML = html;
            }

            // Enhanced form submission with better error handling
            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                if (!validateCurrentStep()) {
                    return;
                }

                const submitText = submitButton.querySelector('.submit-text');
                const loadingText = submitButton.querySelector('.loading-text');
                const submitIcon = submitButton.querySelector('.submit-icon');

                // Show loading state
                submitButton.disabled = true;
                submitText.classList.add('hidden');
                loadingText.classList.remove('hidden');
                submitIcon.classList.add('hidden');

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
                        // Show success message briefly before redirect
                        submitText.textContent = 'Success!';
                        submitText.classList.remove('hidden');
                        loadingText.classList.add('hidden');

                        setTimeout(() => {
                            window.location.href = result.redirect_url;
                        }, 1000);
                    } else {
                        // Handle validation errors
                        if (result.errors) {
                            let firstErrorField = null;

                            Object.keys(result.errors).forEach(field => {
                                if (!firstErrorField) firstErrorField = field;
                                showError(field, result.errors[field][0]);
                            });

                            // Go to the step with the first error
                            if (firstErrorField) {
                                goToErrorStep(firstErrorField);
                            }
                        } else {
                            // Show general error message
                            alert(result.message ||
                                'An error occurred while submitting your application. Please try again.'
                            );
                        }
                    }
                } catch (error) {
                    console.error('Error submitting form:', error);
                    alert('A network error occurred. Please check your connection and try again.');
                } finally {
                    // Reset loading state
                    submitButton.disabled = false;
                    submitText.textContent = 'Submit Application';
                    submitText.classList.remove('hidden');
                    loadingText.classList.add('hidden');
                    submitIcon.classList.remove('hidden');
                }
            });

            // Add real-time validation
            const validateOnBlur = ['contribution_name', 'contribution_description', 'first_name', 'last_name',
                'email', 'phone', 'id_number'
            ];

            validateOnBlur.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field) {
                    field.addEventListener('blur', function() {
                        // Only validate if the field has some content
                        if (this.value.trim()) {
                            // Clear previous error
                            hideError(fieldId);

                            // Basic validation based on field type
                            if (fieldId === 'email' && !isValidEmail(this.value.trim())) {
                                showError(fieldId, 'Please enter a valid email address');
                            } else if (fieldId === 'phone' && !isValidPhone(this.value.trim())) {
                                showError(fieldId, 'Please enter a valid phone number');
                            }
                        }
                    });
                }
            });

            // Drag and drop for file upload
            const uploadArea = document.querySelector('.upload-area');
            if (uploadArea) {
                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                    uploadArea.addEventListener(eventName, preventDefaults, false);
                });

                function preventDefaults(e) {
                    e.preventDefault();
                    e.stopPropagation();
                }

                ['dragenter', 'dragover'].forEach(eventName => {
                    uploadArea.addEventListener(eventName, highlight, false);
                });

                ['dragleave', 'drop'].forEach(eventName => {
                    uploadArea.addEventListener(eventName, unhighlight, false);
                });

                function highlight() {
                    uploadArea.classList.add('border-primary-500', 'bg-primary-100');
                }

                function unhighlight() {
                    uploadArea.classList.remove('border-primary-500', 'bg-primary-100');
                }

                uploadArea.addEventListener('drop', handleDrop, false);

                function handleDrop(e) {
                    const dt = e.dataTransfer;
                    const files = dt.files;

                    // Add files to the input
                    const fileArray = Array.from(files);
                    const maxSize = 5 * 1024 * 1024;
                    const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];

                    fileArray.forEach(file => {
                        if (file.size > maxSize) {
                            showError('support_documents',
                                `File "${file.name}" is too large. Maximum size is 5MB.`);
                            return;
                        }

                        if (!allowedTypes.includes(file.type)) {
                            showError('support_documents',
                                `File "${file.name}" is not supported. Use PDF, JPG, JPEG, or PNG.`);
                            return;
                        }

                        uploadedFiles.push(file);
                        hideError('support_documents');
                    });

                    updateUploadedFilesDisplay();
                }
            }
        });
    </script>
</x-app-layout>
