<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="py-3">
                <h1 class="text-2xl font-heading font-bold text-neutral-800 tracking-tight">
                    Withdrawal Details
                </h1>
                <p class="text-sm text-neutral-500 font-medium">
                    View withdrawal request information
                </p>
            </div>
            <a href="{{ route('wallet.dashboard') }}"
                class="group inline-flex items-center gap-2 px-6 py-3 text-sm bg-gradient-to-r from-neutral-600 to-neutral-700 text-white font-semibold rounded-2xl hover:from-neutral-700 hover:to-neutral-800 focus:outline-none focus:ring-4 focus:ring-neutral-200 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
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

            <!-- Status Header -->
            <div class="mb-8">
                @php
                    $statusColors = [
                        'pending' => 'from-secondary-500 to-secondary-600',
                        'approved' => 'from-primary-500 to-primary-600',
                        'processing' => 'from-primary-600 to-primary-700',
                        'completed' => 'from-success-500 to-success-600',
                        'failed' => 'from-danger-500 to-danger-600',
                        'cancelled' => 'from-neutral-500 to-neutral-600',
                    ];
                    $bgColor = $statusColors[$withdrawal->status] ?? 'from-neutral-500 to-neutral-600';
                @endphp

                <div class="bg-gradient-to-br {{ $bgColor }} rounded-3xl p-8 text-white shadow-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <h2 class="text-3xl font-bold font-heading">KES
                                    {{ number_format($withdrawal->amount, 2) }}</h2>
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-white bg-opacity-20 backdrop-blur-sm">
                                    <span class="w-2 h-2 bg-white rounded-full mr-2"></span>
                                    {{ ucfirst($withdrawal->status) }}
                                </span>
                            </div>
                            <p class="text-white text-opacity-90 text-lg">
                                Withdrawal Request #{{ $withdrawal->request_reference }}
                            </p>
                            <p class="text-white text-opacity-75 text-sm mt-1">
                                Requested on {{ $withdrawal->created_at->format('F j, Y \a\t g:i A') }}
                            </p>
                        </div>
                        <div class="w-16 h-16 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center">
                            @if ($withdrawal->status === 'completed')
                                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                                </svg>
                            @elseif($withdrawal->status === 'failed' || $withdrawal->status === 'cancelled')
                                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12L19 6.41Z" />
                                </svg>
                            @elseif($withdrawal->status === 'processing')
                                <svg class="w-8 h-8 text-white animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                            @else
                                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z" />
                                </svg>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Amount Breakdown -->
                <div class="bg-white rounded-3xl shadow-xl border border-neutral-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-neutral-50 to-white p-6 border-b border-neutral-100">
                        <h3 class="text-xl font-heading font-semibold text-neutral-800">Amount Breakdown</h3>
                        <p class="text-neutral-500 text-sm mt-1">Detailed cost analysis</p>
                    </div>

                    <div class="p-6 space-y-4">
                        <div class="flex justify-between items-center py-3 border-b border-neutral-100">
                            <span class="text-neutral-600">Withdrawal Amount:</span>
                            <span class="font-semibold text-lg text-neutral-800">KES
                                {{ number_format($withdrawal->amount, 2) }}</span>
                        </div>

                        <div class="flex justify-between items-center py-3 border-b border-neutral-100">
                            <span class="text-neutral-600">Processing Fee:</span>
                            <span class="font-semibold text-lg text-danger-600">- KES
                                {{ number_format($withdrawal->fee_amount, 2) }}</span>
                        </div>

                        <div class="flex justify-between items-center py-3 bg-success-50 rounded-2xl px-4">
                            <span class="text-success-700 font-semibold">Net Amount:</span>
                            <span class="font-bold text-xl text-success-600">KES
                                {{ number_format($withdrawal->net_amount, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Payout Method Details -->
                <div class="bg-white rounded-3xl shadow-xl border border-neutral-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-primary-50 to-primary-100 p-6 border-b border-primary-200">
                        <h3 class="text-xl font-heading font-semibold text-primary-800">Payout Method</h3>
                        <p class="text-primary-600 text-sm mt-1">Funds destination</p>
                    </div>

                    <div class="p-6">
                        <div class="flex items-center gap-4 mb-4">
                            <div
                                class="w-12 h-12 rounded-2xl flex items-center justify-center {{ $withdrawal->withdrawal_method === 'mpesa' ? 'bg-success-100' : ($withdrawal->withdrawal_method === 'bank_transfer' ? 'bg-primary-100' : 'bg-secondary-100') }}">
                                @if ($withdrawal->withdrawal_method === 'mpesa')
                                    <svg class="w-6 h-6 text-success-600" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z" />
                                    </svg>
                                @elseif($withdrawal->withdrawal_method === 'bank_transfer')
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
                                <h4 class="font-semibold text-lg text-neutral-800">
                                    {{ ucfirst(str_replace('_', ' ', $withdrawal->withdrawal_method)) }}</h4>
                                <p class="text-neutral-500">Primary payout method</p>
                            </div>
                        </div>

                        <div class="bg-neutral-50 rounded-2xl p-4 space-y-3">
                            @if ($withdrawal->withdrawal_method === 'mpesa')
                                <div class="flex justify-between">
                                    <span class="text-neutral-600 text-sm">Provider:</span>
                                    <span
                                        class="font-medium text-neutral-800">{{ $withdrawal->withdrawal_details['provider'] ?? 'M-Pesa' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-neutral-600 text-sm">Phone Number:</span>
                                    <span
                                        class="font-medium text-neutral-800">{{ $withdrawal->withdrawal_details['phone_number'] ?? 'N/A' }}</span>
                                </div>
                            @elseif($withdrawal->withdrawal_method === 'bank_transfer')
                                <div class="flex justify-between">
                                    <span class="text-neutral-600 text-sm">Bank:</span>
                                    <span
                                        class="font-medium text-neutral-800">{{ $withdrawal->withdrawal_details['bank_name'] ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-neutral-600 text-sm">Account Number:</span>
                                    <span
                                        class="font-medium text-neutral-800">{{ $withdrawal->withdrawal_details['account_number'] ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-neutral-600 text-sm">Account Name:</span>
                                    <span
                                        class="font-medium text-neutral-800">{{ $withdrawal->withdrawal_details['account_name'] ?? 'N/A' }}</span>
                                </div>
                            @elseif($withdrawal->withdrawal_method === 'paybill')
                                <div class="flex justify-between">
                                    <span class="text-neutral-600 text-sm">Paybill Number:</span>
                                    <span
                                        class="font-medium text-neutral-800">{{ $withdrawal->withdrawal_details['paybill_number'] ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-neutral-600 text-sm">Account Number:</span>
                                    <span
                                        class="font-medium text-neutral-800">{{ $withdrawal->withdrawal_details['account_number'] ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-neutral-600 text-sm">Account Name:</span>
                                    <span
                                        class="font-medium text-neutral-800">{{ $withdrawal->withdrawal_details['account_name'] ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-neutral-600 text-sm">Provider:</span>
                                    <span
                                        class="font-medium text-neutral-800">{{ $withdrawal->withdrawal_details['provider'] ?? 'N/A' }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Request Information -->
            <div class="mt-8 bg-white rounded-3xl shadow-xl border border-neutral-100 overflow-hidden">
                <div class="bg-gradient-to-r from-neutral-50 to-white p-6 border-b border-neutral-100">
                    <h3 class="text-xl font-heading font-semibold text-neutral-800">Request Information</h3>
                    <p class="text-neutral-500 text-sm mt-1">Tracking and timeline details</p>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="text-center">
                            <div
                                class="w-12 h-12 bg-primary-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-primary-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M9 11H7v6h2v-6zm4 0h-2v6h2v-6zm4 0h-2v6h2v-6zm2-7h-3V2h-2v2H8V2H6v2H3c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H3V9h14v11z" />
                                </svg>
                            </div>
                            <p class="text-neutral-500 text-sm">Request Date</p>
                            <p class="font-semibold text-neutral-800">{{ $withdrawal->created_at->format('M d, Y') }}
                            </p>
                            <p class="text-xs text-neutral-400">{{ $withdrawal->created_at->format('g:i A') }}</p>
                        </div>

                        @if ($withdrawal->approved_at)
                            <div class="text-center">
                                <div
                                    class="w-12 h-12 bg-success-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6 text-success-600" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                                    </svg>
                                </div>
                                <p class="text-neutral-500 text-sm">Approved Date</p>
                                <p class="font-semibold text-neutral-800">
                                    {{ $withdrawal->approved_at->format('M d, Y') }}</p>
                                <p class="text-xs text-neutral-400">{{ $withdrawal->approved_at->format('g:i A') }}
                                </p>
                            </div>
                        @endif

                        @if ($withdrawal->processed_at)
                            <div class="text-center">
                                <div
                                    class="w-12 h-12 bg-primary-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6 text-primary-600" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm5 11H9V9h8v4z" />
                                    </svg>
                                </div>
                                <p class="text-neutral-500 text-sm">Processed Date</p>
                                <p class="font-semibold text-neutral-800">
                                    {{ $withdrawal->processed_at->format('M d, Y') }}</p>
                                <p class="text-xs text-neutral-400">{{ $withdrawal->processed_at->format('g:i A') }}
                                </p>
                            </div>
                        @endif

                        <div class="text-center">
                            <div
                                class="w-12 h-12 bg-neutral-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-neutral-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                                </svg>
                            </div>
                            <p class="text-neutral-500 text-sm">Reference ID</p>
                            <p class="font-mono font-semibold text-neutral-800 text-sm">
                                {{ $withdrawal->request_reference }}</p>
                        </div>
                    </div>

                    @if ($withdrawal->gateway_reference)
                        <div class="mt-6 pt-6 border-t border-neutral-200">
                            <div class="bg-neutral-50 rounded-2xl p-4">
                                <p class="text-neutral-600 text-sm mb-1">Gateway Reference:</p>
                                <p class="font-mono font-semibold text-neutral-800">
                                    {{ $withdrawal->gateway_reference }}</p>
                            </div>
                        </div>
                    @endif

                    @if ($withdrawal->rejection_reason)
                        <div class="mt-6 pt-6 border-t border-neutral-200">
                            <div class="bg-danger-50 border border-danger-200 rounded-2xl p-4">
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-danger-600 mt-0.5" fill="currentColor"
                                        viewBox="0 0 24 24">
                                        <path
                                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" />
                                    </svg>
                                    <div>
                                        <p class="text-danger-800 font-semibold text-sm mb-1">Rejection Reason:</p>
                                        <p class="text-danger-700 text-sm">{{ $withdrawal->rejection_reason }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            @if ($withdrawal->canBeCancelled())
                <div class="mt-8 flex justify-center">
                    <form method="POST" action="{{ route('wallet.withdrawal.cancel', $withdrawal) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                            onclick="return confirm('Are you sure you want to cancel this withdrawal? This action cannot be undone.')"
                            class="inline-flex items-center gap-3 bg-gradient-to-r from-danger-500 to-danger-600 hover:from-danger-600 hover:to-danger-700 text-white font-semibold px-8 py-4 rounded-2xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12L19 6.41Z" />
                            </svg>
                            Cancel Withdrawal Request
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
