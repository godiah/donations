<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="space-y-2 py-3">
                <h1 class="text-2xl font-heading font-bold text-neutral-800 tracking-tight">
                    Transaction History
                </h1>
                <p class="text-sm text-neutral-500 font-medium">
                    Track and manage all your wallet transactions
                </p>
            </div>
            <a href="{{ route('wallet.dashboard') }}"
                class="group inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold rounded-2xl hover:from-primary-600 hover:to-primary-700 focus:outline-none focus:ring-4 focus:ring-primary-200 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                <svg class="w-5 h-5 transition-transform group-hover:-translate-x-1" fill="currentColor"
                    viewBox="0 0 24 24">
                    <path d="M15.41 7.41L14 6L8 12L14 18L15.41 16.59L10.83 12L15.41 7.41Z" />
                </svg>
                <span>Back to Dashboard</span>
            </a>
        </div>
    </x-slot>

    <div class="pt-6 pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Filter and Search Section -->
            {{-- <div class="bg-white rounded-3xl shadow-lg border border-neutral-100 p-6 mb-8">
                <div class="flex flex-col lg:flex-row gap-4 items-center">
                    <div class="flex-1">
                        <div class="relative">
                            <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-neutral-400"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input type="text" placeholder="Search transactions..."
                                class="w-full pl-12 pr-4 py-3 border border-neutral-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-primary-100 focus:border-primary-500 transition-all duration-200">
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <select
                            class="px-4 py-3 border border-neutral-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-primary-100 focus:border-primary-500 transition-all duration-200">
                            <option value="">All Types</option>
                            <option value="credit">Credits</option>
                            <option value="debit">Debits</option>
                        </select>
                        <select
                            class="px-4 py-3 border border-neutral-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-primary-100 focus:border-primary-500 transition-all duration-200">
                            <option value="">All Status</option>
                            <option value="completed">Completed</option>
                            <option value="pending">Pending</option>
                            <option value="failed">Failed</option>
                        </select>
                    </div>
                </div>
            </div> --}}

            <!-- Transactions List -->
            <div id="transactionsContainer" class="space-y-4">
                @forelse($transactions as $transaction)
                    @php
                        $isCredit = $transaction['type'] === 'credit';
                        $statusColors = [
                            'completed' => 'bg-success-100 text-success-800 border-success-200',
                            'pending' => 'bg-secondary-100 text-secondary-800 border-secondary-200',
                            'failed' => 'bg-danger-100 text-danger-800 border-danger-200',
                        ];
                        $statusClass =
                            $statusColors[$transaction['status']] ??
                            'bg-neutral-100 text-neutral-800 border-neutral-200';
                    @endphp

                    <div
                        class="group bg-white rounded-3xl shadow-lg hover:shadow-xl border border-neutral-100 hover:border-primary-200 transition-all duration-300 overflow-hidden">
                        <div class="flex items-center justify-between p-6">
                            <div class="flex items-center gap-5">
                                <div class="relative">
                                    <div
                                        class="w-16 h-16 rounded-2xl flex items-center justify-center {{ $isCredit ? 'bg-gradient-to-br from-success-100 to-success-200' : 'bg-gradient-to-br from-danger-100 to-danger-200' }} group-hover:scale-105 transition-transform duration-300">
                                        @if ($isCredit)
                                            <svg class="w-8 h-8 text-success-600" fill="currentColor"
                                                viewBox="0 0 24 24">
                                                <path d="M7 14L12 9L17 14H7Z" />
                                            </svg>
                                        @else
                                            <svg class="w-8 h-8 text-danger-600" fill="currentColor"
                                                viewBox="0 0 24 24">
                                                <path d="M7 10L12 15L17 10H7Z" />
                                            </svg>
                                        @endif
                                    </div>
                                    <div
                                        class="absolute -top-1 -right-1 w-5 h-5 rounded-full {{ $isCredit ? 'bg-success-500' : 'bg-danger-500' }} flex items-center justify-center">
                                        <span class="text-white text-xs font-bold">{{ $isCredit ? '+' : '-' }}</span>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <h4
                                        class="font-heading font-semibold text-lg text-neutral-800 group-hover:text-primary-600 transition-colors">
                                        {{ $transaction['description'] }}
                                    </h4>
                                    <div class="flex items-center gap-4 text-sm text-neutral-500">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                                            </svg>
                                            <span
                                                class="font-medium">{{ \Carbon\Carbon::parse($transaction['created_at'])->format('M j, Y g:i A') }}</span>
                                        </div>
                                        <div class="w-1 h-1 bg-neutral-300 rounded-full"></div>
                                        <span
                                            class="capitalize font-medium bg-neutral-100 px-3 py-1 rounded-full">{{ $transaction['type'] }}</span>
                                        @if ($transaction['reference'])
                                            <div class="w-1 h-1 bg-neutral-300 rounded-full"></div>
                                            <span
                                                class="font-mono text-xs bg-neutral-100 px-3 py-1 rounded-full">{{ $transaction['reference'] }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="text-right space-y-2">
                                <div
                                    class="text-2xl font-bold font-heading {{ $isCredit ? 'text-success-600' : 'text-danger-600' }}">
                                    {{ $isCredit ? '+' : '-' }}KES {{ number_format($transaction['amount'], 2) }}
                                </div>
                                <div class="text-sm text-neutral-500 bg-neutral-50 px-3 py-1 rounded-full">
                                    Balance: KES {{ number_format($transaction['running_balance'], 2) }}
                                </div>
                                @if ($transaction['fee_amount'] > 0)
                                    <div class="text-xs text-neutral-400 bg-neutral-50 px-3 py-1 rounded-full">
                                        Fee: KES {{ number_format($transaction['fee_amount'], 2) }}
                                    </div>
                                @endif
                                <div class="flex justify-end">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border-2 {{ $statusClass }} shadow-sm">
                                        <span
                                            class="w-2 h-2 rounded-full {{ $transaction['status'] === 'completed' ? 'bg-success-500' : ($transaction['status'] === 'pending' ? 'bg-secondary-500' : 'bg-danger-500') }} mr-2"></span>
                                        {{ ucfirst($transaction['status']) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Expandable Details -->
                        <div
                            class="hidden group-hover:block border-t border-neutral-100 px-6 py-4 bg-gradient-to-r from-neutral-50 to-white">
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center gap-4">
                                    <span class="text-neutral-500">Transaction ID:</span>
                                    <span class="font-mono text-neutral-700">{{ $transaction['id'] ?? 'N/A' }}</span>
                                </div>
                                {{-- <button class="text-primary-600 hover:text-primary-700 font-medium transition-colors">
                                    View Details
                                </button> --}}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-20">
                        <div
                            class="w-24 h-24 mx-auto mb-6 rounded-full bg-gradient-to-br from-neutral-100 to-neutral-200 flex items-center justify-center">
                            <svg class="w-12 h-12 text-neutral-400" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-heading font-semibold text-neutral-800 mb-3">No Transactions Found</h3>
                        <p class="text-neutral-600 mb-6">Start making transactions to see your history here.</p>
                        <a href="{{ route('wallet.dashboard') }}"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold rounded-2xl hover:from-primary-600 hover:to-primary-700 transition-all duration-300 shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm5 11H9V9h8v4z" />
                            </svg>
                            Make a Transaction
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- Enhanced Load More Button -->
            @if ($hasMore)
                <div class="mt-12 text-center">
                    <div
                        class="inline-flex flex-col items-center gap-4 bg-white rounded-3xl shadow-lg border border-neutral-100 p-8">
                        <p class="text-sm text-neutral-500 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                            </svg>
                            Showing {{ $currentCount }} of {{ $totalTransactions }} transactions
                        </p>
                        <button id="loadMoreBtn" onclick="loadMoreTransactions()"
                            class="group bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white px-8 py-4 rounded-2xl font-semibold transition-all duration-300 flex items-center gap-3 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                            <span class="load-more-text">Load More Transactions</span>
                            <svg class="w-5 h-5 load-more-icon transition-transform group-hover:translate-x-1"
                                fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 4L10.59 5.41L16.17 11H4V13H16.17L10.59 18.59L12 20L20 12L12 4Z" />
                            </svg>
                            <svg class="w-5 h-5 loading-spinner hidden animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        let currentPage = 1;
        let isLoading = false;

        function loadMoreTransactions() {
            if (isLoading) return;

            isLoading = true;
            currentPage++;

            // Update button state
            const button = document.getElementById('loadMoreBtn');
            const loadMoreText = button.querySelector('.load-more-text');
            const loadMoreIcon = button.querySelector('.load-more-icon');
            const loadingSpinner = button.querySelector('.loading-spinner');

            loadMoreText.textContent = 'Loading...';
            loadMoreIcon.classList.add('hidden');
            loadingSpinner.classList.remove('hidden');
            button.disabled = true;

            fetch(`{{ route('wallet.transactions') }}?page=${currentPage}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.transactions.length > 0) {
                        appendTransactions(data.transactions);

                        // Update counter
                        document.querySelector('.text-neutral-500').innerHTML =
                            `<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>Showing ${data.currentCount} of ${data.totalCount} transactions`;

                        // Hide load more button if no more transactions
                        if (!data.hasMore) {
                            button.parentElement.style.display = 'none';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error loading transactions:', error);
                    alert('Failed to load more transactions. Please try again.');
                    currentPage--; // Rollback page increment
                })
                .finally(() => {
                    // Reset button state
                    loadMoreText.textContent = 'Load More Transactions';
                    loadMoreIcon.classList.remove('hidden');
                    loadingSpinner.classList.add('hidden');
                    button.disabled = false;
                    isLoading = false;
                });
        }

        function appendTransactions(transactions) {
            const container = document.getElementById('transactionsContainer');

            transactions.forEach(transaction => {
                const isCredit = transaction.type === 'credit';
                const statusColors = {
                    'completed': 'bg-success-100 text-success-800 border-success-200',
                    'pending': 'bg-secondary-100 text-secondary-800 border-secondary-200',
                    'failed': 'bg-danger-100 text-danger-800 border-danger-200'
                };
                const statusClass = statusColors[transaction.status] ||
                    'bg-neutral-100 text-neutral-800 border-neutral-200';

                const transactionElement = document.createElement('div');
                transactionElement.className = 'space-y-4';
                transactionElement.innerHTML = `
                    <div class="group bg-white rounded-3xl shadow-lg hover:shadow-xl border border-neutral-100 hover:border-primary-200 transition-all duration-300 overflow-hidden">
                        <div class="flex items-center justify-between p-6">
                            <div class="flex items-center gap-5">
                                <div class="relative">
                                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center ${isCredit ? 'bg-gradient-to-br from-success-100 to-success-200' : 'bg-gradient-to-br from-danger-100 to-danger-200'} group-hover:scale-105 transition-transform duration-300">
                                        ${isCredit ? 
                                            '<svg class="w-8 h-8 text-success-600" fill="currentColor" viewBox="0 0 24 24"><path d="M7 14L12 9L17 14H7Z"/></svg>' :
                                            '<svg class="w-8 h-8 text-danger-600" fill="currentColor" viewBox="0 0 24 24"><path d="M7 10L12 15L17 10H7Z"/></svg>'
                                        }
                                    </div>
                                    <div class="absolute -top-1 -right-1 w-5 h-5 rounded-full ${isCredit ? 'bg-success-500' : 'bg-danger-500'} flex items-center justify-center">
                                        <span class="text-white text-xs font-bold">${isCredit ? '+' : '-'}</span>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <h4 class="font-heading font-semibold text-lg text-neutral-800 group-hover:text-primary-600 transition-colors">
                                        ${transaction.description}
                                    </h4>
                                    <div class="flex items-center gap-4 text-sm text-neutral-500">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                                            </svg>
                                            <span class="font-medium">${new Date(transaction.created_at).toLocaleDateString('en-US', { 
                                                month: 'short', 
                                                day: 'numeric', 
                                                year: 'numeric', 
                                                hour: '2-digit', 
                                                minute: '2-digit' 
                                            })}</span>
                                        </div>
                                        <div class="w-1 h-1 bg-neutral-300 rounded-full"></div>
                                        <span class="capitalize font-medium bg-neutral-100 px-3 py-1 rounded-full">${transaction.type}</span>
                                        ${transaction.reference ? `
                                                                                <div class="w-1 h-1 bg-neutral-300 rounded-full"></div>
                                                                                <span class="font-mono text-xs bg-neutral-100 px-3 py-1 rounded-full">${transaction.reference}</span>
                                                                            ` : ''}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-right space-y-2">
                                <div class="text-2xl font-bold font-heading ${isCredit ? 'text-success-600' : 'text-danger-600'}">
                                    ${isCredit ? '+' : '-'}KES ${parseFloat(transaction.amount).toFixed(2)}
                                </div>
                                <div class="text-sm text-neutral-500 bg-neutral-50 px-3 py-1 rounded-full">
                                    Balance: KES ${parseFloat(transaction.running_balance).toFixed(2)}
                                </div>
                                ${transaction.fee_amount > 0 ? `
                                                                        <div class="text-xs text-neutral-400 bg-neutral-50 px-3 py-1 rounded-full">
                                                                            Fee: KES ${parseFloat(transaction.fee_amount).toFixed(2)}
                                                                        </div>
                                                                    ` : ''}
                                <div class="flex justify-end">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border-2 ${statusClass} shadow-sm">
                                        <span class="w-2 h-2 rounded-full ${transaction.status === 'completed' ? 'bg-success-500' : (transaction.status === 'pending' ? 'bg-secondary-500' : 'bg-danger-500')} mr-2"></span>
                                        ${transaction.status.charAt(0).toUpperCase() + transaction.status.slice(1)}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="hidden group-hover:block border-t border-neutral-100 px-6 py-4 bg-gradient-to-r from-neutral-50 to-white">
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center gap-4">
                                    <span class="text-neutral-500">Transaction ID:</span>
                                    <span class="font-mono text-neutral-700">${transaction.id || 'N/A'}</span>
                                </div>
                                <button class="text-primary-600 hover:text-primary-700 font-medium transition-colors">
                                    View Details
                                </button>
                            </div>
                        </div>
                    </div>
                `;

                container.appendChild(transactionElement);
            });
        }
    </script>
</x-app-layout>
