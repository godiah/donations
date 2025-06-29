@extends('layouts.donation')

@section('title', $application->applicant->contribution_name ?? 'Support Our Cause')

@section('content')
    <div class="p-6 sm:p-8">
        <!-- Header -->
        <header class="text-center mb-10">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Make a Donation</h1>
            <p class="mt-2 text-sm sm:text-base text-gray-600">Your support makes a difference. Thank you!</p>
        </header>

        <!-- Contribution Details -->
        <section class="mb-10">
            <div class="grid gap-6 sm:grid-cols-2">
                <!-- Contribution Info -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">About This Cause</h2>
                    <div class="space-y-4 text-sm">
                        <div>
                            <span class="block text-gray-500 font-medium">Cause Name</span>
                            <p class="text-gray-900">{{ $application->applicant->contribution_name ?? 'General Donation' }}
                            </p>
                        </div>
                        <div>
                            <span class="block text-gray-500 font-medium">Description</span>
                            <p class="text-gray-900">
                                {{ $application->applicant->contribution_description ?? 'No description provided.' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Funding Progress -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Funding Progress</h2>
                    <div class="space-y-4 text-sm">
                        <div>
                            <span class="block text-gray-500 font-medium">Target Amount</span>
                            <p class="text-lg font-bold text-green-600">
                                @if ($application->applicant->target_amount)
                                    KES {{ number_format($application->applicant->target_amount, 2) }}
                                @else
                                    Open Contribution
                                @endif
                            </p>
                        </div>
                        <div>
                            <span class="block text-gray-500 font-medium">Amount Raised</span>
                            <p class="text-lg font-semibold text-blue-600">
                                KES {{ number_format($application->applicant->amount_raised ?? 0, 2) }}
                            </p>
                        </div>
                        <div>
                            <span class="block text-gray-500 font-medium">Target Date</span>
                            <p class="text-gray-900">
                                {{ $application->applicant->target_date ? \Carbon\Carbon::parse($application->applicant->target_date)->format('M d, Y') : 'Ongoing' }}
                            </p>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    @if ($application->applicant->target_amount)
                        @php
                            $progressPercentage = min(
                                ($application->applicant->amount_raised / $application->applicant->target_amount) * 100,
                                100,
                            );
                        @endphp
                        <div class="mt-4">
                            <div class="flex justify-between text-sm text-gray-600 mb-2">
                                <span>Progress</span>
                                <span>{{ number_format($progressPercentage, 1) }}%</span>
                            </div>
                            <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-blue-600 rounded-full transition-all duration-300"
                                    style="width: {{ $progressPercentage }}%"></div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        <!-- Donation Form -->
        <section class="bg-gray-50 rounded-lg p-6 sm:p-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Complete Your Donation</h2>

            <form action="{{ route('donation.process', $donationLink->code) }}" method="POST" id="donation-form"
                aria-labelledby="donation-form-heading">
                @csrf

                <!-- Phone Number (Optional) -->
                <div class="mb-6">
                    <label for="donor_phone" class="block text-sm font-medium text-gray-700 mb-2">
                        Phone Number
                        <span class="text-gray-500 text-xs ml-1"
                            title="Optional: Provide your phone number for donation updates">(Optional)</span>
                    </label>
                    <input type="tel" id="donor_phone" name="donor_phone" value="{{ old('donor_phone') }}"
                        placeholder="e.g., +254700000000" pattern="\+254[0-9]{9}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                        aria-describedby="donor_phone_help">
                    <p id="donor_phone_help" class="mt-1 text-xs text-gray-500">Enter a valid Kenyan phone number (e.g.,
                        +254700000000).</p>
                    @error('donor_phone')
                        <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Donation Amount -->
                <div class="mb-6">
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                        Donation Amount (KES)
                        <span class="text-red-600" aria-hidden="true">*</span>
                        <span class="sr-only">(Required)</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">KES</span>
                        <input type="number" id="amount" name="amount" value="{{ old('amount') }}" min="1"
                            step="0.01" required
                            class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors @error('amount') border-red-500 @enderror"
                            placeholder="0.00" aria-describedby="amount_error">
                    </div>
                    @error('amount')
                        <p id="amount_error" class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                    @enderror

                    <!-- Quick Amount Buttons -->
                    <div class="mt-4 flex flex-wrap gap-3">
                        @foreach ([500, 1000, 2500, 5000] as $amount)
                            <button type="button"
                                class="quick-amount px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-blue-100 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors"
                                data-amount="{{ $amount }}"
                                aria-label="Select donation amount of KES {{ number_format($amount, 2) }}">
                                KES {{ number_format($amount, 2) }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="mb-8">
                    <fieldset>
                        <legend class="block text-sm font-medium text-gray-700 mb-4">Payment Method <span
                                class="text-red-600" aria-hidden="true">*</span><span class="sr-only">(Required)</span>
                        </legend>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <!-- M-Pesa Option -->
                            <div class="payment-option">
                                <input type="radio" id="mpesa" name="payment_method" value="mpesa" class="sr-only"
                                    {{ old('payment_method') === 'mpesa' ? 'checked' : '' }} required
                                    aria-describedby="mpesa_description">
                                <label for="mpesa"
                                    class="block w-full p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors payment-label">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div
                                                class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center mr-3">
                                                <span class="text-white font-bold text-lg">M</span>
                                            </div>
                                            <div>
                                                <h3 class="font-semibold text-gray-900">M-Pesa</h3>
                                                <p id="mpesa_description" class="text-sm text-gray-600">Pay using M-Pesa
                                                    mobile money</p>
                                            </div>
                                        </div>
                                        <div class="payment-check hidden">
                                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20"
                                                aria-hidden="true">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <!-- Card Option -->
                            <div class="payment-option">
                                <input type="radio" id="card" name="payment_method" value="card"
                                    class="sr-only" {{ old('payment_method') === 'card' ? 'checked' : '' }} required
                                    aria-describedby="card_description">
                                <label for="card"
                                    class="block w-full p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors payment-label">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div
                                                class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center mr-3">
                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                                                    </path>
                                                </svg>
                                            </div>
                                            <div>
                                                <h3 class="font-semibold text-gray-900">Debit/Credit Card</h3>
                                                <p id="card_description" class="text-sm text-gray-600">Pay with Visa,
                                                    Mastercard ...</p>
                                            </div>
                                        </div>
                                        <div class="payment-check hidden">
                                            <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20"
                                                aria-hidden="true">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                        @error('payment_method')
                            <p class="mt-2 text-sm text-red-600" role="alert">{{ $message }}</p>
                        @enderror
                    </fieldset>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit"
                        class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors disabled:bg-blue-300 disabled:cursor-not-allowed"
                        aria-label="Proceed to payment">
                        Proceed to Payment
                    </button>
                </div>
            </form>
        </section>

        <!-- Security Notice -->
        <footer class="mt-8 text-center text-sm text-gray-600">
            <p class="flex items-center justify-center">
                <svg class="w-5 h-5 text-gray-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 11c0 2.21-1.79 4-4 4s-4-1.79-4-4 1.79-4 4-4 4 1.79 4 4zm0 0l9 6m0 0l-9 6m9-12v12"></path>
                </svg>
                Your donation is secure and encrypted. We never store your payment information.
            </p>
        </footer>
    </div>

    <!-- JavaScript for form interactions -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Quick Amount Buttons
            const amountInput = document.getElementById('amount');
            document.querySelectorAll('.quick-amount').forEach(button => {
                button.addEventListener('click', () => {
                    amountInput.value = button.dataset.amount;
                    document.querySelectorAll('.quick-amount').forEach(btn => {
                        btn.classList.remove('bg-blue-600', 'text-white');
                        btn.classList.add('bg-gray-100', 'text-gray-700');
                    });
                    button.classList.remove('bg-gray-100', 'text-gray-700');
                    button.classList.add('bg-blue-600', 'text-white');
                    button.focus();
                });
            });

            // Payment Method Selection
            const updatePaymentSelection = (radio) => {
                document.querySelectorAll('.payment-label').forEach(label => {
                    label.classList.remove('border-green-500', 'border-blue-500', 'bg-green-50',
                        'bg-blue-50');
                    label.classList.add('border-gray-300');
                    label.querySelector('.payment-check').classList.add('hidden');
                });

                const selectedLabel = document.querySelector(`label[for="${radio.id}"]`);
                const selectedCheck = selectedLabel.querySelector('.payment-check');
                selectedLabel.classList.remove('border-gray-300');
                selectedLabel.classList.add(radio.value === 'mpesa' ? 'border-green-500' : 'border-blue-500',
                    radio.value === 'mpesa' ? 'bg-green-50' : 'bg-blue-50');
                selectedCheck.classList.remove('hidden');
            };

            document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
                radio.addEventListener('change', () => updatePaymentSelection(radio));
                if (radio.checked) updatePaymentSelection(radio);
            });

            // Client-side Phone Number Validation
            const phoneInput = document.getElementById('donor_phone');
            phoneInput.addEventListener('input', () => {
                const phonePattern = /^\+254[0-9]{9}$/;
                if (phoneInput.value && !phonePattern.test(phoneInput.value)) {
                    phoneInput.setCustomValidity(
                        'Please enter a valid Kenyan phone number (e.g., +254700000000).');
                } else {
                    phoneInput.setCustomValidity('');
                }
            });

            // Client-side Amount Validation
            amountInput.addEventListener('input', () => {
                if (amountInput.value < 1) {
                    amountInput.setCustomValidity('Donation amount must be at least KES 1.00.');
                } else {
                    amountInput.setCustomValidity('');
                }
            });

            // Form Submission
            document.getElementById('donation-form').addEventListener('submit', (e) => {
                const submitButton = e.target.querySelector('button[type="submit"]');
                submitButton.disabled = true;
                submitButton.textContent = 'Processing...';
            });
        });
    </script>
@endsection
