<x-app-layout>

    <div class="pt-16 bg-gradient-to-br from-neutral-50 to-neutral-100">
        <div class="relative py-8 sm:py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Header -->
                <div class="bg-gradient-to-r from-success-600 to-success-700 rounded-2xl p-6 text-white shadow-xl">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M21 18V19C21 20.1 20.1 21 19 21H5C3.9 21 3 20.1 3 19V5C3 3.9 3.9 3 5 3H19C20.1 3 21 3.9 21 5V6H12C10.9 6 10 6.9 10 8V16C10 17.1 10.9 18 12 18H21ZM12 16H22V8H12V16ZM16 13.5C15.2 13.5 14.5 12.8 14.5 12C14.5 11.2 15.2 10.5 16 10.5C16.8 10.5 17.5 11.2 17.5 12C17.5 12.8 16.8 13.5 16 13.5Z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-2xl font-heading font-bold">My Wallet</h2>
                                <p class="text-success-100 mt-1">Manage your funds and transactions</p>
                            </div>
                        </div>
                        <a href="{{ route('wallet.withdraw') }}"
                            class="bg-white text-success-600 hover:bg-success-50 px-6 py-3 rounded-xl font-semibold transition-all duration-200 transform hover:scale-[1.02] flex items-center gap-2 shadow-md hover:shadow-lg">

                            Request Withdrawal
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                            </svg>
                        </a>
                    </div>
                </div>

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

                    <!-- Pending Withdrawals -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-2xl shadow-sm border border-neutral-200 overflow-hidden">
                            <div class="bg-gradient-to-r from-secondary-600 to-secondary-700 px-6 py-4">
                                <h3 class="text-lg font-heading font-semibold text-white flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM13 17H11V15H13V17ZM13 13H11V7H13V13Z" />
                                    </svg>
                                    Pending Withdrawals
                                </h3>
                                <p class="text-secondary-100 text-sm mt-1">Awaiting processing</p>
                            </div>

                            <div class="p-6">
                                @if (count($pendingWithdrawals) > 0)
                                    <div class="space-y-4">
                                        @foreach ($pendingWithdrawals as $withdrawal)
                                            <div
                                                class="p-4 bg-gradient-to-r from-secondary-50 to-white border border-secondary-200 rounded-xl">
                                                <div class="flex items-start justify-between mb-3">
                                                    <div>
                                                        <div class="text-lg font-bold text-secondary-800">
                                                            KES {{ number_format($withdrawal->amount, 2) }}
                                                        </div>
                                                        <div class="text-sm text-secondary-600">
                                                            {{ $withdrawal->created_at->format('M d, Y') }}
                                                        </div>
                                                    </div>
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-semibold bg-secondary-100 text-secondary-800 border border-secondary-200">
                                                        {{ ucfirst($withdrawal->status) }}
                                                    </span>
                                                </div>

                                                <div class="text-sm text-neutral-600 mb-3">
                                                    <span class="font-medium">Method:</span>
                                                    {{ ucfirst($withdrawal->withdrawal_method) }}
                                                </div>

                                                @if ($withdrawal->canBeCancelled())
                                                    <form method="POST"
                                                        action="{{ route('wallet.withdrawal.cancel', $withdrawal) }}"
                                                        class="mt-3">
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
                                                            Cancel Withdrawal
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-8">
                                        <div
                                            class="w-16 h-16 bg-gradient-to-br from-secondary-100 to-secondary-200 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                            <svg class="w-8 h-8 text-secondary" fill="currentColor"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM13 17H11V15H13V17ZM13 13H11V7H13V13Z" />
                                            </svg>
                                        </div>
                                        <h4 class="font-semibold text-neutral-800 mb-1">No Pending Withdrawals</h4>
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

            fetch('{{ route('wallet.transactions') }}?limit=100')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateTransactionsContainer(data.transactions);
                    }
                })
                .catch(error => {
                    console.error('Error loading transactions:', error);
                    alert('Failed to load transactions. Please try again.');
                })
                .finally(() => {
                    button.innerHTML = originalContent;
                    button.disabled = false;
                });
        }

        function updateTransactionsContainer(transactions) {
            const container = document.getElementById('transactionsContainer');
            container.innerHTML = '';

            transactions.forEach(transaction => {
                const isCredit = transaction.type === 'credit';
                const statusColors = {
                    'completed': 'bg-success-100 text-success-800 border-success-200',
                    'pending': 'bg-secondary-100 text-secondary-800 border-secondary-200',
                    'failed': 'bg-danger-100 text-danger-800 border-danger-200'
                };
                const statusClass = statusColors[transaction.status] ||
                    'bg-neutral-100 text-neutral-800 border-neutral-200';

                const transactionElement = `
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-neutral-50 to-white border border-neutral-200 rounded-xl hover:shadow-md transition-shadow">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center ${isCredit ? 'bg-success-100' : 'bg-danger-100'}">
                                ${isCredit ? 
                                    '<svg class="w-6 h-6 text-success-600" fill="currentColor" viewBox="0 0 24 24"><path d="M7 14L12 9L17 14H7Z"/></svg>' :
                                    '<svg class="w-6 h-6 text-danger-600" fill="currentColor" viewBox="0 0 24 24"><path d="M7 10L12 15L17 10H7Z"/></svg>'
                                }
                            </div>
                            <div>
                                <h4 class="font-semibold text-neutral-800">${transaction.description}</h4>
                                <div class="flex items-center gap-3 text-sm text-neutral-500">
                                    <span>${new Date(transaction.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' })}</span>
                                    <span>•</span>
                                    <span class="capitalize">${transaction.type}</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold ${isCredit ? 'text-success-600' : 'text-danger-600'}">
                                ${isCredit ? '+' : '-'}KES ${transaction.amount.toFixed(2)}
                            </div>
                            <div class="text-sm text-neutral-500">
                                Balance: KES ${transaction.running_balance.toFixed(2)}
                            </div>
                            <div class="mt-1">
                                <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-semibold border ${statusClass}">
                                    ${transaction.status.charAt(0).toUpperCase() + transaction.status.slice(1)}
                                </span>
                            </div>
                        </div>
                    </div>
                `;
                container.innerHTML += transactionElement;
            });
        }
    </script>
</x-app-layout>
