<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="py-3">
                <h1 class="text-2xl font-heading font-bold text-neutral-800 tracking-tight">
                    Withdrawal Request Details
                </h1>
                <p class="text-sm text-neutral-500 font-medium">
                    Review and manage withdrawal request #{{ $withdrawal->request_reference }}
                </p>
            </div>
            <a href="{{ route('admin.withdrawals.index') }}"
                class="group inline-flex items-center gap-2 px-6 py-3 text-sm bg-gradient-to-r from-neutral-600 to-neutral-700 text-white font-semibold rounded-2xl hover:from-neutral-700 hover:to-neutral-800 focus:outline-none focus:ring-4 focus:ring-neutral-200 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                <svg class="w-5 h-5 transition-transform group-hover:-translate-x-1" fill="currentColor"
                    viewBox="0 0 24 24">
                    <path d="M15.41 7.41L14 6L8 12L14 18L15.41 16.59L10.83 12L15.41 7.41Z" />
                </svg>
                <span>Back to Withdrawals</span>
            </a>
        </div>
    </x-slot>

    <div class="pt-8 pb-12 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Status Header with Actions -->
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
                        <div class="flex-1">
                            <div class="flex items-center gap-4 mb-3">
                                <h2 class="text-4xl font-bold font-heading">KES
                                    {{ number_format($withdrawal->amount, 2) }}</h2>
                                <span
                                    class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-white bg-opacity-20 backdrop-blur-sm">
                                    <span class="w-2 h-2 bg-white rounded-full mr-2"></span>
                                    {{ ucfirst($withdrawal->status) }}
                                </span>
                                @if ($withdrawal->status === 'pending')
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-400 bg-opacity-20 text-yellow-100 animate-pulse">
                                        ⏳ Awaiting Review
                                    </span>
                                @endif
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-white text-opacity-90">
                                <div>
                                    <p class="text-sm opacity-75">Reference</p>
                                    <p class="font-mono font-semibold">{{ $withdrawal->request_reference }}</p>
                                </div>
                                <div>
                                    <p class="text-sm opacity-75">Requested</p>
                                    <p class="font-semibold">{{ $withdrawal->created_at->format('M j, Y g:i A') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm opacity-75">User</p>
                                    <p class="font-semibold">{{ $withdrawal->user->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        @if ($withdrawal->isPending())
                            <div class="flex gap-3 ml-6">
                                <button onclick="openApprovalModal()"
                                    class="group bg-white bg-opacity-20 hover:bg-opacity-30 backdrop-blur-sm text-white font-semibold px-6 py-3 rounded-2xl transition-all duration-300 flex items-center gap-2 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                    <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="currentColor"
                                        viewBox="0 0 24 24">
                                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                                    </svg>
                                    Approve
                                </button>
                                <button onclick="openRejectionModal()"
                                    class="group bg-white bg-opacity-20 hover:bg-opacity-30 backdrop-blur-sm text-white font-semibold px-6 py-3 rounded-2xl transition-all duration-300 flex items-center gap-2 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                    <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="currentColor"
                                        viewBox="0 0 24 24">
                                        <path
                                            d="M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12L19 6.41Z" />
                                    </svg>
                                    Reject
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
                <!-- Main Content -->
                <div class="xl:col-span-2 space-y-8">
                    <!-- Amount Breakdown -->
                    <div class="bg-white rounded-3xl shadow-xl border border-neutral-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-primary-50 to-primary-100 p-6 border-b border-primary-200">
                            <h3 class="text-xl font-heading font-semibold text-primary-800">Financial Breakdown</h3>
                            <p class="text-primary-600 text-sm mt-1">Detailed amount calculation</p>
                        </div>

                        <div class="p-6">
                            <div class="space-y-4">
                                <div class="flex justify-between items-center py-4 border-b border-neutral-100">
                                    <div>
                                        <span class="text-neutral-600">Gross Withdrawal Amount</span>
                                        <p class="text-xs text-neutral-400">Amount requested by user</p>
                                    </div>
                                    <span class="font-bold text-2xl text-neutral-800">KES
                                        {{ number_format($withdrawal->amount, 2) }}</span>
                                </div>

                                <div class="flex justify-between items-center py-4 border-b border-neutral-100">
                                    <div>
                                        <span class="text-neutral-600">Processing Fee</span>
                                        <p class="text-xs text-neutral-400">Platform processing charge
                                            ({{ round(($withdrawal->fee_amount / $withdrawal->amount) * 100, 2) }}%)</p>
                                    </div>
                                    <span class="font-bold text-2xl text-danger-600">- KES
                                        {{ number_format($withdrawal->fee_amount, 2) }}</span>
                                </div>

                                <div class="flex justify-between items-center py-4 bg-success-50 rounded-2xl px-6">
                                    <div>
                                        <span class="text-success-700 font-semibold text-lg">Net Payout Amount</span>
                                        <p class="text-xs text-success-600">Amount to be transferred</p>
                                    </div>
                                    <span class="font-bold text-3xl text-success-600">KES
                                        {{ number_format($withdrawal->net_amount, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payout Method Details -->
                    <div class="bg-white rounded-3xl shadow-xl border border-neutral-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-success-50 to-success-100 p-6 border-b border-success-200">
                            <h3 class="text-xl font-heading font-semibold text-success-800">Payout Destination</h3>
                            <p class="text-success-600 text-sm mt-1">Where funds will be transferred</p>
                        </div>

                        <div class="p-6">
                            <div class="flex items-center gap-4 mb-6">
                                <div
                                    class="w-16 h-16 rounded-2xl flex items-center justify-center {{ $withdrawal->withdrawal_method === 'mpesa' ? 'bg-success-100' : ($withdrawal->withdrawal_method === 'bank_transfer' ? 'bg-primary-100' : 'bg-secondary-100') }}">
                                    @if ($withdrawal->withdrawal_method === 'mpesa')
                                        <svg class="w-8 h-8 text-success-600" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z" />
                                        </svg>
                                    @elseif($withdrawal->withdrawal_method === 'bank_transfer')
                                        <svg class="w-8 h-8 text-primary-600" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M11.5 1L2 6v2h19V6m-5 4v7h3v-7M2 17v2h19v-2M6 10v7h3v-7H6m6 0v7h3v-7h-3Z" />
                                        </svg>
                                    @else
                                        <svg class="w-8 h-8 text-secondary-600" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z" />
                                        </svg>
                                    @endif
                                </div>
                                <div>
                                    <h4 class="font-semibold text-xl text-neutral-800">
                                        {{ ucfirst(str_replace('_', ' ', $withdrawal->withdrawal_method)) }}</h4>
                                    <p class="text-neutral-500">Payment method</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @if ($withdrawal->withdrawal_method === 'mpesa')
                                    <div class="bg-neutral-50 rounded-2xl p-4">
                                        <p class="text-neutral-500 text-sm mb-1">Provider</p>
                                        <p class="font-semibold text-neutral-800">
                                            {{ $withdrawal->withdrawal_details['provider'] ?? 'M-Pesa' }}</p>
                                    </div>
                                    <div class="bg-neutral-50 rounded-2xl p-4">
                                        <p class="text-neutral-500 text-sm mb-1">Phone Number</p>
                                        <p class="font-mono font-semibold text-neutral-800">
                                            {{ $withdrawal->withdrawal_details['phone_number'] ?? 'N/A' }}</p>
                                    </div>
                                @elseif($withdrawal->withdrawal_method === 'bank_transfer')
                                    <div class="bg-neutral-50 rounded-2xl p-4">
                                        <p class="text-neutral-500 text-sm mb-1">Bank Name</p>
                                        <p class="font-semibold text-neutral-800">
                                            {{ $withdrawal->withdrawal_details['bank_name'] ?? 'N/A' }}</p>
                                    </div>
                                    <div class="bg-neutral-50 rounded-2xl p-4">
                                        <p class="text-neutral-500 text-sm mb-1">Account Number</p>
                                        <p class="font-mono font-semibold text-neutral-800">
                                            {{ $withdrawal->withdrawal_details['account_number'] ?? 'N/A' }}</p>
                                    </div>
                                    <div class="bg-neutral-50 rounded-2xl p-4 md:col-span-2">
                                        <p class="text-neutral-500 text-sm mb-1">Account Name</p>
                                        <p class="font-semibold text-neutral-800">
                                            {{ $withdrawal->withdrawal_details['account_name'] ?? 'N/A' }}</p>
                                    </div>
                                @elseif($withdrawal->withdrawal_method === 'paybill')
                                    <div class="bg-neutral-50 rounded-2xl p-4">
                                        <p class="text-neutral-500 text-sm mb-1">Paybill Number</p>
                                        <p class="font-mono font-semibold text-neutral-800">
                                            {{ $withdrawal->withdrawal_details['paybill_number'] ?? 'N/A' }}</p>
                                    </div>
                                    <div class="bg-neutral-50 rounded-2xl p-4">
                                        <p class="text-neutral-500 text-sm mb-1">Account Number</p>
                                        <p class="font-mono font-semibold text-neutral-800">
                                            {{ $withdrawal->withdrawal_details['account_number'] ?? 'N/A' }}</p>
                                    </div>
                                    <div class="bg-neutral-50 rounded-2xl p-4">
                                        <p class="text-neutral-500 text-sm mb-1">Account Name</p>
                                        <p class="font-semibold text-neutral-800">
                                            {{ $withdrawal->withdrawal_details['account_name'] ?? 'N/A' }}</p>
                                    </div>
                                    <div class="bg-neutral-50 rounded-2xl p-4">
                                        <p class="text-neutral-500 text-sm mb-1">Provider</p>
                                        <p class="font-semibold text-neutral-800">
                                            {{ $withdrawal->withdrawal_details['provider'] ?? 'N/A' }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Rejection Reason (if exists) -->
                    @if ($withdrawal->rejection_reason)
                        <div class="bg-white rounded-3xl shadow-xl border border-danger-200 overflow-hidden">
                            <div class="bg-gradient-to-r from-danger-50 to-danger-100 p-6 border-b border-danger-200">
                                <h3 class="text-xl font-heading font-semibold text-danger-800">Rejection Details</h3>
                                <p class="text-danger-600 text-sm mt-1">Reason for rejection</p>
                            </div>

                            <div class="p-6">
                                <div class="flex items-start gap-3">
                                    <svg class="w-6 h-6 text-danger-600 mt-1" fill="currentColor"
                                        viewBox="0 0 24 24">
                                        <path
                                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" />
                                    </svg>
                                    <div>
                                        <p class="text-danger-800 font-semibold mb-2">Rejection Reason:</p>
                                        <p class="text-danger-700">{{ $withdrawal->rejection_reason }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- User Information -->
                    <div class="bg-white rounded-3xl shadow-xl border border-neutral-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-neutral-50 to-white p-6 border-b border-neutral-100">
                            <h3 class="text-lg font-heading font-semibold text-neutral-800">User Information</h3>
                        </div>

                        <div class="p-6 space-y-4">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-primary-100 rounded-2xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-primary-600" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-semibold text-neutral-800">{{ $withdrawal->user->name ?? 'N/A' }}
                                    </p>
                                    <p class="text-neutral-500 text-sm">{{ $withdrawal->user->email ?? 'N/A' }}</p>
                                </div>
                            </div>

                            <div class="space-y-3 pt-4 border-t border-neutral-100">
                                <div class="flex justify-between">
                                    <span class="text-neutral-500 text-sm">User ID:</span>
                                    <span class="font-mono text-sm">{{ $withdrawal->user_id }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-neutral-500 text-sm">Wallet Balance:</span>
                                    <span class="font-semibold text-success-600">KES
                                        {{ number_format($withdrawal->wallet->balance ?? 0, 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-neutral-500 text-sm">Pending Withdrawals:</span>
                                    <span class="font-semibold text-secondary-600">KES
                                        {{ number_format($withdrawal->wallet->pending_withdrawals ?? 0, 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-neutral-500 text-sm">Total Withdrawn:</span>
                                    <span class="font-semibold text-neutral-600">KES
                                        {{ number_format($withdrawal->wallet->total_withdrawn ?? 0, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Request Timeline -->
                    <div class="bg-white rounded-3xl shadow-xl border border-neutral-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-neutral-50 to-white p-6 border-b border-neutral-100">
                            <h3 class="text-lg font-heading font-semibold text-neutral-800">Timeline</h3>
                        </div>

                        <div class="p-6 space-y-4">
                            <!-- Request Created -->
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center mt-1">
                                    <svg class="w-4 h-4 text-primary-600" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm5 11H9V9h8v4z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-neutral-800">Request Created</p>
                                    <p class="text-neutral-500 text-sm">
                                        {{ $withdrawal->created_at->format('M j, Y g:i A') }}</p>
                                </div>
                            </div>

                            @if ($withdrawal->approved_at)
                                <div class="flex items-start gap-3">
                                    <div
                                        class="w-8 h-8 bg-success-100 rounded-full flex items-center justify-center mt-1">
                                        <svg class="w-4 h-4 text-success-600" fill="currentColor"
                                            viewBox="0 0 24 24">
                                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium text-neutral-800">Approved</p>
                                        <p class="text-neutral-500 text-sm">
                                            {{ $withdrawal->approved_at->format('M j, Y g:i A') }}</p>
                                    </div>
                                </div>
                            @endif

                            @if ($withdrawal->processed_at)
                                <div class="flex items-start gap-3">
                                    <div
                                        class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center mt-1">
                                        <svg class="w-4 h-4 text-primary-600" fill="currentColor"
                                            viewBox="0 0 24 24">
                                            <path
                                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm5 11H9V9h8v4z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium text-neutral-800">Processed</p>
                                        <p class="text-neutral-500 text-sm">
                                            {{ $withdrawal->processed_at->format('M j, Y g:i A') }}</p>
                                    </div>
                                </div>
                            @endif

                            @if ($withdrawal->rejection_reason)
                                <div class="flex items-start gap-3">
                                    <div
                                        class="w-8 h-8 bg-danger-100 rounded-full flex items-center justify-center mt-1">
                                        <svg class="w-4 h-4 text-danger-600" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12L19 6.41Z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium text-neutral-800">Rejected</p>
                                        <p class="text-neutral-500 text-sm">
                                            {{ $withdrawal->updated_at->format('M j, Y g:i A') }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Additional Details -->
                    <div class="bg-white rounded-3xl shadow-xl border border-neutral-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-neutral-50 to-white p-6 border-b border-neutral-100">
                            <h3 class="text-lg font-heading font-semibold text-neutral-800">Technical Details</h3>
                        </div>

                        <div class="p-6 space-y-3">
                            <div>
                                <p class="text-neutral-500 text-sm">Request ID</p>
                                <p class="font-mono text-sm font-medium">{{ $withdrawal->id }}</p>
                            </div>

                            <div>
                                <p class="text-neutral-500 text-sm">Wallet ID</p>
                                <p class="font-mono text-sm font-medium">{{ $withdrawal->wallet_id }}</p>
                            </div>

                            @if ($withdrawal->payout_method_id)
                                <div>
                                    <p class="text-neutral-500 text-sm">Payout Method ID</p>
                                    <p class="font-mono text-sm font-medium">{{ $withdrawal->payout_method_id }}</p>
                                </div>
                            @endif

                            @if ($withdrawal->gateway_reference)
                                <div>
                                    <p class="text-neutral-500 text-sm">Gateway Reference</p>
                                    <p class="font-mono text-sm font-medium">{{ $withdrawal->gateway_reference }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Approval Modal -->
    <div id="approvalModal"
        class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full mx-4 transform scale-95 transition-transform">
            <div class="p-6">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 bg-success-100 rounded-2xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-success-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-heading font-semibold text-neutral-800">Approve Withdrawal</h3>
                        <p class="text-neutral-500 text-sm">Confirm withdrawal approval</p>
                    </div>
                </div>

                <div class="bg-success-50 border border-success-200 rounded-2xl p-4 mb-6">
                    <p class="text-success-800 text-sm">
                        <strong>KES {{ number_format($withdrawal->net_amount, 2) }}</strong> will be processed for
                        transfer to the user's payout method.
                    </p>
                    <p class="text-success-600 text-xs mt-1">This action cannot be undone.</p>
                </div>

                <div class="flex gap-3">
                    <button onclick="closeApprovalModal()"
                        class="flex-1 bg-neutral-100 hover:bg-neutral-200 text-neutral-700 font-semibold py-3 rounded-2xl transition-colors">
                        Cancel
                    </button>
                    <form method="POST" action="{{ route('admin.withdrawals.approve', $withdrawal) }}"
                        class="flex-1">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                            class="w-full bg-gradient-to-r from-success-500 to-success-600 hover:from-success-600 hover:to-success-700 text-white font-semibold py-3 rounded-2xl transition-all duration-300 shadow-lg hover:shadow-xl">
                            Approve
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Rejection Modal -->
    <div id="rejectionModal"
        class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full mx-4 transform scale-95 transition-transform">
            <form method="POST" action="{{ route('admin.withdrawals.reject', $withdrawal) }}">
                @csrf
                @method('PATCH')
                <div class="p-6">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 bg-danger-100 rounded-2xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-danger-600" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12L19 6.41Z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-heading font-semibold text-neutral-800">Reject Withdrawal</h3>
                            <p class="text-neutral-500 text-sm">Provide a reason for rejection</p>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="rejection_reason" class="block text-sm font-semibold text-neutral-700 mb-3">
                            Rejection Reason
                        </label>
                        <textarea name="rejection_reason" id="rejection_reason" rows="4" required maxlength="500"
                            placeholder="Please provide a clear reason for rejecting this withdrawal request..."
                            class="w-full px-4 py-3 border border-neutral-200 rounded-2xl  transition-all duration-200 resize-none"></textarea>
                        <p class="text-xs text-neutral-400 mt-2" id="charCounter">0/500 characters</p>
                    </div>

                    <div class="bg-danger-50 border border-danger-200 rounded-2xl p-4 mb-6">
                        <p class="text-danger-800 text-sm">
                            <strong>Warning:</strong> This will cancel the withdrawal request and restore the user's
                            available balance.
                        </p>
                    </div>

                    <div class="flex gap-3">
                        <button type="button" onclick="closeRejectionModal()"
                            class="flex-1 bg-neutral-100 hover:bg-neutral-200 text-neutral-700 font-semibold py-3 rounded-2xl transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                            class="flex-1 bg-gradient-to-r from-danger-500 to-danger-600 hover:from-danger-600 hover:to-danger-700 text-white font-semibold py-3 rounded-2xl transition-all duration-300 shadow-lg hover:shadow-xl">
                            Reject
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openApprovalModal() {
            const modal = document.getElementById('approvalModal');
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.querySelector('.transform').classList.remove('scale-95');
                modal.querySelector('.transform').classList.add('scale-100');
            }, 10);
        }

        function closeApprovalModal() {
            const modal = document.getElementById('approvalModal');
            modal.querySelector('.transform').classList.remove('scale-100');
            modal.querySelector('.transform').classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 150);
        }

        function openRejectionModal() {
            const modal = document.getElementById('rejectionModal');
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.querySelector('.transform').classList.remove('scale-95');
                modal.querySelector('.transform').classList.add('scale-100');
            }, 10);
        }

        function closeRejectionModal() {
            const modal = document.getElementById('rejectionModal');
            modal.querySelector('.transform').classList.remove('scale-100');
            modal.querySelector('.transform').classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 150);
        }

        // Close modals when clicking outside
        document.getElementById('approvalModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeApprovalModal();
            }
        });

        document.getElementById('rejectionModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeRejectionModal();
            }
        });

        // Close modals with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeApprovalModal();
                closeRejectionModal();
            }
        });

        // Character counter for rejection reason
        const textarea = document.getElementById('rejection_reason');
        const charCounter = document.getElementById('charCounter');

        if (textarea && charCounter) {
            textarea.addEventListener('input', function() {
                const maxLength = 500;
                const currentLength = this.value.length;
                charCounter.textContent = `${currentLength}/${maxLength} characters`;

                if (currentLength > maxLength - 50) {
                    charCounter.classList.add('text-danger-500');
                    charCounter.classList.remove('text-neutral-400');
                } else {
                    charCounter.classList.remove('text-danger-500');
                    charCounter.classList.add('text-neutral-400');
                }
            });
        }

        // Auto-focus on rejection reason when modal opens
        function openRejectionModal() {
            const modal = document.getElementById('rejectionModal');
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.querySelector('.transform').classList.remove('scale-95');
                modal.querySelector('.transform').classList.add('scale-100');
                // Focus on textarea after animation
                setTimeout(() => {
                    document.getElementById('rejection_reason').focus();
                }, 150);
            }, 10);
        }
    </script>
</x-app-layout>
