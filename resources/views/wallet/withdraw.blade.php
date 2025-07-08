<x-app-layout>

    <div class="pt-16 bg-gradient-to-br from-neutral-50 to-neutral-100">
        <div class="relative py-8 sm:py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-6">
                <!--Header -->
                <div class="bg-gradient-to-r from-secondary-600 to-secondary-700 rounded-2xl p-6 text-white shadow-xl">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.042 21.672 13.684 16.6m0 0-2.51 2.225.569-9.47 5.227 7.917-3.286-.672Zm-7.518-.267A8.25 8.25 0 1 1 20.25 10.5M8.288 14.212A5.25 5.25 0 1 1 17.25 10.5" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-2xl font-heading font-bold">Request Withdrawal</h2>
                                <p class="text-secondary-100 mt-1">Withdraw funds from your wallet</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-secondary-200">Available Balance</div>
                            <div class="text-2xl font-bold text-white">KES {{ number_format($availableBalance, 2) }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-neutral-200 overflow-hidden">
                    <!-- Form Header -->
                    <div class="bg-gradient-to-r from-neutral-800 to-neutral-700 px-6 py-4">
                        <h3 class="text-lg font-heading font-semibold text-white flex items-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M14 2H6C4.9 2 4 2.9 4 4V20C4 21.1 4.89 22 5.99 22H18C19.1 22 20 21.1 20 20V8L14 2ZM18 20H6V4H13V9H18V20Z" />
                            </svg>
                            Withdrawal Details
                        </h3>
                        <p class="text-neutral-300 text-sm mt-1">Complete the form below to request your withdrawal</p>
                    </div>

                    <form method="POST" action="{{ route('wallet.withdraw.store') }}" id="withdrawalForm"
                        class="p-6 space-y-6">
                        @csrf

                        <!-- Amount and Method Row -->
                        <div class="grid gap-6 md:grid-cols-2">
                            <!-- Withdrawal Amount -->
                            <div class="space-y-3">
                                <label for="amount" class="block text-sm font-semibold text-neutral-800">
                                    Withdrawal Amount (KES) <span class="text-danger-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <span class="text-neutral-500 font-medium">KES</span>
                                    </div>
                                    <input type="number" id="amount" name="amount" value="{{ old('amount') }}"
                                        min="1" max="{{ $availableBalance }}" step="0.01" required
                                        class="w-full pl-16 pr-4 py-4 text-lg font-semibold border-2 border-neutral-200 rounded-xl focus:border-secondary-500 focus:ring-0 transition-colors @error('amount') border-danger-500 @enderror"
                                        placeholder="0.00">
                                </div>
                                @error('amount')
                                    <p class="text-sm text-danger-600 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M13 13H11V7H13M13 17H11V15H13M12 2A10 10 0 0 0 2 12A10 10 0 0 0 12 22A10 10 0 0 0 22 12A10 10 0 0 0 12 2Z" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                                <p class="text-sm text-neutral-500">
                                    Minimum: KES 100 • Maximum: KES
                                    {{ number_format(min($availableBalance, 50000), 2) }}
                                </p>
                            </div>

                            <!-- Withdrawal Method -->
                            <div class="space-y-3">
                                <label for="withdrawal_method" class="block text-sm font-semibold text-neutral-800">
                                    Withdrawal Method <span class="text-danger-500">*</span>
                                </label>
                                <div class="relative">
                                    <select id="withdrawal_method" name="withdrawal_method" required
                                        class="w-full px-4 py-4 border-2 border-neutral-200 rounded-xl focus:border-secondary-500 focus:ring-0 transition-colors appearance-none bg-white @error('withdrawal_method') border-danger-500 @enderror">
                                        <option value="">Select withdrawal method</option>
                                        <option value="mpesa"
                                            {{ old('withdrawal_method') === 'mpesa' ? 'selected' : '' }}>
                                            M-Pesa</option>
                                        <option value="bank_transfer"
                                            {{ old('withdrawal_method') === 'bank_transfer' ? 'selected' : '' }}>Bank
                                            Transfer</option>
                                    </select>
                                </div>
                                @error('withdrawal_method')
                                    <p class="text-sm text-danger-600 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M13 13H11V7H13M13 17H11V15H13M12 2A10 10 0 0 0 2 12A10 10 0 0 0 12 22A10 10 0 0 0 22 12A10 10 0 0 0 12 2Z" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        <!-- M-Pesa Fields -->
                        <div id="mpesa_fields" class="hidden space-y-4">
                            <div class="bg-success-50 rounded-xl p-6 border border-success-200">
                                <h4 class="font-semibold text-success-800 mb-4 flex items-center gap-2">
                                    <div class="w-8 h-8 bg-success-500 rounded-lg flex items-center justify-center">
                                        <span class="text-white font-bold text-sm">M</span>
                                    </div>
                                    M-Pesa Withdrawal Details
                                </h4>

                                <div class="space-y-3">
                                    <label for="mpesa_number" class="block text-sm font-semibold text-success-800">
                                        M-Pesa Phone Number <span class="text-danger-500">*</span>
                                    </label>
                                    <input type="tel" id="mpesa_number" name="mpesa_number"
                                        value="{{ old('mpesa_number') }}" placeholder="254XXXXXXXXX"
                                        class="w-full px-4 py-3 border-2 border-success-200 rounded-lg focus:border-success-500 focus:ring-0 transition-colors bg-white @error('mpesa_number') border-danger-500 @enderror">
                                    @error('mpesa_number')
                                        <p class="text-sm text-danger-600 flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    d="M13 13H11V7H13M13 17H11V15H13M12 2A10 10 0 0 0 2 12A10 10 0 0 0 12 22A10 10 0 0 0 22 12A10 10 0 0 0 12 2Z" />
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                    <p class="text-sm text-success-600">Enter your M-Pesa number in format 254XXXXXXXXX
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Bank Transfer Fields -->
                        <div id="bank_fields" class="hidden space-y-4">
                            <div class="bg-primary-50 rounded-xl p-6 border border-primary-200">
                                <h4 class="font-semibold text-primary-800 mb-4 flex items-center gap-2">
                                    <div class="w-8 h-8 bg-primary-500 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M5 6H23V18H5V6ZM7 8V16H21V8H7ZM1 4H3V20H1V4Z" />
                                        </svg>
                                    </div>
                                    Bank Transfer Details
                                </h4>

                                <div class="grid gap-4 md:grid-cols-2">
                                    <!-- Bank Name -->
                                    <div class="space-y-2">
                                        <label for="bank_name" class="block text-sm font-semibold text-primary-800">
                                            Bank Name <span class="text-danger-500">*</span>
                                        </label>
                                        <input type="text" id="bank_name" name="bank_name"
                                            value="{{ old('bank_name') }}"
                                            class="w-full px-4 py-3 border-2 border-primary-200 rounded-lg focus:border-primary-500 focus:ring-0 transition-colors bg-white @error('bank_name') border-danger-500 @enderror">
                                        @error('bank_name')
                                            <p class="text-sm text-danger-600 flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                    <path
                                                        d="M13 13H11V7H13M13 17H11V15H13M12 2A10 10 0 0 0 2 12A10 10 0 0 0 12 22A10 10 0 0 0 22 12A10 10 0 0 0 12 2Z" />
                                                </svg>
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>

                                    <!-- Account Number -->
                                    <div class="space-y-2">
                                        <label for="account_number"
                                            class="block text-sm font-semibold text-primary-800">
                                            Account Number <span class="text-danger-500">*</span>
                                        </label>
                                        <input type="text" id="account_number" name="account_number"
                                            value="{{ old('account_number') }}"
                                            class="w-full px-4 py-3 border-2 border-primary-200 rounded-lg focus:border-primary-500 focus:ring-0 transition-colors bg-white @error('account_number') border-danger-500 @enderror">
                                        @error('account_number')
                                            <p class="text-sm text-danger-600 flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                    <path
                                                        d="M13 13H11V7H13M13 17H11V15H13M12 2A10 10 0 0 0 2 12A10 10 0 0 0 12 22A10 10 0 0 0 22 12A10 10 0 0 0 12 2Z" />
                                                </svg>
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>

                                    <!-- Account Name -->
                                    <div class="space-y-2">
                                        <label for="account_name"
                                            class="block text-sm font-semibold text-primary-800">
                                            Account Name <span class="text-danger-500">*</span>
                                        </label>
                                        <input type="text" id="account_name" name="account_name"
                                            value="{{ old('account_name') }}"
                                            class="w-full px-4 py-3 border-2 border-primary-200 rounded-lg focus:border-primary-500 focus:ring-0 transition-colors bg-white @error('account_name') border-danger-500 @enderror">
                                        @error('account_name')
                                            <p class="text-sm text-danger-600 flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                    <path
                                                        d="M13 13H11V7H13M13 17H11V15H13M12 2A10 10 0 0 0 2 12A10 10 0 0 0 12 22A10 10 0 0 0 22 12A10 10 0 0 0 12 2Z" />
                                                </svg>
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>

                                    <!-- Branch Code -->
                                    <div class="space-y-2">
                                        <label for="branch_code" class="block text-sm font-semibold text-primary-800">
                                            Branch Code <span class="text-neutral-500">(Optional)</span>
                                        </label>
                                        <input type="text" id="branch_code" name="branch_code"
                                            value="{{ old('branch_code') }}"
                                            class="w-full px-4 py-3 border-2 border-primary-200 rounded-lg focus:border-primary-500 focus:ring-0 transition-colors bg-white @error('branch_code') border-danger-500 @enderror">
                                        @error('branch_code')
                                            <p class="text-sm text-danger-600 flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                    <path
                                                        d="M13 13H11V7H13M13 17H11V15H13M12 2A10 10 0 0 0 2 12A10 10 0 0 0 12 22A10 10 0 0 0 22 12A10 10 0 0 0 12 2Z" />
                                                </svg>
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Fee Calculation -->
                        <div id="fee_info" class="hidden">
                            <div
                                class="bg-gradient-to-r from-secondary-50 to-secondary-100 border-2 border-secondary-200 rounded-xl p-6">
                                <h4 class="font-semibold text-secondary-800 mb-4 flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M11.8 10.9C9.53 10.31 8.8 9.7 8.8 8.75C8.8 7.66 9.81 6.9 11.5 6.9C13.28 6.9 13.94 7.75 14 9H16.21C16.14 7.28 15.09 5.7 13 5.19V3H10V5.16C8.06 5.58 6.5 6.84 6.5 8.77C6.5 11.08 8.41 12.23 11.2 12.9C13.7 13.5 14.2 14.38 14.2 15.31C14.2 16 13.71 17.1 11.5 17.1C9.44 17.1 8.63 16.18 8.5 15H6.32C6.44 17.19 8.08 18.42 10 18.83V21H13V18.85C14.95 18.5 16.5 17.35 16.5 15.3C16.5 12.46 14.07 11.5 11.8 10.9Z" />
                                    </svg>
                                    Fee Breakdown
                                </h4>
                                <div id="fee_details" class="space-y-2 text-sm text-secondary-700"></div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-neutral-200">
                            <a href="{{ route('wallet.dashboard') }}"
                                class="flex-1 bg-neutral-100 hover:bg-neutral-200 text-neutral-700 px-6 py-4 rounded-xl font-semibold transition-all duration-200 flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20 11H7.83L13.42 5.41L12 4L4 12L12 20L13.41 18.59L7.83 13H20V11Z" />
                                </svg>
                                Back to Wallet
                            </a>
                            <button type="submit"
                                class="flex-1 bg-gradient-to-r from-secondary-500 to-secondary-600 hover:from-secondary-600 hover:to-secondary-700 text-white px-6 py-4 rounded-xl font-semibold transition-all duration-200 transform hover:scale-[1.02] flex items-center justify-center gap-2 shadow-md hover:shadow-lg">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M2 21L23 12L2 3V10L17 12L2 14V21Z" />
                                </svg>
                                Submit Withdrawal Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const withdrawalMethod = document.getElementById('withdrawal_method');
            const mpesaFields = document.getElementById('mpesa_fields');
            const bankFields = document.getElementById('bank_fields');
            const amountInput = document.getElementById('amount');
            const feeInfo = document.getElementById('fee_info');
            const feeDetails = document.getElementById('fee_details');

            // Show/hide fields based on withdrawal method
            withdrawalMethod.addEventListener('change', function() {
                // Hide all method fields first
                mpesaFields.classList.add('hidden');
                bankFields.classList.add('hidden');

                // Show relevant fields
                if (this.value === 'mpesa') {
                    mpesaFields.classList.remove('hidden');
                } else if (this.value === 'bank_transfer') {
                    bankFields.classList.remove('hidden');
                }

                calculateFee();
            });

            // Calculate fee when amount changes
            amountInput.addEventListener('input', calculateFee);

            function calculateFee() {
                const amount = parseFloat(amountInput.value) || 0;
                const method = withdrawalMethod.value;

                if (amount > 0 && method) {
                    let feePercentage, minFee, maxFee, methodName;

                    if (method === 'mpesa') {
                        feePercentage = 0.02; // 2%
                        minFee = 10;
                        maxFee = 100;
                        methodName = 'M-Pesa';
                    } else if (method === 'bank_transfer') {
                        feePercentage = 0.015; // 1.5%
                        minFee = 50;
                        maxFee = 500;
                        methodName = 'Bank Transfer';
                    } else {
                        feeInfo.classList.add('hidden');
                        return;
                    }

                    const calculatedFee = amount * feePercentage;
                    const actualFee = Math.max(minFee, Math.min(calculatedFee, maxFee));
                    const netAmount = amount - actualFee;

                    feeDetails.innerHTML = `
                        <div class="flex justify-between items-center p-3 bg-white/60 rounded-lg">
                            <span class="font-medium">Withdrawal Amount:</span>
                            <span class="font-bold">KES ${amount.toFixed(2)}</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-white/60 rounded-lg">
                            <span class="font-medium">${methodName} Processing Fee:</span>
                            <span class="font-bold text-secondary-600">- KES ${actualFee.toFixed(2)}</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-secondary-200 rounded-lg border-2 border-secondary-300">
                            <span class="font-bold text-secondary-800">You will receive:</span>
                            <span class="font-bold text-lg text-secondary-800">KES ${netAmount.toFixed(2)}</span>
                        </div>
                    `;
                    feeInfo.classList.remove('hidden');
                } else {
                    feeInfo.classList.add('hidden');
                }
            }

            // Trigger initial calculation if values are preset
            if (withdrawalMethod.value) {
                withdrawalMethod.dispatchEvent(new Event('change'));
            }

            // Form submission handling
            const form = document.getElementById('withdrawalForm');
            form.addEventListener('submit', function(e) {
                const submitButton = form.querySelector('button[type="submit"]');
                const originalContent = submitButton.innerHTML;

                submitButton.disabled = true;
                submitButton.innerHTML = `
                    <svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Processing Request...
                `;

                // Re-enable button after 3 seconds if form doesn't submit (for error cases)
                setTimeout(() => {
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalContent;
                }, 3000);
            });
        });
    </script>
</x-app-layout>
