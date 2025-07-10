<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="space-y-1  py-2">
                <h2 class="text-2xl font-heading font-bold text-neutral-800">
                    New Payout Method
                </h2>
                <p class="text-sm font-medium text-neutral-500">
                    Set up how you want to receive donations from your campaigns
                </p>
            </div>
            <div class="hidden sm:flex items-center space-x-2 text-sm text-neutral-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <span>Secure Setup</span>
            </div>
        </div>
    </x-slot>

    <div class="pb-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-xl border border-neutral-100 overflow-hidden">
                <!-- Header Section -->
                <div class="px-8 py-6 bg-gradient-to-r from-primary-50 to-blue-50 border-b border-neutral-100">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl font-heading font-bold text-neutral-800">Add Payout Method</h1>
                            <p class="text-sm text-neutral-600">Choose your preferred way to receive donations</p>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('payout-methods.store') }}" class="p-8">
                    @csrf

                    <!-- Payout Type Selection -->
                    <div class="mb-8">
                        <div class="text-center mb-6">
                            <h2 class="text-xl font-heading font-bold text-neutral-800 mb-2">Select Payout Type</h2>
                            <p class="text-neutral-600">Choose how you want to receive your donations</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Mobile Money Option -->
                            <div class="payout-option">
                                <input id="mobile_money" name="type" type="radio" value="mobile_money" class="payout-radio" {{ old('type', 'mobile_money') === 'mobile_money' ? 'checked' : '' }}>
                                <label for="mobile_money" class="payout-label">
                                    <div class="payout-icon bg-gradient-to-br from-success-500 to-success-600">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <div class="payout-content">
                                        <h3 class="payout-title">Mobile Money</h3>
                                        <p class="payout-description">Instant mobile payments</p>
                                        <div class="payout-features">
                                            <span class="feature-tag">⚡ Instant</span>
                                            <span class="feature-tag">📱 Mobile</span>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <!-- Bank Account Option -->
                            <div class="payout-option">
                                <input id="bank_account" name="type" type="radio" value="bank_account" class="payout-radio" {{ old('type') === 'bank_account' ? 'checked' : '' }}>
                                <label for="bank_account" class="payout-label">
                                    <div class="payout-icon bg-gradient-to-br from-primary-500 to-primary-600">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    </div>
                                    <div class="payout-content">
                                        <h3 class="payout-title">Bank Account</h3>
                                        <p class="payout-description">Traditional bank transfer</p>
                                        <div class="payout-features">
                                            <span class="feature-tag">🏦 Secure</span>
                                            <span class="feature-tag">⏱️ 1-3 days</span>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <!-- Paybill Option -->
                            <div class="payout-option">
                                <input id="paybill" name="type" type="radio" value="paybill" class="payout-radio" {{ old('type') === 'paybill' ? 'checked' : '' }}>
                                <label for="paybill" class="payout-label">
                                    <div class="payout-icon bg-gradient-to-br from-secondary-500 to-secondary-600">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <div class="payout-content">
                                        <h3 class="payout-title">Paybill</h3>
                                        <p class="payout-description">Business paybill account</p>
                                        <div class="payout-features">
                                            <span class="feature-tag">🏢 Business</span>
                                            <span class="feature-tag">💼 Professional</span>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                        
                        @error('type')
                            <p class="mt-4 text-sm text-danger-600 text-center">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Dynamic Form Fields -->
                    <div class="form-sections">
                        <!-- Mobile Money Fields -->
                        <div id="mobile_money_fields" class="form-section" style="{{ old('type', 'mobile_money') === 'mobile_money' ? 'display: block;' : 'display: none;' }}">
                            <div class="section-header">
                                <div class="w-10 h-10 bg-gradient-to-br from-success-500 to-success-600 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="section-title">Mobile Money Details</h3>
                                    <p class="section-description">Enter your mobile money account information</p>
                                </div>
                            </div>

                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="provider" class="form-label">Provider</label>
                                    <select id="provider" name="provider" class="form-select">
                                        <option value="">Select your mobile money provider</option>
                                        <option value="M-Pesa" {{ old('provider') === 'M-Pesa' ? 'selected' : '' }}>
                                            Safaricom M-Pesa
                                        </option>
                                    </select>
                                    @error('provider')
                                        <p class="form-error">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="mobile_account_number" class="form-label">Phone Number</label>
                                    <input type="text" id="mobile_account_number" name="account_number" value="{{ old('account_number') }}" placeholder="0712345678" class="form-input">
                                    @error('account_number')
                                        <p class="form-error">{{ $message }}</p>
                                    @enderror
                                    <p class="form-hint">Enter your mobile number registered with the provider</p>
                                </div>

                                <div class="form-group col-span-2">
                                    <label for="mobile_account_name" class="form-label">Account Name</label>
                                    <input type="text" id="mobile_account_name" name="account_name" value="{{ old('account_name') }}" placeholder="Name as registered with mobile money provider" class="form-input">
                                    @error('account_name')
                                        <p class="form-error">{{ $message }}</p>
                                    @enderror
                                    <p class="form-hint">This must match the name on your mobile money account</p>
                                </div>
                            </div>
                        </div>

                        <!-- Bank Account Fields -->
                        <div id="bank_account_fields" class="form-section" style="display: none;">
                            <div class="section-header">
                                <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="section-title">Bank Account Details</h3>
                                    <p class="section-description">Enter your bank account information</p>
                                </div>
                            </div>

                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="bank_id" class="form-label">Bank Name</label>
                                    <select id="bank_id" name="bank_id" class="form-select">
                                        <option value="">Select your bank</option>
                                        @foreach ($banks as $bank)
                                            <option value="{{ $bank->id }}" {{ old('bank_id') == $bank->id ? 'selected' : '' }}>
                                                {{ $bank->display_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('bank_id')
                                        <p class="form-error">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="bank_account_number" class="form-label">Account Number</label>
                                    <input type="text" id="bank_account_number" name="account_number" value="{{ old('account_number') }}" placeholder="Your bank account number" class="form-input">
                                    @error('account_number')
                                        <p class="form-error">{{ $message }}</p>
                                    @enderror
                                    <p class="form-hint">Enter your complete bank account number</p>
                                </div>

                                <div class="form-group col-span-2">
                                    <label for="bank_account_name" class="form-label">Account Name</label>
                                    <input type="text" id="bank_account_name" name="account_name" value="{{ old('account_name') }}" placeholder="Account holder name" class="form-input">
                                    @error('account_name')
                                        <p class="form-error">{{ $message }}</p>
                                    @enderror
                                    <p class="form-hint">This must match the name on your bank account</p>
                                </div>
                            </div>
                        </div>

                        <!-- Paybill Fields -->
                        <div id="paybill_fields" class="form-section" style="display: none;">
                            <div class="section-header">
                                <div class="w-10 h-10 bg-gradient-to-br from-secondary-500 to-secondary-600 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="section-title">Paybill Details</h3>
                                    <p class="section-description">Enter your paybill account information</p>
                                </div>
                            </div>

                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="paybill_number" class="form-label">Paybill Number</label>
                                    <input type="text" id="paybill_number" name="paybill_number" value="{{ old('paybill_number') }}" placeholder="400200" class="form-input">
                                    @error('paybill_number')
                                        <p class="form-error">{{ $message }}</p>
                                    @enderror
                                    <p class="form-hint">Your business paybill number</p>
                                </div>

                                <div class="form-group">
                                    <label for="paybill_account_number" class="form-label">Account Number</label>
                                    <input type="text" id="paybill_account_number" name="account_number" value="{{ old('account_number') }}" placeholder="Your account number for this paybill" class="form-input">
                                    @error('account_number')
                                        <p class="form-error">{{ $message }}</p>
                                    @enderror
                                    <p class="form-hint">Your unique account number for this paybill</p>
                                </div>

                                <div class="form-group">
                                    <label for="paybill_account_name" class="form-label">Account Name</label>
                                    <input type="text" id="paybill_account_name" name="paybill_account_name" value="{{ old('paybill_account_name') }}" placeholder="Account holder name" class="form-input">
                                    @error('paybill_account_name')
                                        <p class="form-error">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="paybill_provider" class="form-label">Provider</label>
                                    <select id="paybill_provider" name="provider" class="form-select">
                                        <option value="">Select provider</option>
                                        <option value="M-Pesa" {{ old('provider') === 'M-Pesa' ? 'selected' : '' }}>Safaricom M-Pesa</option>
                                        <option value="Airtel Money" {{ old('provider') === 'Airtel Money' ? 'selected' : '' }}>Airtel Money</option>
                                        <option value="T-Kash" {{ old('provider') === 'T-Kash' ? 'selected' : '' }}>T-Kash</option>
                                    </select>
                                    @error('provider')
                                        <p class="form-error">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="form-group col-span-2">
                                    <label for="paybill_description" class="form-label">Description (Optional)</label>
                                    <textarea id="paybill_description" name="paybill_description" rows="3" placeholder="Additional information about this paybill account" class="form-textarea">{{ old('paybill_description') }}</textarea>
                                    @error('paybill_description')
                                        <p class="form-error">{{ $message }}</p>
                                    @enderror
                                    <p class="form-hint">Any additional details about this paybill account</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Primary Method Setting -->
                    <div class="primary-section">
                        <div class="primary-checkbox">
                            <input id="is_primary" name="is_primary" type="checkbox" value="1" {{ old('is_primary') ? 'checked' : '' }} class="checkbox-input">
                            <label for="is_primary" class="checkbox-label">
                                <div class="checkbox-content">
                                    <div class="checkbox-header">
                                        <svg class="w-5 h-5 text-secondary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                                        </svg>
                                        <span class="checkbox-title">Set as Primary Payout Method</span>
                                    </div>
                                    <p class="checkbox-description">This will be your default method for receiving donations from all campaigns</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <a href="{{ route('payout-methods.index') }}" class="btn-secondary">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Cancel
                        </a>
                        <button type="submit" class="btn-primary">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Add Payout Method
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeRadios = document.querySelectorAll('input[name="type"]');
            const mobileFields = document.getElementById('mobile_money_fields');
            const bankFields = document.getElementById('bank_account_fields');
            const paybillFields = document.getElementById('paybill_fields');

            function toggleFields() {
                const selectedType = document.querySelector('input[name="type"]:checked');

                // Hide all fields first with smooth animation
                [mobileFields, bankFields, paybillFields].forEach(container => {
                    container.style.display = 'none';
                    container.style.opacity = '0';
                    container.style.transform = 'translateY(20px)';
                });

                // Disable all fields
                [mobileFields, bankFields, paybillFields].forEach(container => {
                    container.querySelectorAll('input, select, textarea').forEach(field => {
                        field.disabled = true;
                    });
                });

                // Show and enable appropriate fields based on selection
                let targetContainer = null;
                
                if (selectedType?.value === 'mobile_money') {
                    targetContainer = mobileFields;
                    clearFields([bankFields, paybillFields]);
                } else if (selectedType?.value === 'bank_account') {
                    targetContainer = bankFields;
                    clearFields([mobileFields, paybillFields]);
                } else if (selectedType?.value === 'paybill') {
                    targetContainer = paybillFields;
                    clearFields([mobileFields, bankFields]);
                }

                // Animate in the selected container
                if (targetContainer) {
                    targetContainer.style.display = 'block';
                    targetContainer.querySelectorAll('input, select, textarea').forEach(field => {
                        field.disabled = false;
                    });
                    
                    // Smooth animation
                    setTimeout(() => {
                        targetContainer.style.opacity = '1';
                        targetContainer.style.transform = 'translateY(0)';
                        targetContainer.style.transition = 'all 0.3s ease-out';
                    }, 10);
                }
            }

            function clearFields(containers) {
                containers.forEach(container => {
                    container.querySelectorAll('input, select, textarea').forEach(field => {
                        if (field.type === 'checkbox' || field.type === 'radio') {
                            field.checked = false;
                        } else {
                            field.value = '';
                        }
                    });
                });
            }

            // Add visual feedback for form validation
            function addValidationFeedback() {
                const inputs = document.querySelectorAll('input, select, textarea');
                inputs.forEach(input => {
                    input.addEventListener('blur', function() {
                        if (this.value.trim() && !this.disabled) {
                            this.classList.add('border-success-300');
                            this.classList.remove('border-neutral-300');
                        } else if (this.hasAttribute('required') && !this.value.trim()) {
                            this.classList.add('border-danger-300');
                            this.classList.remove('border-neutral-300', 'border-success-300');
                        } else {
                            this.classList.remove('border-success-300', 'border-danger-300');
                            this.classList.add('border-neutral-300');
                        }
                    });

                    input.addEventListener('input', function() {
                        if (this.classList.contains('border-danger-300') && this.value.trim()) {
                            this.classList.remove('border-danger-300');
                            this.classList.add('border-neutral-300');
                        }
                    });
                });
            }

            // Phone number formatting for mobile money
            function formatPhoneNumber() {
                const phoneInput = document.getElementById('mobile_account_number');
                if (phoneInput) {
                    phoneInput.addEventListener('input', function() {
                        let value = this.value.replace(/\D/g, ''); // Remove non-digits
                        
                        // Add Kenya country code if missing
                        if (value.length > 0 && value.charAt(0) === '7') {
                            value = '254' + value;
                        } else if (value.length > 0 && value.charAt(0) === '0') {
                            value = '254' + value.substring(1);
                        }
                        
                        // Format as: +254 XXX XXX XXX
                        if (value.length >= 12) {
                            value = value.substring(0, 12);
                            this.value = '+' + value.substring(0, 3) + ' ' + value.substring(3, 6) + ' ' + value.substring(6, 9) + ' ' + value.substring(9);
                        } else if (value.length >= 9) {
                            this.value = '+' + value.substring(0, 3) + ' ' + value.substring(3, 6) + ' ' + value.substring(6, 9) + ' ' + value.substring(9);
                        } else if (value.length >= 6) {
                            this.value = '+' + value.substring(0, 3) + ' ' + value.substring(3, 6) + ' ' + value.substring(6);
                        } else if (value.length >= 3) {
                            this.value = '+' + value.substring(0, 3) + ' ' + value.substring(3);
                        } else if (value.length > 0) {
                            this.value = '+' + value;
                        }
                    });
                }
            }

            // Bank account number validation
            function validateBankAccount() {
                const bankAccountInput = document.getElementById('bank_account_number');
                if (bankAccountInput) {
                    bankAccountInput.addEventListener('input', function() {
                        // Remove non-digits
                        this.value = this.value.replace(/\D/g, '');
                        
                        // Basic validation for account number length
                        if (this.value.length < 6) {
                            this.setCustomValidity('Account number must be at least 6 digits');
                        } else if (this.value.length > 20) {
                            this.setCustomValidity('Account number cannot exceed 20 digits');
                        } else {
                            this.setCustomValidity('');
                        }
                    });
                }
            }

            // Paybill number validation
            function validatePaybill() {
                const paybillInput = document.getElementById('paybill_number');
                if (paybillInput) {
                    paybillInput.addEventListener('input', function() {
                        // Remove non-digits
                        this.value = this.value.replace(/\D/g, '');
                        
                        // Paybill numbers are typically 5-7 digits
                        if (this.value.length < 5) {
                            this.setCustomValidity('Paybill number must be at least 5 digits');
                        } else if (this.value.length > 7) {
                            this.setCustomValidity('Paybill number cannot exceed 7 digits');
                        } else {
                            this.setCustomValidity('');
                        }
                    });
                }
            }

            // Form submission with loading state
            function handleFormSubmission() {
                const form = document.querySelector('form');
                const submitButton = document.querySelector('button[type="submit"]');
                const submitText = submitButton.querySelector('svg + span') || submitButton.childNodes[submitButton.childNodes.length - 1];
                
                form.addEventListener('submit', function(e) {
                    // Show loading state
                    submitButton.disabled = true;
                    submitButton.classList.add('opacity-75', 'cursor-not-allowed');
                    
                    // Change button text
                    const originalText = submitText.textContent;
                    submitText.textContent = 'Adding Method...';
                    
                    // Add spinner
                    const spinner = document.createElement('svg');
                    spinner.className = 'animate-spin w-4 h-4 mr-2';
                    spinner.innerHTML = `
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    `;
                    
                    // Replace the existing icon
                    const existingIcon = submitButton.querySelector('svg');
                    if (existingIcon) {
                        existingIcon.replaceWith(spinner);
                    }
                    
                    // Reset if form submission fails (for demo purposes)
                    setTimeout(() => {
                        if (submitButton.disabled) {
                            submitButton.disabled = false;
                            submitButton.classList.remove('opacity-75', 'cursor-not-allowed');
                            submitText.textContent = originalText;
                            
                            // Restore original icon
                            const checkIcon = document.createElement('svg');
                            checkIcon.className = 'w-4 h-4 mr-2';
                            checkIcon.innerHTML = `
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            `;
                            checkIcon.setAttribute('fill', 'none');
                            checkIcon.setAttribute('stroke', 'currentColor');
                            checkIcon.setAttribute('viewBox', '0 0 24 24');
                            
                            spinner.replaceWith(checkIcon);
                        }
                    }, 10000); // 10 second timeout
                });
            }

            // Initialize all functions
            typeRadios.forEach(radio => radio.addEventListener('change', toggleFields));
            toggleFields(); // Initialize on load
            addValidationFeedback();
            formatPhoneNumber();
            validateBankAccount();
            validatePaybill();
            handleFormSubmission();

            // Add smooth scroll to form sections when they become visible
            typeRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    setTimeout(() => {
                        const activeSection = document.querySelector('.form-section[style*="display: block"]');
                        if (activeSection) {
                            activeSection.scrollIntoView({
                                behavior: 'smooth',
                                block: 'nearest'
                            });
                        }
                    }, 100);
                });
            });

            // Add hover effects for better interactivity
            const payoutOptions = document.querySelectorAll('.payout-option');
            payoutOptions.forEach(option => {
                const label = option.querySelector('.payout-label');
                const icon = option.querySelector('.payout-icon');
                
                label.addEventListener('mouseenter', function() {
                    icon.style.transform = 'scale(1.1)';
                    icon.style.transition = 'transform 0.2s ease';
                });
                
                label.addEventListener('mouseleave', function() {
                    icon.style.transform = 'scale(1)';
                });
            });

            // Add real-time validation feedback
            const requiredFields = document.querySelectorAll('input[required], select[required]');
            requiredFields.forEach(field => {
                field.addEventListener('input', function() {
                    const errorElement = this.parentNode.querySelector('.form-error');
                    if (errorElement && this.value.trim()) {
                        errorElement.style.display = 'none';
                    }
                });
            });
        });
    </script>
</x-app-layout>