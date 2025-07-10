@extends('layouts.donation')

@section('title', $application->applicant->contribution_name ?? 'Support Our Cause')

@section('content')
    <div class="relative">
        <!-- Header Section -->
        <header class="bg-gradient-to-r from-primary-500 to-primary-600 text-white p-6 sm:p-8">
            <div class="text-center">
                <h1 class="text-2xl sm:text-3xl font-heading font-bold mb-2">Make a Donation</h1>
                <p class="text-primary-100 text-sm sm:text-base">Your support creates lasting impact. Thank you for caring!
                </p>
            </div>
        </header>

        <div class="p-6 sm:p-8 space-y-8">
            <!-- Beneficiary Section -->
            <section class="bg-gradient-to-br from-neutral-50 to-white rounded-2xl p-6 border border-neutral-200 shadow-sm">
                <div class="grid gap-6 lg:grid-cols-3">
                    <!-- Beneficiary Photo -->
                    <div class="lg:col-span-1">
                        <div class="relative">
                            <div
                                class="aspect-square rounded-2xl overflow-hidden bg-gradient-to-br from-neutral-100 to-neutral-200 shadow-lg">
                                @if (isset($application->applicant->photo_url))
                                    <img src="{{ $application->applicant->photo_url }}"
                                        alt="{{ $application->applicant->contribution_name ?? 'Beneficiary' }}"
                                        class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-16 h-16 text-neutral-400" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M12 12C14.21 12 16 10.21 16 8C16 5.79 14.21 4 12 4C9.79 4 8 5.79 8 8C8 10.21 9.79 12 12 12ZM12 14C9.33 14 4 15.34 4 18V20H20V18C20 15.34 14.67 14 12 14Z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <!-- Verification Badge -->
                            <div class="absolute -top-2 -right-2 bg-success-500 text-white rounded-full p-2 shadow-lg">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M9 16.17L4.83 12L3.41 13.41L9 19L21 7L19.59 5.59L9 16.17Z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Beneficiary Details -->
                    <div class="lg:col-span-2 space-y-4">
                        <div>
                            <h2 class="text-xl font-heading font-bold text-neutral-800 mb-2">
                                {{ $application->applicant->contribution_name ?? 'General Donation' }}
                            </h2>
                            <p class="text-neutral-600 leading-relaxed">
                                {{ $application->applicant->contribution_description ?? 'Your generous contribution will make a meaningful difference in supporting this important cause.' }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Progress Section -->
            <section class="bg-gradient-to-r from-success-50 to-primary-50 rounded-2xl p-6 border border-success-200">
                <div class="text-center mb-6">
                    <h3 class="text-lg font-heading font-semibold text-neutral-800 mb-2">Fundraising Progress</h3>
                    <div class="flex items-center justify-center gap-4 text-sm text-neutral-600">
                        <span class="flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor"class="w-4 h-4 text-success-500">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                            </svg>
                            KES {{ $contributionStats['total_raised_formatted'] }} raised
                        </span>
                        <span class="text-neutral-400">•</span>
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4 text-primary-500" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M16 4C18.2 4 20 5.8 20 8C20 10.2 18.2 12 16 12C13.8 12 12 10.2 12 8C12 5.8 13.8 4 16 4ZM16 14C18.7 14 24 15.3 24 18V20H8V18C8 15.3 13.3 14 16 14ZM8 12C10.2 12 12 10.2 12 8C12 5.8 10.2 4 8 4C5.8 4 4 5.8 4 8C4 10.2 5.8 12 8 12ZM8 14C5.3 14 0 15.3 0 18V20H6V18C6 16.4 6.7 15.1 7.6 14.1C7.1 14 6.6 14 8 14Z" />
                            </svg>
                            {{ $contributionStats['total_contributors'] }} contributors
                        </span>
                        <span class="text-neutral-400">•</span>
                        @if ($contributionStats['has_target'])
                            <span class="flex items-center gap-1 text-sm text-gray-600">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M12,4A8,8 0 0,1 20,12A8,8 0 0,1 12,20A8,8 0 0,1 4,12A8,8 0 0,1 12,4M12,6A6,6 0 0,0 6,12A6,6 0 0,0 12,18A6,6 0 0,0 18,12A6,6 0 0,0 12,6M12,8A4,4 0 0,1 16,12A4,4 0 0,1 12,16A4,4 0 0,1 8,12A4,4 0 0,1 12,8Z" />
                                </svg>
                                Goal: KES {{ $contributionStats['target_amount_formatted'] }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex justify-between items-center text-sm">
                        <span class="font-medium text-neutral-700">Progress</span>
                        <span class="font-bold text-primary-600">{{ number_format($progressPercentage, 1) }}%</span>
                    </div>

                    <div class="relative h-4 bg-white rounded-full overflow-hidden shadow-inner border border-neutral-200">
                        <div class="absolute inset-0 bg-gradient-to-r from-success-400 to-primary-500 rounded-full transition-all duration-1000 ease-out"
                            style="width: {{ $progressPercentage }}%">
                            <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
                        </div>
                    </div>

                    {{-- Progress Description --}}
                    <div class="text-xs text-gray-500 text-center">
                        @if ($contributionStats['progress_type'] === 'target_based')
                            @if ($contributionStats['target_reached'])
                                🎉 Target reached!
                            @elseif($contributionStats['remaining_to_target'] > 0)
                                KES {{ $contributionStats['remaining_to_target_formatted'] }} remaining to reach goal
                            @endif
                        @else
                            @if ($contributionStats['total_contributors'] === 0)
                                Be the first to contribute!
                            @elseif($contributionStats['total_contributors'] < 10)
                                Building momentum with {{ $contributionStats['total_contributors'] }}
                                contribution{{ $contributionStats['total_contributors'] !== 1 ? 's' : '' }}
                            @elseif($contributionStats['total_contributors'] < 50)
                                Great progress with {{ $contributionStats['total_contributors'] }} contributors!
                            @else
                                Amazing support from {{ $contributionStats['total_contributors'] }} contributors!
                            @endif
                        @endif
                    </div>
                </div>
            </section>

            <!-- Donation Form -->
            <section class="bg-white rounded-2xl border border-neutral-200 shadow-sm overflow-hidden">
                <div class="bg-gradient-to-r from-neutral-800 to-neutral-700 text-white p-6">
                    <h3 class="text-xl font-heading font-bold flex items-center gap-2">
                        Complete Your Donation
                    </h3>
                    <p class="text-neutral-300 mt-1">Choose your preferred payment method and amount</p>
                </div>

                <form action="{{ route('donation.process', $donationLink->code) }}" method="POST" id="donation-form"
                    class="p-6 space-y-6">
                    @csrf

                    <!-- Donation Amount -->
                    <div class="space-y-3">
                        <label for="amount" class="block text-sm font-semibold text-neutral-800">
                            Donation Amount <span class="text-danger-500">*</span>
                        </label>
                        <div class="flex gap-4">
                            <!-- Currency Selector -->
                            <div class="w-24">
                                <select name="currency" id="currency"
                                    class="w-full px-3 py-4 border-2 border-neutral-200 rounded-xl focus:border-primary-500 focus:ring-0 transition-colors text-lg font-semibold bg-white @error('currency') border-danger-500 @enderror">
                                    <option value="KES" {{ old('currency', 'KES') === 'KES' ? 'selected' : '' }}>KES
                                    </option>
                                    <option value="USD" {{ old('currency') === 'USD' ? 'selected' : '' }}>USD</option>
                                </select>
                                @error('currency')
                                    <p class="text-sm text-danger-600 flex items-center gap-1 mt-1">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                            <!-- Amount Input -->
                            <div class="flex-1">
                                <input type="number" id="amount" name="amount" value="{{ old('amount') }}"
                                    min="1" step="0.01" required
                                    class="w-full px-4 py-4 text-lg font-semibold border-2 border-neutral-200 rounded-xl focus:border-primary-500 focus:ring-0 transition-colors @error('amount') border-danger-500 @enderror"
                                    placeholder="0.00">
                                @error('amount')
                                    <p class="text-sm text-danger-600 flex items-center gap-1 mt-1">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Donation Type -->
                    <div class="space-y-3">
                        <label for="donation_type">
                            Donate as
                        </label>
                        <div class="relative">
                            <select id="donation_type" name="donation_type"
                                class="w-full px-4 py-4 border-2 border-neutral-200 rounded-xl focus:border-primary-500 focus:ring-0 transition-colors">
                                <option value="anonymous" {{ old('donation_type') === 'anonymous' ? 'selected' : '' }}>
                                    Anonymous</option>
                                <option value="family" {{ old('donation_type') === 'family' ? 'selected' : '' }}>Family
                                    Member</option>
                                <option value="friend" {{ old('donation_type') === 'friend' ? 'selected' : '' }}>Friend
                                </option>
                                <option value="colleague" {{ old('donation_type') === 'colleague' ? 'selected' : '' }}>
                                    Colleague</option>
                                <option value="supporter" {{ old('donation_type') === 'supporter' ? 'selected' : '' }}>
                                    Supporter</option>
                                <option value="other" {{ old('donation_type') === 'other' ? 'selected' : '' }}>
                                    Other</option>
                            </select>
                        </div>
                    </div>

                    <!-- Email Field -->
                    <div class="space-y-3">
                        <label for="email">
                            Email Address <span class="text-danger-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                class="w-full px-4 py-4 border-2 border-neutral-200 rounded-xl focus:border-primary-500 focus:ring-0 transition-colors @error('email') border-danger-500 @enderror"
                                placeholder="your@email.com">
                        </div>
                        @error('email')
                            <p class="text-sm text-danger-600 flex items-center gap-1">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Phone Field  -->
                    <div class="space-y-3">
                        <label for="phone">
                            Phone Number <span class="text-danger-500" id="phone-required"
                                style="display: none;">*</span>
                        </label>
                        <div class="relative">
                            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                                class="w-full px-4 py-4 border-2 border-neutral-200 rounded-xl focus:border-primary-500 focus:ring-0 transition-colors @error('phone') border-danger-500 @enderror"
                                placeholder="+254700000000">
                        </div>
                        @error('phone')
                            <p class="text-sm text-danger-600 flex items-center gap-1">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Payment Method Selection -->
                    <div class="space-y-4">
                        <h4 class="text-sm font-semibold text-neutral-800">Payment Method <span
                                class="text-danger-500">*</span></h4>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <!-- M-Pesa Option -->
                            <div class="payment-method-card" data-method="mpesa">
                                <input type="radio" id="mpesa" name="payment_method" value="mpesa"
                                    class="sr-only" {{ old('payment_method') === 'mpesa' ? 'checked' : '' }} required>
                                <label for="mpesa"
                                    class="payment-method-label block w-full p-4 border-2 border-neutral-200 rounded-xl cursor-pointer transition-all hover:border-primary-300 hover:shadow-md">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div>
                                                <h5 class="font-semibold text-neutral-800">M-Pesa</h5>
                                                <p class="text-sm text-neutral-600">Mobile money payment</p>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <!-- Card Option -->
                            <div class="payment-method-card" data-method="card">
                                <input type="radio" id="card" name="payment_method" value="card"
                                    class="sr-only" {{ old('payment_method') === 'card' ? 'checked' : '' }} required>
                                <label for="card"
                                    class="payment-method-label block w-full p-4 border-2 border-neutral-200 rounded-xl cursor-pointer transition-all hover:border-primary-300 hover:shadow-md">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div>
                                                <h5 class="font-semibold text-neutral-800">Debit/Credit Card</h5>
                                                <p class="text-sm text-neutral-600">Visa or Mastercard</p>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        @error('payment_method')
                            <p class="text-sm text-danger-600 flex items-center gap-1">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- M-Pesa Payment Options (shown when M-Pesa is selected) -->
                    {{-- <div id="mpesa-options" style="display: none;">
                        @if ($stkPushEnabled && $paybillEnabled)
                            <div class="card border-success mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="fas fa-mobile-alt text-success me-2"></i>
                                        Choose M-Pesa Payment Method
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="radio" name="mpesa_method"
                                            id="stk_push" value="stk_push" checked>
                                        <label class="form-check-label" for="stk_push">
                                            <strong>STK Push (Recommended)</strong>
                                            <br><small class="text-muted">Instant payment via phone prompt</small>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="mpesa_method"
                                            id="paybill" value="paybill">
                                        <label class="form-check-label" for="paybill">
                                            <strong>Paybill Payment</strong>
                                            <br><small class="text-muted">Manual payment using paybill number</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($paybillEnabled && $paybillDetails)
                            <!-- Paybill Details (shown when paybill option is selected) -->
                            <div id="paybill-details" style="display: none;">
                                <div class="card border-info mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">
                                            <i class="fas fa-receipt text-info me-2"></i>
                                            Paybill Payment Details
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="bg-light p-3 rounded mb-3">
                                                    <div class="text-center">
                                                        <h5 class="text-primary">{{ $paybillDetails['paybill_number'] }}
                                                        </h5>
                                                        <small class="text-muted">Paybill Number</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="bg-light p-3 rounded mb-3">
                                                    <div class="text-center">
                                                        <h6 class="text-primary">{{ $paybillDetails['account_number'] }}
                                                        </h6>
                                                        <small class="text-muted">Account Number</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            <strong>Please enter the exact details below to confirm your payment:</strong>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="paybill_account_number" class="form-label">
                                                        Account Number <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text"
                                                        class="form-control @error('paybill_account_number') is-invalid @enderror"
                                                        id="paybill_account_number" name="paybill_account_number"
                                                        value="{{ old('paybill_account_number') }}"
                                                        placeholder="{{ $paybillDetails['account_number'] }}">
                                                    <small class="text-muted">Enter:
                                                        {{ $paybillDetails['account_number'] }}</small>
                                                    @error('paybill_account_number')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="paybill_account_name" class="form-label">
                                                        Account Name <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text"
                                                        class="form-control @error('paybill_account_name') is-invalid @enderror"
                                                        id="paybill_account_name" name="paybill_account_name"
                                                        value="{{ old('paybill_account_name') }}"
                                                        placeholder="{{ $paybillDetails['account_name'] }}">
                                                    <small class="text-muted">Enter:
                                                        {{ $paybillDetails['account_name'] }}</small>
                                                    @error('paybill_account_name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div> --}}

                    <!-- Submit Button -->
                    <div class="pt-6 border-t border-neutral-200">
                        <button type="submit" id="submit-btn"
                            class="w-full bg-primary-600 text-white py-4 px-6 rounded-xl font-semibold hover:bg-primary-700 transition-colors">
                            <span class="flex items-center justify-center gap-2">
                                <span id="submit-text">Complete Donation</span>
                            </span>
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </div>

    <style>
        .payment-method-card input:checked+.payment-method-label {
            border-color: #3B82F6;
            /* primary-500 */
            background-color: #EFF6FF;
            /* primary-50 */
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            /* primary-500 with opacity */
        }

        .payment-method-card input:checked+.payment-method-label[for="mpesa"] {
            @apply border-success-500 bg-success-50;
        }

        .payment-method-card input:checked+.payment-method-label .payment-check {
            @apply block;
        }

        .mpesa-method-label input:checked+label {
            @apply border-success-500 bg-success-100;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // DOM Elements
            const elements = {
                paymentMethods: document.querySelectorAll('input[name="payment_method"]'),
                currencySelect: document.getElementById('currency'),
                amountInput: document.getElementById('amount'),
                amountHint: document.getElementById('amount-hint'),
                phoneInput: document.getElementById('phone'),
                phoneRequired: document.getElementById('phone-required'),
                phoneHint: document.getElementById('phone-hint'),
                mpesaOptions: document.getElementById('mpesa-options'),
                paybillDetails: document.getElementById('paybill-details'),
                submitBtn: document.getElementById('submit-btn'),
                submitText: document.getElementById('submit-text'),
                form: document.getElementById('donation-form')
            };

            // Heroicons
            const icons = {
                mobile: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>`,
                creditCard: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>`,
                heart: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>`,
                loading: `<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>`
            };

            // Update submit button
            function updateSubmitButton() {
                const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
                const submitContainer = elements.submitBtn.querySelector('.flex');

                const config = selectedMethod?.value === 'mpesa' ? {
                        icon: icons.mobile,
                        text: 'Continue with M-Pesa'
                    } :
                    selectedMethod?.value === 'card' ? {
                        icon: icons.creditCard,
                        text: 'Proceed to Card Payment'
                    } : {
                        icon: icons.heart,
                        text: 'Complete Donation'
                    };

                submitContainer.innerHTML = `${config.icon}<span id="submit-text">${config.text}</span>`;
            }

            // Update amount hint based on currency
            function updateAmountHint() {
                elements.amountHint.textContent = elements.currencySelect.value === 'KES' ? 'Minimum: KES 1' :
                    'Minimum: $1';
            }

            // Format Kenyan phone number
            function formatKenyanPhone() {
                if (elements.phoneInput.value && document.querySelector('input[name="payment_method"]:checked')
                    ?.value === 'mpesa') {
                    let value = elements.phoneInput.value.replace(/\D/g, '');
                    if (value.startsWith('0')) value = '254' + value.substring(1);
                    else if (value.length === 9) value = '254' + value;
                    if (value.length >= 12) {
                        elements.phoneInput.value =
                            `+${value.slice(0,3)} ${value.slice(3,6)} ${value.slice(6,9)} ${value.slice(9,12)}`;
                    }
                }
            }

            // Handle M-Pesa selection
            function handleMpesaSelection() {
                elements.currencySelect.value = 'KES';
                elements.currencySelect.disabled = true;
                elements.phoneRequired.style.display = 'inline';
                elements.phoneInput.required = true;
                elements.phoneHint.textContent = 'Required for M-Pesa payments (Kenyan number)';
                if (elements.mpesaOptions) elements.mpesaOptions.style.display = 'block';
                if (elements.paybillDetails) elements.paybillDetails.style.display = 'none';
                elements.amountHint.textContent = 'Minimum: KES 1';
                updateSubmitButton();
            }

            // Handle card selection
            function handleCardSelection() {
                elements.currencySelect.disabled = false;
                elements.phoneRequired.style.display = 'inline';
                elements.phoneInput.required = true;
                elements.phoneHint.textContent = 'Required for card payments';
                if (elements.mpesaOptions) elements.mpesaOptions.style.display = 'none';
                if (elements.paybillDetails) elements.paybillDetails.style.display = 'none';
                updateAmountHint();
                updateSubmitButton();
            }

            // Update form based on payment method
            function updateFormForPaymentMethod() {
                document.querySelectorAll('.payment-method-card').forEach(card =>
                    card.classList.toggle('selected', card.querySelector('input').checked)
                );

                const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
                if (selectedMethod?.value === 'mpesa') handleMpesaSelection();
                else if (selectedMethod?.value === 'card') handleCardSelection();
                else updateSubmitButton();
            }

            // Handle form submission
            function handleFormSubmit(e) {
                e.preventDefault();
                elements.submitBtn.disabled = true;
                elements.submitText.style.display = 'none';
                elements.submitBtn.querySelector('.flex').innerHTML = `${icons.loading}<span>Loading...</span>`;
                elements.form.submit();
            }

            // Event listeners
            elements.paymentMethods.forEach(method =>
                method.addEventListener('change', updateFormForPaymentMethod)
            );
            elements.phoneInput.addEventListener('blur', formatKenyanPhone);
            elements.currencySelect.addEventListener('change', updateAmountHint);
            elements.form.addEventListener('submit', handleFormSubmit);

            // Initial setup
            updateFormForPaymentMethod();
            updateAmountHint();
        });
    </script>
@endsection
