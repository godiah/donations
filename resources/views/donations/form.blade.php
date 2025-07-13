@extends('layouts.donation')

@section('title', $application->applicant->contribution_name ?? 'Support Our Cause')

@section('content')
    <div class="relative">
        <!-- Header Section -->
        <header class="p-6 text-white bg-gradient-to-r from-primary-500 to-primary-600 sm:p-8">
            <div class="text-center">
                <h1 class="mb-2 text-2xl font-bold sm:text-3xl font-heading">Make a Donation</h1>
                <p class="text-sm text-primary-100 sm:text-base">Your support creates lasting impact. Thank you for caring!
                </p>
            </div>
        </header>

        <div class="p-6 space-y-8 sm:p-8">
            <!-- Beneficiary Section -->
            <section class="p-6 border shadow-sm bg-gradient-to-br from-neutral-50 to-white rounded-2xl border-neutral-200">
                <div class="grid gap-6 lg:grid-cols-3">
                    <!-- Beneficiary Photo -->
                    <div class="lg:col-span-1">
                        <div class="relative">
                            <div
                                class="overflow-hidden shadow-lg aspect-square rounded-2xl bg-gradient-to-br from-neutral-100 to-neutral-200">
                                @if (isset($application->applicant->photo_url))
                                    <img src="{{ $application->applicant->photo_url }}"
                                        alt="{{ $application->applicant->contribution_name ?? 'Beneficiary' }}"
                                        class="object-cover w-full h-full">
                                @else
                                    <div class="flex items-center justify-center w-full h-full">
                                        <svg class="w-16 h-16 text-neutral-400" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M12 12C14.21 12 16 10.21 16 8C16 5.79 14.21 4 12 4C9.79 4 8 5.79 8 8C8 10.21 9.79 12 12 12ZM12 14C9.33 14 4 15.34 4 18V20H20V18C20 15.34 14.67 14 12 14Z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <!-- Verification Badge -->
                            <div class="absolute p-2 text-white rounded-full shadow-lg -top-2 -right-2 bg-success-500">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M9 16.17L4.83 12L3.41 13.41L9 19L21 7L19.59 5.59L9 16.17Z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Beneficiary Details -->
                    <div class="space-y-4 lg:col-span-2">
                        <div>
                            <h2 class="mb-2 text-xl font-bold font-heading text-neutral-800">
                                {{ $application->applicant->contribution_name ?? 'General Donation' }}
                            </h2>
                            <p class="leading-relaxed text-neutral-600">
                                {{ $application->applicant->contribution_description ?? 'Your generous contribution will make a meaningful difference in supporting this important cause.' }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Progress Section -->
            <section class="p-6 border bg-gradient-to-r from-success-50 to-primary-50 rounded-2xl border-success-200">
                <div class="mb-6 text-center">
                    <h3 class="mb-2 text-lg font-semibold font-heading text-neutral-800">Fundraising Progress</h3>
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
                    <div class="flex items-center justify-between text-sm">
                        <span class="font-medium text-neutral-700">Progress</span>
                        <span class="font-bold text-primary-600">{{ number_format($progressPercentage, 1) }}%</span>
                    </div>

                    <div class="relative h-4 overflow-hidden bg-white border rounded-full shadow-inner border-neutral-200">
                        <div class="absolute inset-0 transition-all duration-1000 ease-out rounded-full bg-gradient-to-r from-success-400 to-primary-500"
                            style="width: {{ $progressPercentage }}%">
                            <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
                        </div>
                    </div>

                    {{-- Progress Description --}}
                    <div class="text-xs text-center text-gray-500">
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
            <section class="overflow-hidden bg-white border shadow-sm rounded-2xl border-neutral-200">
                <div class="p-6 text-white bg-gradient-to-r from-neutral-800 to-neutral-700">
                    <h3 class="flex items-center gap-2 text-xl font-bold font-heading">
                        Complete Your Donation
                    </h3>
                    <p class="mt-1 text-neutral-300">Choose your preferred payment method and amount</p>
                </div>

                <form action="{{ route('donation.process', $donationLink->code) }}" method="POST" id="donation-form"
                    class="p-6 space-y-6">
                    @csrf

                    <!-- Step 1: Donation Amount -->
                    <div class="donation-step" id="step-amount">
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
                                        <option value="USD" {{ old('currency') === 'USD' ? 'selected' : '' }}>USD
                                        </option>
                                    </select>
                                    @error('currency')
                                        <p class="flex items-center gap-1 mt-1 text-sm text-danger-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <!-- Amount Input -->
                                <div class="flex-1">
                                    <input type="number" id="amount" name="amount" value="{{ old('amount') }}"
                                        min="1" step="0.01" required
                                        class="w-full px-4 py-4 text-lg font-semibold border-2 border-neutral-200 rounded-xl focus:border-primary-500 focus:ring-0 transition-colors @error('amount') border-danger-500 @enderror"
                                        placeholder="0.00">
                                    @error('amount')
                                        <p class="flex items-center gap-1 mt-1 text-sm text-danger-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <p class="text-sm text-neutral-600" id="amount-hint">Minimum: KES 10</p>
                        </div>
                    </div>

                    <!-- Step 2: Donation Type -->
                    <div class="donation-step" id="step-donation-type">
                        <div class="space-y-3">
                            <label for="donation_type" class="block text-sm font-semibold text-neutral-800">
                                Donate as <span class="text-danger-500">*</span>
                            </label>
                            <div class="relative">
                                <select id="donation_type" name="donation_type" required
                                    class="w-full px-4 py-4 border-2 border-neutral-200 rounded-xl focus:border-primary-500 focus:ring-0 transition-colors @error('donation_type') border-danger-500 @enderror">
                                    <option value="">Select donation type</option>
                                    <option value="anonymous"
                                        {{ old('donation_type') === 'anonymous' ? 'selected' : '' }}>Anonymous</option>
                                    <option value="family" {{ old('donation_type') === 'family' ? 'selected' : '' }}>
                                        Family Member</option>
                                    <option value="friend" {{ old('donation_type') === 'friend' ? 'selected' : '' }}>
                                        Friend</option>
                                    <option value="colleague"
                                        {{ old('donation_type') === 'colleague' ? 'selected' : '' }}>Colleague</option>
                                    <option value="supporter"
                                        {{ old('donation_type') === 'supporter' ? 'selected' : '' }}>Supporter</option>
                                    <option value="other" {{ old('donation_type') === 'other' ? 'selected' : '' }}>Other
                                    </option>
                                </select>
                                @error('donation_type')
                                    <p class="flex items-center gap-1 mt-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Contact Information -->
                    <div class="donation-step" id="step-contact" style="display: none;">
                        <div class="space-y-4">
                            <h4 class="text-sm font-semibold text-neutral-800">Contact Information</h4>

                            <!-- Email Field -->
                            <div class="space-y-2">
                                <label for="email" class="block text-sm font-medium text-neutral-700">
                                    Email Address <span class="text-danger-500">*</span>
                                </label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}"
                                    required
                                    class="w-full px-4 py-3 border-2 border-neutral-200 rounded-xl focus:border-primary-500 focus:ring-0 transition-colors @error('email') border-danger-500 @enderror"
                                    placeholder="your@email.com">
                                @error('email')
                                    <p class="flex items-center gap-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Payment Method Selection -->
                    <div class="donation-step" id="step-payment-method" style="display: none;">
                        <div class="space-y-4">
                            <h4 class="text-sm font-semibold text-neutral-800">Choose Payment Method <span
                                    class="text-danger-500">*</span></h4>

                            <div class="grid gap-4 sm:grid-cols-2">
                                <!-- M-Pesa Option -->
                                <div class="payment-method-card" data-method="mpesa">
                                    <input type="radio" id="mpesa" name="payment_method" value="mpesa"
                                        class="sr-only" {{ old('payment_method') === 'mpesa' ? 'checked' : '' }} required>
                                    <label for="mpesa"
                                        class="block w-full p-4 transition-all border-2 cursor-pointer payment-method-label border-neutral-200 rounded-xl hover:border-primary-300 hover:shadow-md">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="flex items-center justify-center w-10 h-10 bg-green-100 rounded-lg">
                                                    <svg class="w-6 h-6 text-green-600" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <h5 class="font-semibold text-neutral-800">M-Pesa</h5>
                                                    <p class="text-sm text-neutral-600">Mobile money payment</p>
                                                </div>
                                            </div>
                                            <div
                                                class="flex items-center justify-center w-5 h-5 border-2 rounded-full payment-method-check border-neutral-300">
                                                <div
                                                    class="w-3 h-3 transition-opacity rounded-full opacity-0 bg-primary-500">
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
                                        class="block w-full p-4 transition-all border-2 cursor-pointer payment-method-label border-neutral-200 rounded-xl hover:border-primary-300 hover:shadow-md">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="flex items-center justify-center w-10 h-10 bg-blue-100 rounded-lg">
                                                    <svg class="w-6 h-6 text-blue-600" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 003 3z" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <h5 class="font-semibold text-neutral-800">Debit/Credit Card</h5>
                                                    <p class="text-sm text-neutral-600">Visa, Mastercard, etc.</p>
                                                </div>
                                            </div>
                                            <div
                                                class="flex items-center justify-center w-5 h-5 border-2 rounded-full payment-method-check border-neutral-300">
                                                <div
                                                    class="w-3 h-3 transition-opacity rounded-full opacity-0 bg-primary-500">
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            @error('payment_method')
                                <p class="flex items-center gap-1 text-sm text-danger-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Step 5: Payment Details (Single section with conditional fields) -->
                    <div class="donation-step" id="step-payment-details" style="display: none;">

                        <!-- M-Pesa Details -->
                        <div class="payment-details" id="mpesa-details" style="display: none;">
                            <div class="space-y-4">
                                <h4 class="text-sm font-semibold text-neutral-800">M-Pesa Payment Details</h4>

                                <div class="space-y-2">
                                    <label for="phone" class="block text-sm font-medium text-neutral-700">
                                        M-Pesa Phone Number <span class="text-danger-500">*</span>
                                    </label>
                                    <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                                        class="w-full px-4 py-3 border-2 border-neutral-200 rounded-xl focus:border-primary-500 focus:ring-0 transition-colors @error('phone') border-danger-500 @enderror"
                                        placeholder="0712345678">
                                    <p class="text-sm text-neutral-600">Enter your Safaricom number (e.g., 0712345678)</p>
                                    @error('phone')
                                        <p class="flex items-center gap-1 text-sm text-danger-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Optional name field for M-Pesa -->
                                <div class="space-y-2">
                                    <label for="full_name" class="block text-sm font-medium text-neutral-700">
                                        Full Name <span class="text-neutral-500">(Optional)</span>
                                    </label>
                                    <input type="text" id="full_name" name="full_name"
                                        value="{{ old('full_name') }}"
                                        class="w-full px-4 py-3 transition-colors border-2 border-neutral-200 rounded-xl focus:border-primary-500 focus:ring-0"
                                        placeholder="Your full name">
                                    <p class="text-sm text-neutral-600">Leave blank to donate anonymously</p>
                                </div>
                            </div>
                        </div>

                        <!-- Card Payment Details -->
                        <div class="payment-details" id="card-details" style="display: none;">
                            <div class="space-y-4">
                                <h4 class="text-sm font-semibold text-neutral-800">Billing Information</h4>

                                <!-- Full Name -->
                                <div class="space-y-2">
                                    <label for="card_full_name" class="block text-sm font-medium text-neutral-700">
                                        Full Name <span class="text-danger-500">*</span>
                                    </label>
                                    <input type="text" id="card_full_name" name="card_full_name"
                                        value="{{ old('full_name') }}"
                                        class="w-full px-4 py-3 border-2 border-neutral-200 rounded-xl focus:border-primary-500 focus:ring-0 transition-colors @error('full_name') border-danger-500 @enderror"
                                        placeholder="John Doe">
                                    @error('full_name')
                                        <p class="flex items-center gap-1 text-sm text-danger-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Phone (optional for card) -->
                                <div class="space-y-2">
                                    <label for="card_phone" class="block text-sm font-medium text-neutral-700">
                                        Phone Number <span class="text-neutral-500">(Optional)</span>
                                    </label>
                                    <input type="tel" id="card_phone" name="card_phone" value="{{ old('phone') }}"
                                        class="w-full px-4 py-3 transition-colors border-2 border-neutral-200 rounded-xl focus:border-primary-500 focus:ring-0"
                                        placeholder="+1234567890">
                                </div>

                                <!-- Address -->
                                <div class="space-y-2">
                                    <label for="address_line1" class="block text-sm font-medium text-neutral-700">
                                        Address <span class="text-danger-500">*</span>
                                    </label>
                                    <input type="text" id="address_line1" name="address_line1"
                                        value="{{ old('address_line1') }}"
                                        class="w-full px-4 py-3 border-2 border-neutral-200 rounded-xl focus:border-primary-500 focus:ring-0 transition-colors @error('address_line1') border-danger-500 @enderror"
                                        placeholder="123 Main Street">
                                    @error('address_line1')
                                        <p class="flex items-center gap-1 text-sm text-danger-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- City and State -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <label for="city" class="block text-sm font-medium text-neutral-700">
                                            City <span class="text-danger-500">*</span>
                                        </label>
                                        <input type="text" id="city" name="city" value="{{ old('city') }}"
                                            class="w-full px-4 py-3 border-2 border-neutral-200 rounded-xl focus:border-primary-500 focus:ring-0 transition-colors @error('city') border-danger-500 @enderror"
                                            placeholder="New York">
                                        @error('city')
                                            <p class="flex items-center gap-1 text-sm text-danger-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="space-y-2">
                                        <label for="state" class="block text-sm font-medium text-neutral-700">
                                            State/Province <span class="text-danger-500">*</span>
                                        </label>
                                        <input type="text" id="state" name="state" value="{{ old('state') }}"
                                            class="w-full px-4 py-3 border-2 border-neutral-200 rounded-xl focus:border-primary-500 focus:ring-0 transition-colors @error('state') border-danger-500 @enderror"
                                            placeholder="NY">
                                        @error('state')
                                            <p class="flex items-center gap-1 text-sm text-danger-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Postal Code and Country -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <label for="postal_code" class="block text-sm font-medium text-neutral-700">
                                            Postal Code <span class="text-danger-500">*</span>
                                        </label>
                                        <input type="text" id="postal_code" name="postal_code"
                                            value="{{ old('postal_code') }}"
                                            class="w-full px-4 py-3 border-2 border-neutral-200 rounded-xl focus:border-primary-500 focus:ring-0 transition-colors @error('postal_code') border-danger-500 @enderror"
                                            placeholder="10001">
                                        @error('postal_code')
                                            <p class="flex items-center gap-1 text-sm text-danger-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="space-y-2">
                                        <label for="country" class="block text-sm font-medium text-neutral-700">
                                            Country <span class="text-danger-500">*</span>
                                        </label>
                                        <select id="country" name="country"
                                            class="w-full px-4 py-3 border-2 border-neutral-200 rounded-xl focus:border-primary-500 focus:ring-0 transition-colors @error('country') border-danger-500 @enderror">
                                            <option value="">Select Country</option>
                                            <option value="US" {{ old('country') === 'US' ? 'selected' : '' }}>United
                                                States</option>
                                            <option value="KE" {{ old('country') === 'KE' ? 'selected' : '' }}>Kenya
                                            </option>
                                            <option value="CA" {{ old('country') === 'CA' ? 'selected' : '' }}>Canada
                                            </option>
                                            <option value="GB" {{ old('country') === 'GB' ? 'selected' : '' }}>United
                                                Kingdom</option>
                                            <option value="AU" {{ old('country') === 'AU' ? 'selected' : '' }}>
                                                Australia</option>
                                            <option value="DE" {{ old('country') === 'DE' ? 'selected' : '' }}>Germany
                                            </option>
                                            <option value="FR" {{ old('country') === 'FR' ? 'selected' : '' }}>France
                                            </option>
                                            <option value="IT" {{ old('country') === 'IT' ? 'selected' : '' }}>Italy
                                            </option>
                                            <option value="ES" {{ old('country') === 'ES' ? 'selected' : '' }}>Spain
                                            </option>
                                            <option value="NL" {{ old('country') === 'NL' ? 'selected' : '' }}>
                                                Netherlands</option>
                                        </select>
                                        @error('country')
                                            <p class="flex items-center gap-1 text-sm text-danger-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-6 border-t border-neutral-200" id="submit-section" style="display: none;">
                        <button type="submit" id="submit-btn"
                            class="w-full px-6 py-4 font-semibold text-white transition-colors bg-primary-600 rounded-xl hover:bg-primary-700 disabled:bg-neutral-400 disabled:cursor-not-allowed">
                            <span class="flex items-center justify-center gap-2">
                                <span id="submit-text">Complete Donation</span>
                            </span>
                        </button>

                        <!-- Security Notice -->
                        <div class="mt-4 text-center">
                            <p class="flex items-center justify-center gap-2 text-sm text-neutral-600">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Secure payment processing
                            </p>
                        </div>
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

        /* Progressive form styling */
        .donation-step {
            opacity: 1;
            transform: translateY(0);
            transition: all 0.3s ease;
        }

        .payment-method-card {
            transition: all 0.2s ease;
        }

        .payment-method-card:hover {
            transform: translateY(-1px);
        }

        .payment-method-label.border-primary-500 {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .payment-details {
            animation: slideIn 0.3s ease forwards;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Focus states */
        input:focus,
        select:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        /* Button states */
        button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* Payment method check animation */
        .payment-method-check div {
            transition: opacity 0.2s ease;
        }

        /* Responsive adjustments */
        @media (max-width: 640px) {
            .grid-cols-2 {
                grid-template-columns: 1fr;
            }

            .sm\\:grid-cols-2 {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // DOM Elements
            const elements = {
                // Form and inputs
                form: document.getElementById('donation-form'),
                amountInput: document.getElementById('amount'),
                currencySelect: document.getElementById('currency'),
                donationTypeSelect: document.getElementById('donation_type'),
                emailInput: document.getElementById('email'),

                // Payment method elements
                paymentMethods: document.querySelectorAll('input[name="payment_method"]'),
                mpesaRadio: document.getElementById('mpesa'),
                cardRadio: document.getElementById('card'),

                // M-Pesa specific fields
                phoneInput: document.getElementById('phone'),
                fullNameInput: document.getElementById('full_name'),

                // Card specific fields
                cardFullNameInput: document.getElementById('card_full_name'),
                cardPhoneInput: document.getElementById('card_phone'),
                addressInput: document.getElementById('address_line1'),
                cityInput: document.getElementById('city'),
                stateInput: document.getElementById('state'),
                postalCodeInput: document.getElementById('postal_code'),
                countrySelect: document.getElementById('country'),

                // Steps
                stepAmount: document.getElementById('step-amount'),
                stepDonationType: document.getElementById('step-donation-type'),
                stepContact: document.getElementById('step-contact'),
                stepPaymentMethod: document.getElementById('step-payment-method'),
                stepPaymentDetails: document.getElementById('step-payment-details'),
                mpesaDetails: document.getElementById('mpesa-details'),
                cardDetails: document.getElementById('card-details'),
                submitSection: document.getElementById('submit-section'),

                // Other elements
                amountHint: document.getElementById('amount-hint'),
                submitBtn: document.getElementById('submit-btn'),
                submitText: document.getElementById('submit-text'),
            };

            // Form state
            let currentStep = 1;
            const totalSteps = 5;

            // Heroicons
            const icons = {
                mobile: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
        </svg>`,
                creditCard: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 003 3z"/>
        </svg>`,
                heart: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
        </svg>`,
                loading: `<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>`
            };

            // Progressive form reveal functions
            function showStep(stepNumber) {
                // Hide all steps first
                const steps = [
                    elements.stepAmount,
                    elements.stepDonationType,
                    elements.stepContact,
                    elements.stepPaymentMethod,
                    elements.stepPaymentDetails
                ];

                steps.forEach(step => {
                    if (step) step.style.display = 'none';
                });

                if (elements.mpesaDetails) elements.mpesaDetails.style.display = 'none';
                if (elements.cardDetails) elements.cardDetails.style.display = 'none';
                if (elements.submitSection) elements.submitSection.style.display = 'none';

                // Show appropriate steps based on progression
                if (stepNumber >= 1 && elements.stepAmount) elements.stepAmount.style.display = 'block';
                if (stepNumber >= 2 && elements.stepDonationType) elements.stepDonationType.style.display = 'block';
                if (stepNumber >= 3 && elements.stepContact) elements.stepContact.style.display = 'block';
                if (stepNumber >= 4 && elements.stepPaymentMethod) elements.stepPaymentMethod.style.display =
                    'block';
                if (stepNumber >= 5 && elements.stepPaymentDetails) {
                    elements.stepPaymentDetails.style.display = 'block';

                    const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
                    if (selectedMethod) {
                        if (selectedMethod.value === 'mpesa') {
                            elements.mpesaDetails.style.display = 'block';
                        } else if (selectedMethod.value === 'card') {
                            elements.cardDetails.style.display = 'block';
                        }
                        elements.submitSection.style.display = 'block';
                    }
                }
            }

            function validateCurrentStep() {
                switch (currentStep) {
                    case 1:
                        return elements.amountInput.value && parseFloat(elements.amountInput.value) > 0;
                    case 2:
                        return elements.donationTypeSelect.value;
                    case 3:
                        return elements.emailInput.value && elements.emailInput.checkValidity();
                    case 4:
                        return document.querySelector('input[name="payment_method"]:checked');
                    case 5:
                        const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
                        if (selectedMethod?.value === 'mpesa') {
                            return elements.phoneInput.value && elements.phoneInput.value.length >= 9;
                        } else if (selectedMethod?.value === 'card') {
                            return elements.cardFullNameInput.value &&
                                elements.addressInput.value &&
                                elements.cityInput.value &&
                                elements.stateInput.value &&
                                elements.postalCodeInput.value &&
                                elements.countrySelect.value;
                        }
                        return false;
                    default:
                        return true;
                }
            }

            function advanceStep() {
                if (validateCurrentStep() && currentStep < totalSteps) {
                    currentStep++;
                    showStep(currentStep);
                    updateSubmitButton();
                }
            }

            // Synchronize form fields based on payment method
            function synchronizeFields() {
                const selectedMethod = document.querySelector('input[name="payment_method"]:checked');

                if (selectedMethod?.value === 'mpesa') {
                    // Sync full_name for M-Pesa
                    if (elements.fullNameInput && elements.cardFullNameInput) {
                        elements.fullNameInput.value = elements.cardFullNameInput.value || elements.fullNameInput
                            .value;
                    }

                    // Sync phone for M-Pesa (M-Pesa phone takes priority)
                    if (elements.phoneInput && elements.cardPhoneInput) {
                        elements.cardPhoneInput.value = elements.phoneInput.value || elements.cardPhoneInput.value;
                    }

                } else if (selectedMethod?.value === 'card') {
                    // Sync full_name for Card
                    if (elements.cardFullNameInput && elements.fullNameInput) {
                        elements.cardFullNameInput.value = elements.fullNameInput.value || elements
                            .cardFullNameInput.value;
                    }

                    // Sync phone for Card (card phone takes priority)
                    if (elements.cardPhoneInput && elements.phoneInput) {
                        elements.phoneInput.value = elements.cardPhoneInput.value || elements.phoneInput.value;
                    }
                }
            }

            // Update submit button based on payment method
            function updateSubmitButton() {
                const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
                const submitContainer = elements.submitBtn.querySelector('.flex');

                if (currentStep < 5) return;

                const config = selectedMethod?.value === 'mpesa' ? {
                        icon: icons.mobile,
                        text: 'Continue with M-Pesa'
                    } :
                    selectedMethod?.value === 'card' ? {
                        icon: icons.creditCard,
                        text: 'Proceed to Secure Payment'
                    } : {
                        icon: icons.heart,
                        text: 'Complete Donation'
                    };

                submitContainer.innerHTML = `${config.icon}<span id="submit-text">${config.text}</span>`;
            }

            // Update amount hint based on currency and payment method
            function updateAmountHint() {
                const currency = elements.currencySelect.value;
                const selectedMethod = document.querySelector('input[name="payment_method"]:checked');

                if (selectedMethod?.value === 'mpesa') {
                    elements.amountHint.textContent = 'Minimum: KES 10 (M-Pesa only supports KES)';
                } else {
                    elements.amountHint.textContent = currency === 'KES' ? 'Minimum: KES 10' : 'Minimum: $1';
                }
            }

            // Format Kenyan phone number
            function formatKenyanPhone() {
                if (!elements.phoneInput.value) return;

                let value = elements.phoneInput.value.replace(/\D/g, '');

                // Handle different input formats
                if (value.startsWith('0')) {
                    value = '254' + value.substring(1);
                } else if (value.length === 9) {
                    value = '254' + value;
                } else if (!value.startsWith('254')) {
                    // If it doesn't start with 254 and isn't 9 digits, assume it needs 254 prefix
                    if (value.length <= 9) {
                        value = '254' + value;
                    }
                }

                // Format the display
                if (value.length >= 12) {
                    elements.phoneInput.value =
                        `+${value.slice(0,3)} ${value.slice(3,6)} ${value.slice(6,9)} ${value.slice(9,12)}`;
                } else {
                    // Keep the raw number for shorter inputs
                    elements.phoneInput.value = value.startsWith('254') ? '+' + value : value;
                }
            }

            // Handle M-Pesa selection
            function handleMpesaSelection() {
                // Force currency to KES for M-Pesa
                elements.currencySelect.value = 'KES';
                elements.currencySelect.disabled = true;

                // Make phone required for M-Pesa
                if (elements.phoneInput) {
                    elements.phoneInput.required = true;
                }

                // Clear and disable card-specific fields
                const cardFields = [
                    elements.cardFullNameInput,
                    elements.cardPhoneInput,
                    elements.addressInput,
                    elements.cityInput,
                    elements.stateInput,
                    elements.postalCodeInput,
                    elements.countrySelect
                ];

                cardFields.forEach(field => {
                    if (field) {
                        field.required = false;
                        field.disabled = true;
                    }
                });

                // Enable M-Pesa fields
                if (elements.fullNameInput) elements.fullNameInput.disabled = false;

                updateAmountHint();
                updateSubmitButton();
                synchronizeFields();
            }

            // Handle card selection
            function handleCardSelection() {
                // Allow all currencies for card payments
                elements.currencySelect.disabled = false;

                // Make card fields required
                const cardFields = [
                    elements.cardFullNameInput,
                    elements.addressInput,
                    elements.cityInput,
                    elements.stateInput,
                    elements.postalCodeInput,
                    elements.countrySelect
                ];

                cardFields.forEach(field => {
                    if (field) {
                        field.required = true;
                        field.disabled = false;
                    }
                });

                // Optional phone for card
                if (elements.cardPhoneInput) {
                    elements.cardPhoneInput.required = false;
                    elements.cardPhoneInput.disabled = false;
                }

                // Disable M-Pesa specific fields
                if (elements.phoneInput) {
                    elements.phoneInput.required = false;
                    elements.phoneInput.disabled = true;
                }
                if (elements.fullNameInput) {
                    elements.fullNameInput.disabled = true;
                }

                updateAmountHint();
                updateSubmitButton();
                synchronizeFields();
            }

            // Update payment method styling
            function updatePaymentMethodStyling() {
                document.querySelectorAll('.payment-method-card').forEach(card => {
                    const input = card.querySelector('input');
                    const label = card.querySelector('.payment-method-label');
                    const check = card.querySelector('.payment-method-check div');

                    if (input.checked) {
                        label.classList.add('border-primary-500', 'bg-primary-50');
                        label.classList.remove('border-neutral-200');
                        check.classList.add('opacity-100');
                        check.classList.remove('opacity-0');
                    } else {
                        label.classList.remove('border-primary-500', 'bg-primary-50');
                        label.classList.add('border-neutral-200');
                        check.classList.remove('opacity-100');
                        check.classList.add('opacity-0');
                    }
                });
            }

            // Handle form submission with proper field synchronization
            function handleFormSubmit(e) {
                // Final synchronization before submission
                synchronizeFields();

                // Enable all fields temporarily for submission
                const allFields = elements.form.querySelectorAll('input, select');
                allFields.forEach(field => {
                    field.disabled = false;
                });

                // Re-enforce currency for M-Pesa
                const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
                if (selectedMethod?.value === 'mpesa') {
                    elements.currencySelect.value = 'KES';
                }

                // For card payments, copy card_full_name to full_name if needed
                if (selectedMethod?.value === 'card' && elements.cardFullNameInput.value) {
                    // Create a hidden input for full_name if it doesn't exist or use existing one
                    let fullNameField = elements.form.querySelector('input[name="full_name"]');
                    if (!fullNameField) {
                        fullNameField = document.createElement('input');
                        fullNameField.type = 'hidden';
                        fullNameField.name = 'full_name';
                        elements.form.appendChild(fullNameField);
                    }
                    fullNameField.value = elements.cardFullNameInput.value;
                }

                // For card payments, copy card_phone to phone if needed
                if (selectedMethod?.value === 'card' && elements.cardPhoneInput.value) {
                    let phoneField = elements.form.querySelector('input[name="phone"]');
                    if (!phoneField) {
                        phoneField = document.createElement('input');
                        phoneField.type = 'hidden';
                        phoneField.name = 'phone';
                        elements.form.appendChild(phoneField);
                    }
                    phoneField.value = elements.cardPhoneInput.value;
                }

                if (!validateCurrentStep()) {
                    e.preventDefault();
                    return false;
                }

                elements.submitBtn.disabled = true;
                const submitContainer = elements.submitBtn.querySelector('.flex');
                submitContainer.innerHTML = `${icons.loading}<span>Processing...</span>`;

                // Let the form submit naturally
                return true;
            }

            // Event listeners
            elements.amountInput.addEventListener('input', () => {
                if (currentStep === 1 && validateCurrentStep()) {
                    setTimeout(() => advanceStep(), 500);
                }
            });

            elements.donationTypeSelect.addEventListener('change', () => {
                if (currentStep === 2 && validateCurrentStep()) {
                    setTimeout(() => advanceStep(), 300);
                }
            });

            elements.emailInput.addEventListener('blur', () => {
                if (currentStep === 3 && validateCurrentStep()) {
                    setTimeout(() => advanceStep(), 300);
                }
            });

            elements.paymentMethods.forEach(method => {
                method.addEventListener('change', () => {
                    updatePaymentMethodStyling();

                    if (method.value === 'mpesa') {
                        handleMpesaSelection();
                    } else if (method.value === 'card') {
                        handleCardSelection();
                    }

                    if (currentStep === 4 && validateCurrentStep()) {
                        setTimeout(() => advanceStep(), 300);
                    }
                });
            });

            // Phone formatting for M-Pesa
            if (elements.phoneInput) {
                elements.phoneInput.addEventListener('blur', formatKenyanPhone);
                elements.phoneInput.addEventListener('input', () => {
                    if (currentStep === 5) {
                        updateSubmitButton();
                    }
                });
            }

            // Currency change handler
            elements.currencySelect.addEventListener('change', updateAmountHint);

            // Form submission handler
            elements.form.addEventListener('submit', handleFormSubmit);

            // Field synchronization for card fields
            if (elements.cardFullNameInput) {
                elements.cardFullNameInput.addEventListener('input', synchronizeFields);
            }
            if (elements.cardPhoneInput) {
                elements.cardPhoneInput.addEventListener('input', synchronizeFields);
            }
            if (elements.fullNameInput) {
                elements.fullNameInput.addEventListener('input', synchronizeFields);
            }

            // Validate payment details on change
            document.addEventListener('input', (e) => {
                if (currentStep === 5 && e.target.closest('#mpesa-details, #card-details')) {
                    updateSubmitButton();
                }
            });

            // Initialize form
            showStep(currentStep);
            updateAmountHint();
            updatePaymentMethodStyling();

            // If there are old values (form validation errors), advance to appropriate step
            if (elements.amountInput.value) currentStep = Math.max(currentStep, 2);
            if (elements.donationTypeSelect.value) currentStep = Math.max(currentStep, 3);
            if (elements.emailInput.value) currentStep = Math.max(currentStep, 4);
            if (document.querySelector('input[name="payment_method"]:checked')) currentStep = Math.max(currentStep,
                5);

            showStep(currentStep);

            // Handle pre-selected payment method
            const preSelectedMethod = document.querySelector('input[name="payment_method"]:checked');
            if (preSelectedMethod) {
                if (preSelectedMethod.value === 'mpesa') {
                    handleMpesaSelection();
                } else if (preSelectedMethod.value === 'card') {
                    handleCardSelection();
                }
                updatePaymentMethodStyling();
            }
        });
    </script>
@endsection
