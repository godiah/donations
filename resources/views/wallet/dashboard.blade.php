<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="py-3">
                <h1 class="text-2xl font-heading font-bold text-neutral-800 tracking-tight">
                    My Wallet
                </h1>
                <p class="text-sm text-neutral-500 font-medium">
                    Manage your funds and transactions
                </p>
            </div>
            <a href="{{ route('wallet.withdraw') }}"
                class="group inline-flex items-center gap-2 px-6 py-3 text-sm bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold rounded-2xl hover:from-primary-600 hover:to-primary-700 focus:outline-none focus:ring-4 focus:ring-primary-200 transition-all duration-300 shadow-lg hover:shadow-xl transform">
                Request Withdrawal
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>
            </a>
        </div>
    </x-slot>

    <div class="pt-6 pb-8">
        <div class="relative">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Wallet Balance Cards -->
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <!-- Total Balance -->
                    <div class="bg-white rounded-2xl shadow-sm border border-neutral-200 overflow-hidden">
                        <div class="bg-gradient-to-r from-primary-500 to-primary-600 p-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M21 18V19C21 20.1 20.1 21 19 21H5C3.9 21 3 20.1 3 19V5C3 3.9 3.9 3 5 3H19C20.1 3 21 3.9 21 5V6H12C10.9 6 10 6.9 10 8V16C10 17.1 10.9 18 12 18H21ZM12 16H22V8H12V16ZM16 13.5C15.2 13.5 14.5 12.8 14.5 12C14.5 11.2 15.2 10.5 16 10.5C16.8 10.5 17.5 11.2 17.5 12C17.5 12.8 16.8 13.5 16 13.5Z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-white">Total Balance</h3>
                                    <p class="text-primary-100 text-sm">All funds</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="text-3xl font-bold text-primary-600">
                                KES {{ number_format($balance, 2) }}
                            </div>
                            <p class="text-sm text-neutral-600 mt-1">Current wallet balance</p>
                        </div>
                    </div>

                    <!-- Available Balance -->
                    <div class="bg-white rounded-2xl shadow-sm border border-neutral-200 overflow-hidden">
                        <div class="bg-gradient-to-r from-success-500 to-success-600 p-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-white">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                                    </svg>

                                </div>
                                <div>
                                    <h3 class="font-semibold text-white">Available</h3>
                                    <p class="text-success-100 text-sm">Ready to withdraw</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="text-3xl font-bold text-success-600">
                                KES {{ number_format($availableBalance, 2) }}
                            </div>
                            <p class="text-sm text-neutral-600 mt-1">Available for withdrawal</p>
                        </div>
                    </div>

                    <!-- Total Received -->
                    <div class="bg-white rounded-2xl shadow-sm border border-neutral-200 overflow-hidden">
                        <div class="bg-gradient-to-r from-secondary-500 to-secondary-600 p-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-white">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-white">Total Received</h3>
                                    <p class="text-secondary-100 text-sm">All time earnings</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="text-3xl font-bold text-secondary-600">
                                KES {{ number_format($wallet->total_received ?? 0, 2) }}
                            </div>
                            <p class="text-sm text-neutral-600 mt-1">Total donations received</p>
                        </div>
                    </div>

                    <!-- Pending Withdrawals -->
                    <div class="bg-white rounded-2xl shadow-sm border border-neutral-200 overflow-hidden">
                        <div class="bg-gradient-to-r from-secondary-500 to-secondary-600 p-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM13 17H11V15H13V17ZM13 13H11V7H13V13Z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-white">Pending</h3>
                                    <p class="text-secondary-100 text-sm">Processing withdrawals</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="text-3xl font-bold text-secondary-600">
                                KES {{ number_format($wallet->pending_withdrawals ?? 0, 2) }}
                            </div>
                            <p class="text-sm text-neutral-600 mt-1">Awaiting processing</p>
                        </div>
                    </div>
                </div>

                <div class="grid gap-6 lg:grid-cols-3">
                    <!-- Recent Transactions -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-2xl shadow-sm border border-neutral-200 overflow-hidden">
                            <div class="bg-gradient-to-r from-neutral-800 to-neutral-700 px-6 py-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3
                                            class="text-lg font-heading font-semibold text-white flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15.042 21.672 13.684 16.6m0 0-2.51 2.225.569-9.47 5.227 7.917-3.286-.672Zm-7.518-.267A8.25 8.25 0 1 1 20.25 10.5M8.288 14.212A5.25 5.25 0 1 1 17.25 10.5" />
                                            </svg>

                                            Recent Transactions
                                        </h3>
                                        <p class="text-neutral-300 text-sm mt-1">Your latest wallet activity</p>
                                    </div>
                                    <button onclick="loadMoreTransactions()"
                                        class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors flex items-center gap-2">
                                        <span>View All</span>
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8.59 16.59L13.17 12L8.59 7.41L10 6L16 12L10 18L8.59 16.59Z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="p-6">
                                @if (count($recentTransactions) > 0)
                                    <div class="space-y-4" id="transactionsContainer">
                                        @foreach ($recentTransactions as $transaction)
                                            <div
                                                class="flex items-center justify-between p-4 bg-gradient-to-r from-neutral-50 to-white border border-neutral-200 rounded-xl hover:shadow-md transition-shadow">
                                                <div class="flex items-center gap-4">
                                                    <div
                                                        class="w-12 h-12 rounded-xl flex items-center justify-center {{ $transaction['type'] === 'credit' ? 'bg-success-100' : 'bg-danger-100' }}">
                                                        @if ($transaction['type'] === 'credit')
                                                            <svg class="w-6 h-6 text-success-600" fill="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path d="M7 14L12 9L17 14H7Z" />
                                                            </svg>
                                                        @else
                                                            <svg class="w-6 h-6 text-danger-600" fill="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path d="M7 10L12 15L17 10H7Z" />
                                                            </svg>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <h4 class="font-semibold text-neutral-800">
                                                            {{ $transaction['description'] }}</h4>
                                                        <div class="flex items-center gap-3 text-sm text-neutral-500">
                                                            <span>{{ \Carbon\Carbon::parse($transaction['created_at'])->format('M d, Y H:i') }}</span>
                                                            <span>•</span>
                                                            <span class="capitalize">{{ $transaction['type'] }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="text-right">
                                                    <div
                                                        class="text-lg font-bold {{ $transaction['type'] === 'credit' ? 'text-success-600' : 'text-danger-600' }}">
                                                        {{ $transaction['type'] === 'credit' ? '+' : '-' }}KES
                                                        {{ number_format($transaction['amount'], 2) }}
                                                    </div>
                                                    <div class="text-sm text-neutral-500">
                                                        Balance: KES
                                                        {{ number_format($transaction['running_balance'], 2) }}
                                                    </div>
                                                    <div class="mt-1">
                                                        @php
                                                            $statusColors = [
                                                                'completed' =>
                                                                    'bg-success-100 text-success-800 border-success-200',
                                                                'pending' =>
                                                                    'bg-secondary-100 text-secondary-800 border-secondary-200',
                                                                'failed' =>
                                                                    'bg-danger-100 text-danger-800 border-danger-200',
                                                            ];
                                                            $statusClass =
                                                                $statusColors[$transaction['status']] ??
                                                                'bg-neutral-100 text-neutral-800 border-neutral-200';
                                                        @endphp
                                                        <span
                                                            class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-semibold border {{ $statusClass }}">
                                                            {{ ucfirst($transaction['status']) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-12">
                                        <div
                                            class="w-24 h-24 bg-gradient-to-br from-neutral-100 to-neutral-200 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                            <svg class="w-12 h-12 text-neutral-400" fill="currentColor"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="M13 3C9.23 3 6.19 5.95 6 9.66L4.08 12.19C3.84 12.5 4.08 13 4.5 13H6V16C6 17.11 6.89 18 8 18H9V21C9 21.55 9.45 22 10 22S11 21.55 11 21V18H13C17.42 18 21 14.42 21 10S17.42 2 13 2M15.5 13C14.67 13 14 12.33 14 11.5S14.67 10 15.5 10 17 10.67 17 11.5 16.33 13 15.5 13M10.5 13C9.67 13 9 12.33 9 11.5S9.67 10 10.5 10 12 10.67 12 11.5 11.33 13 10.5 13Z" />
                                            </svg>
                                        </div>
                                        <h4 class="text-lg font-heading font-semibold text-neutral-800 mb-2">No
                                            Transactions Yet</h4>
                                        <p class="text-neutral-600">Your transaction history will appear here once you
                                            start receiving donations.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-1 space-y-6">
                        <!-- Active Withdrawals (Pending & Approved) -->
                        <div class="bg-white rounded-2xl shadow-sm border border-neutral-200 overflow-hidden">
                            <div class="bg-gradient-to-r from-primary-600 to-primary-700 px-6 py-4">
                                <h3 class="text-lg font-heading font-semibold text-white flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM13 17H11V15H13V17ZM13 13H11V7H13V13Z" />
                                    </svg>
                                    Active Withdrawals
                                </h3>
                                <p class="text-primary-100 text-sm mt-1">Pending & approved requests</p>
                            </div>

                            <div class="p-6">
                                @if (count($activeWithdrawals) > 0)
                                    <div class="space-y-4">
                                        @foreach ($activeWithdrawals as $withdrawal)
                                            @php
                                                // Determine theme based on status
                                                $isPending = $withdrawal->status === 'pending';
                                                $isApproved = $withdrawal->status === 'approved';

                                                if ($isPending) {
                                                    $bgClass =
                                                        'bg-gradient-to-r from-secondary-50 to-white border border-secondary-200';
                                                    $amountClass = 'text-secondary-800';
                                                    $dateClass = 'text-secondary-600';
                                                    $statusClass =
                                                        'bg-secondary-100 text-secondary-800 border border-secondary-200';
                                                } else {
                                                    $bgClass =
                                                        'bg-gradient-to-r from-success-50 to-white border border-success-200';
                                                    $amountClass = 'text-success-700';
                                                    $dateClass = 'text-success-600';
                                                    $statusClass =
                                                        'bg-success-100 text-success-700 border border-success-200';
                                                }
                                            @endphp

                                            <div class="p-4 {{ $bgClass }} rounded-xl">
                                                <div class="flex items-start justify-between mb-3">
                                                    <div>
                                                        <div class="text-lg font-bold {{ $amountClass }}">
                                                            KES {{ number_format($withdrawal->amount, 2) }}
                                                        </div>
                                                        <div class="text-sm {{ $dateClass }}">
                                                            {{ $withdrawal->created_at->format('M d, Y') }}
                                                        </div>
                                                        @if ($isApproved && $withdrawal->approved_at)
                                                            <div class="text-xs text-success-500 mt-1">
                                                                Approved
                                                                {{ $withdrawal->approved_at->format('M d, Y') }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="flex flex-col items-end gap-2">
                                                        <span
                                                            class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-semibold {{ $statusClass }}">
                                                            @if ($isPending)
                                                                <svg class="w-3 h-3 mr-1" fill="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path
                                                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" />
                                                                </svg>
                                                            @else
                                                                <svg class="w-3 h-3 mr-1" fill="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path
                                                                        d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                                                                </svg>
                                                            @endif
                                                            {{ ucfirst($withdrawal->status) }}
                                                        </span>

                                                        @if ($isApproved)
                                                            <span
                                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                                                <svg class="w-3 h-3 mr-1" fill="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path
                                                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm5 11H9V9h8v4z" />
                                                                </svg>
                                                                Processing
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="text-sm text-neutral-600 mb-3">
                                                    <span class="font-medium">Method:</span>
                                                    {{ ucfirst(str_replace('_', ' ', $withdrawal->withdrawal_method)) }}

                                                    @if ($withdrawal->fee_amount > 0)
                                                        <span class="ml-3">
                                                            <span class="font-medium">Fee:</span>
                                                            KES {{ number_format($withdrawal->fee_amount, 2) }}
                                                        </span>
                                                    @endif
                                                </div>

                                                <!-- Net Amount Display -->
                                                <div class="text-sm text-neutral-600 mb-3">
                                                    <span class="font-medium">You'll receive:</span>
                                                    <span
                                                        class="font-bold {{ $isPending ? 'text-secondary-700' : 'text-success-700' }}">
                                                        KES {{ number_format($withdrawal->net_amount, 2) }}
                                                    </span>
                                                </div>

                                                <!-- Action Buttons -->
                                                <div class="flex gap-2">
                                                    <!-- View Details Button -->
                                                    <a href="{{ route('wallet.withdrawal.show', $withdrawal) }}"
                                                        class="flex-1 bg-primary-100 hover:bg-primary-200 text-primary-700 px-3 py-2 rounded-lg text-sm font-semibold transition-colors flex items-center justify-center gap-2">
                                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                            <path
                                                                d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" />
                                                        </svg>
                                                        View Details
                                                    </a>

                                                    <!-- Cancel Button (if cancellable) -->
                                                    @if ($withdrawal->canBeCancelled())
                                                        <form method="POST"
                                                            action="{{ route('wallet.withdrawal.cancel', $withdrawal) }}"
                                                            class="flex-1">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit"
                                                                onclick="return confirm('Are you sure you want to cancel this withdrawal?')"
                                                                class="w-full bg-danger-100 hover:bg-danger-200 text-danger-700 px-3 py-2 rounded-lg text-sm font-semibold transition-colors flex items-center justify-center gap-2">
                                                                <svg class="w-4 h-4" fill="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path
                                                                        d="M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12L19 6.41Z" />
                                                                </svg>
                                                                Cancel
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-8">
                                        <div
                                            class="w-16 h-16 bg-gradient-to-br from-primary-100 to-primary-200 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                            <svg class="w-8 h-8 text-primary-600" fill="currentColor"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM13 17H11V15H13V17ZM13 13H11V7H13V13Z" />
                                            </svg>
                                        </div>
                                        <h4 class="font-semibold text-neutral-800 mb-1">No Active Withdrawals</h4>
                                        <p class="text-sm text-neutral-600">All withdrawals are up to date</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function loadMoreTransactions() {
            // Show loading state
            const button = event.target.closest('button');
            const originalContent = button.innerHTML;
            button.innerHTML = `
        <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Loading...
    `;
            button.disabled = true;

            // Redirect to the transactions view page
            window.location.href = '{{ route('wallet.transactions') }}';
        }
    </script>
</x-app-layout>
