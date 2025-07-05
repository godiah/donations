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
                            <svg class="w-4 h-4 text-success-500" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 1H5C3.9 1 3 1.9 3 3V21C3 22.1 3.9 23 5 23H19C20.1 23 21 22.1 21 21V9M19 9H14V4H19V9Z" />
                            </svg>
                            KES {{ number_format($application->applicant->amount_raised ?? 0, 2) }} raised
                        </span>
                        <span class="text-neutral-400">•</span>
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4 text-primary-500" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M16 4C18.2 4 20 5.8 20 8C20 10.2 18.2 12 16 12C13.8 12 12 10.2 12 8C12 5.8 13.8 4 16 4ZM16 14C18.7 14 24 15.3 24 18V20H8V18C8 15.3 13.3 14 16 14ZM8 12C10.2 12 12 10.2 12 8C12 5.8 10.2 4 8 4C5.8 4 4 5.8 4 8C4 10.2 5.8 12 8 12ZM8 14C5.3 14 0 15.3 0 18V20H6V18C6 16.4 6.7 15.1 7.6 14.1C7.1 14 6.6 14 8 14Z" />
                            </svg>
                            {{ $application->applicant->contributors_count ?? 0 }} people
                        </span>
                    </div>
                </div>

                <!-- Progress Bar -->
                @php
                    $amountRaised = $application->applicant->amount_raised ?? 0;
                    $targetAmount = $application->applicant->target_amount ?? 100000; // Default target for percentage calculation
                    $progressPercentage = min(($amountRaised / $targetAmount) * 100, 100);
                @endphp

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

                    <!-- Phone Field (Optional for M-Pesa, Required for Card) -->
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
                                                <p class="text-sm text-neutral-600">Visa, Mastercard, etc.</p>
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
        document.addEventListener('DOMContentLoaded', function() {
            const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
            const phoneRequired = document.getElementById('phone-required');
            const phoneInput = document.getElementById('phone');

            function updatePhoneRequirement() {
                const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
                if (selectedMethod && selectedMethod.value === 'card') {
                    phoneRequired.style.display = 'inline';
                    phoneInput.required = true;
                } else {
                    phoneRequired.style.display = 'none';
                    phoneInput.required = false;
                }
            }

            paymentMethods.forEach(method => {
                method.addEventListener('change', updatePhoneRequirement);
            });

            // Initial check
            updatePhoneRequirement();
        });
    </script>

@endsection
