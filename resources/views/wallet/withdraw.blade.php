<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="py-3">
                <h1 class="text-2xl font-heading font-bold text-neutral-800 tracking-tight">
                    Request Withdrawal
                </h1>
                <p class="text-sm text-neutral-500 font-medium">
                    Withdraw funds from your wallet
                </p>
            </div>
            <a href="{{ route('wallet.dashboard') }}"
                class="group inline-flex items-center text-sm gap-2 px-6 py-3 bg-gradient-to-r from-neutral-600 to-neutral-700 text-white font-semibold rounded-2xl hover:from-neutral-700 hover:to-neutral-800 focus:outline-none focus:ring-4 focus:ring-neutral-200 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                <svg class="w-5 h-5 transition-transform group-hover:-translate-x-1" fill="currentColor"
                    viewBox="0 0 24 24">
                    <path d="M15.41 7.41L14 6L8 12L14 18L15.41 16.59L10.83 12L15.41 7.41Z" />
                </svg>
                <span>Back to Wallet</span>
            </a>
        </div>
    </x-slot>

    <div class="pt-8 pb-12 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Available Balance Card -->
            <div class="bg-gradient-to-br from-primary-500 to-primary-600 rounded-3xl p-8 text-white shadow-xl mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-primary-100 text-sm font-medium mb-2">Available Balance</p>
                        <p class="text-4xl font-bold font-heading">KES {{ number_format($availableBalance, 2) }}</p>
                        <p class="text-primary-200 text-sm mt-2">Ready for withdrawal</p>
                    </div>
                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row gap-8 items-start">
                <!-- Withdrawal Form -->
                <div
                    class="w-full lg:w-1/2 bg-white rounded-3xl shadow-xl border border-neutral-100 overflow-hidden min-h-fit">
                    <div class="bg-gradient-to-r from-neutral-50 to-white p-6 border-b border-neutral-100">
                        <h2 class="text-xl font-heading font-semibold text-neutral-800">Withdrawal Details</h2>
                        <p class="text-neutral-500 text-sm mt-1">Enter the amount you want to withdraw</p>
                    </div>

                    <form action="{{ route('wallet.withdraw.store') }}" method="POST" class="p-6">
                        @csrf

                        <!-- Amount Input -->
                        <div class="space-y-4">
                            <div>
                                <label for="amount" class="block text-sm font-semibold text-neutral-700 mb-3">
                                    Withdrawal Amount
                                </label>
                                <div class="relative">
                                    <span
                                        class="absolute left-4 top-1/2 transform -translate-y-1/2 text-neutral-500 font-medium">KES</span>
                                    <input type="number" id="amount" name="amount" step="0.01" min="1"
                                        max="{{ $availableBalance }}" value="{{ old('amount') }}" placeholder="0.00"
                                        class="w-full pl-16 pr-6 py-4 border border-neutral-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-primary-100 focus:border-primary-500 transition-all duration-200 text-lg font-semibold"
                                        oninput="updateFeePreview()">
                                </div>
                                @error('amount')
                                    <p class="text-danger-600 text-sm mt-2 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Fee Breakdown -->
                            <div id="feeBreakdown" class="bg-neutral-50 rounded-2xl p-4 space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-neutral-600 text-sm">Withdrawal Amount:</span>
                                    <span class="font-semibold text-neutral-800" id="grossAmount">KES 0.00</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-neutral-600 text-sm">Processing Fee (<span
                                            id="feePercentage">{{ $feePreview['fee_percentage'] }}%</span>):</span>
                                    <span class="font-semibold text-danger-600" id="feeAmount">KES 0.00</span>
                                </div>
                                <div class="border-t border-neutral-200 pt-3">
                                    <div class="flex justify-between items-center">
                                        <span class="text-neutral-800 font-semibold">You'll Receive:</span>
                                        <span class="font-bold text-success-600 text-lg" id="netAmount">KES 0.00</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit"
                                class="w-full bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white font-semibold py-4 rounded-2xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 focus:outline-none focus:ring-4 focus:ring-primary-200">
                                <span class="flex items-center justify-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 3.75H6.912a2.25 2.25 0 0 0-2.15 1.588L2.35 13.177a2.25 2.25 0 0 0-.1.661V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 0 0-2.15-1.588H15M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859M12 3v8.25m0 0-3-3m3 3 3-3" />
                                    </svg>

                                    Request Withdrawal
                                </span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Payout Method Information -->
                <div class="w-full lg:w-1/2 space-y-6">
                    <!-- Current Payout Method -->
                    <div class="bg-white rounded-3xl shadow-xl border border-neutral-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-success-50 to-success-100 p-6 border-b border-success-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-heading font-semibold text-success-800">Payout Method</h3>
                                    <p class="text-success-600 text-sm mt-1">Funds will be transferred to this account
                                    </p>
                                </div>
                                @if ($payoutMethod->is_primary)
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-success-100 text-success-800 border border-success-200">
                                        <span class="w-2 h-2 bg-success-500 rounded-full mr-2"></span>
                                        Primary
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="flex items-center gap-4 mb-4">
                                <div
                                    class="w-12 h-12 rounded-2xl flex items-center justify-center {{ $payoutMethod->type === 'mobile_money' ? 'bg-success-100' : ($payoutMethod->type === 'bank_account' ? 'bg-primary-100' : 'bg-secondary-100') }}">
                                    @if ($payoutMethod->type === 'mobile_money')
                                        <svg class="w-6 h-6 text-success-600" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z" />
                                        </svg>
                                    @elseif($payoutMethod->type === 'bank_account')
                                        <svg class="w-6 h-6 text-primary-600" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M11.5 1L2 6v2h19V6m-5 4v7h3v-7M2 17v2h19v-2M6 10v7h3v-7H6m6 0v7h3v-7h-3Z" />
                                        </svg>
                                    @else
                                        <svg class="w-6 h-6 text-secondary-600" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z" />
                                        </svg>
                                    @endif
                                </div>
                                <div>
                                    <h4 class="font-semibold text-lg text-neutral-800">{{ $payoutMethod->type_display }}
                                    </h4>
                                    <p class="text-neutral-500">{{ $payoutMethod->formatted_account }}</p>
                                </div>
                            </div>

                            <!-- Payout Method Details -->
                            <div class="bg-neutral-50 rounded-2xl p-4 space-y-3">
                                @if ($payoutMethod->type === 'mobile_money')
                                    <div class="flex justify-between">
                                        <span class="text-neutral-600 text-sm">Provider:</span>
                                        <span
                                            class="font-medium text-neutral-800">{{ $payoutMethod->provider }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-neutral-600 text-sm">Phone Number:</span>
                                        <span
                                            class="font-medium text-neutral-800">{{ $payoutMethod->account_number }}</span>
                                    </div>
                                @elseif($payoutMethod->type === 'bank_account')
                                    <div class="flex justify-between">
                                        <span class="text-neutral-600 text-sm">Bank:</span>
                                        <span
                                            class="font-medium text-neutral-800">{{ $payoutMethod->bank->name ?? $payoutMethod->provider }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-neutral-600 text-sm">Account Number:</span>
                                        <span
                                            class="font-medium text-neutral-800">{{ $payoutMethod->account_number }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-neutral-600 text-sm">Account Name:</span>
                                        <span
                                            class="font-medium text-neutral-800">{{ $payoutMethod->account_name }}</span>
                                    </div>
                                @elseif($payoutMethod->type === 'paybill')
                                    <div class="flex justify-between">
                                        <span class="text-neutral-600 text-sm">Paybill Number:</span>
                                        <span
                                            class="font-medium text-neutral-800">{{ $payoutMethod->paybill_number }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-neutral-600 text-sm">Account Number:</span>
                                        <span
                                            class="font-medium text-neutral-800">{{ $payoutMethod->account_number }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-neutral-600 text-sm">Account Name:</span>
                                        <span
                                            class="font-medium text-neutral-800">{{ $payoutMethod->paybill_account_name }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-neutral-600 text-sm">Provider:</span>
                                        <span
                                            class="font-medium text-neutral-800">{{ $payoutMethod->provider }}</span>
                                    </div>
                                @endif

                                @if ($payoutMethod->is_verified)
                                    <div
                                        class="flex items-center justify-center gap-2 pt-2 border-t border-neutral-200">
                                        <svg class="w-4 h-4 text-success-600" fill="currentColor"
                                            viewBox="0 0 24 24">
                                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                                        </svg>
                                        <span class="text-success-600 text-sm font-medium">Verified</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Update Payout Method Link -->
                            <div class="mt-4">
                                <a href="{{ route('payout-methods.index') }}"
                                    class="inline-flex items-center gap-2 text-primary-600 hover:text-primary-700 font-medium transition-colors">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" />
                                    </svg>
                                    Update Payout Method
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Processing Information -->
                    <div class="bg-white rounded-3xl shadow-lg border border-neutral-100 p-6">
                        <h3 class="text-lg font-heading font-semibold text-neutral-800 mb-4">Processing Information
                        </h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex items-start gap-3">
                                <div class="w-2 h-2 bg-primary-500 rounded-full mt-2"></div>
                                <p class="text-neutral-600">Withdrawal requests are processed within 24-48 hours</p>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="w-2 h-2 bg-primary-500 rounded-full mt-2"></div>
                                <p class="text-neutral-600">Processing fees vary by payout method</p>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="w-2 h-2 bg-primary-500 rounded-full mt-2"></div>
                                <p class="text-neutral-600">You'll receive an email confirmation once processed</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        function updateFeePreview() {
            const amountInput = document.getElementById('amount');
            const amount = parseFloat(amountInput.value) || 0;

            if (amount > 0) {
                // Make AJAX request to get fee preview
                fetch('{{ route('wallet.withdrawals.fee-preview') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            amount: amount
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('grossAmount').textContent = 'KES ' + Number(data.data.gross_amount)
                                .toLocaleString('en-US', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                            document.getElementById('feeAmount').textContent = 'KES ' + Number(data.data.fee_amount)
                                .toLocaleString('en-US', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                            document.getElementById('netAmount').textContent = 'KES ' + Number(data.data.net_amount)
                                .toLocaleString('en-US', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                            document.getElementById('feePercentage').textContent = data.data.fee_percentage;
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching fee preview:', error);
                    });
            } else {
                // Reset to zero
                document.getElementById('grossAmount').textContent = 'KES 0.00';
                document.getElementById('feeAmount').textContent = 'KES 0.00';
                document.getElementById('netAmount').textContent = 'KES 0.00';
            }
        }

        // Format number input with commas
        document.getElementById('amount').addEventListener('input', function(e) {
            let value = e.target.value.replace(/,/g, '');
            if (value && !isNaN(value)) {
                // Don't format while typing decimals
                if (!value.includes('.') || (value.includes('.') && value.split('.')[1].length <= 2)) {
                    updateFeePreview();
                }
            }
        });
    </script>
</x-app-layout>
