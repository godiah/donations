<x-app-layout>

    <div class="pt-16 bg-gradient-to-br from-neutral-50 to-neutral-100">
        <div class="relative py-8 sm:py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Header -->
                <div class="bg-gradient-to-r from-danger-600 to-danger-700 rounded-2xl p-6 text-white shadow-xl">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                                </svg>

                            </div>
                            <div>
                                <h2 class="text-2xl font-heading font-bold">Withdrawal Management</h2>
                                <p class="text-danger-100 mt-1">Review and process withdrawal requests</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="bg-white/20 backdrop-blur-sm rounded-lg px-4 py-2">
                                <div class="text-sm text-danger-200">Total Requests</div>
                                <div class="text-xl font-bold text-white">{{ $withdrawals->total() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Statistics Cards -->
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <!-- Pending Approval -->
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
                                    <h3 class="font-semibold text-white">Pending Approval</h3>
                                    <p class="text-secondary-100 text-sm">Awaiting review</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="text-3xl font-bold text-secondary-600">
                                {{ $withdrawals->where('status', 'pending')->count() }}
                            </div>
                            <p class="text-sm text-neutral-600 mt-1">Requests pending</p>
                        </div>
                    </div>

                    <!-- Processing -->
                    <div class="bg-white rounded-2xl shadow-sm border border-neutral-200 overflow-hidden">
                        <div class="bg-gradient-to-r from-primary-500 to-primary-600 p-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-white">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                    </svg>

                                </div>
                                <div>
                                    <h3 class="font-semibold text-white">Processing</h3>
                                    <p class="text-primary-100 text-sm">In progress</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="text-3xl font-bold text-primary-600">
                                {{ $withdrawals->where('status', 'processing')->count() }}
                            </div>
                            <p class="text-sm text-neutral-600 mt-1">Being processed</p>
                        </div>
                    </div>

                    <!-- Completed Today -->
                    <div class="bg-white rounded-2xl shadow-sm border border-neutral-200 overflow-hidden">
                        <div class="bg-gradient-to-r from-success-500 to-success-600 p-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 16.17L4.83 12L3.41 13.41L9 19L21 7L19.59 5.59L9 16.17Z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-white">Completed Today</h3>
                                    <p class="text-success-100 text-sm">Successfully processed</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="text-3xl font-bold text-success-600">
                                {{ $withdrawals->where('status', 'completed')->where('processed_at', '>=', today())->count() }}
                            </div>
                            <p class="text-sm text-neutral-600 mt-1">Today's completions</p>
                        </div>
                    </div>

                    <!-- Failed/Cancelled -->
                    <div class="bg-white rounded-2xl shadow-sm border border-neutral-200 overflow-hidden">
                        <div class="bg-gradient-to-r from-danger-500 to-danger-600 p-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M13,14H11V10H13M13,18H11V16H13M1,21H23L12,2L1,21Z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-white">Failed/Cancelled</h3>
                                    <p class="text-danger-100 text-sm">Unsuccessful requests</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="text-3xl font-bold text-danger-600">
                                {{ $withdrawals->whereIn('status', ['failed', 'cancelled'])->count() }}
                            </div>
                            <p class="text-sm text-neutral-600 mt-1">Failed requests</p>
                        </div>
                    </div>
                </div>

                <!-- Filters Section -->
                <div class="bg-white rounded-2xl shadow-sm border border-neutral-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-neutral-800 to-neutral-700 px-6 py-4">
                        <h3 class="text-lg font-heading font-semibold text-white flex items-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M14 12L10 8V11H2V13H10V16L14 12ZM20 3H4C2.9 3 2 3.9 2 5V8H4V5H20V19H4V16H2V19C2 20.1 2.9 21 4 21H20C21.1 21 22 20.1 22 19V5C22 3.9 21.1 3 20 3Z" />
                            </svg>
                            Filter Withdrawals
                        </h3>
                        <p class="text-neutral-300 text-sm mt-1">Filter by withdrawal status</p>
                    </div>

                    <div style="padding: 1.5rem;">
                        <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.75rem;">
                            <span style="font-size: 0.875rem; font-weight: 500; color: #4b5563;">Status:</span>

                            <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                                <a href="{{ route('admin.withdrawals.index') }}"
                                    style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; border-radius: 0.75rem; font-size: 0.875rem; font-weight: 600; transition: all 200ms ease; transform: translate(1.02); {{ !request('status') ? 'background: linear-gradient(to right, #3b82f6, #2563eb); color: #ffffff; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);' : 'background-color: #f3f4f6; color: #4b5563; border: 1px solid #e5e7eb; transform: translate(1.02);' }}">

                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" style="width: 1rem; height: 1rem;">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 9V4.5M9 9H4.5M9 9 3.75 3.75M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 9h4.5M15 9V4.5M15 9l5.25-5.25M15 15h4.5M15 15v4.5m0-4.5 5.25 5.25" />
                                    </svg>
                                    All Withdrawals
                                    @if (!request('status'))
                                        <span
                                            style="margin-left: 0.5rem; background-color: rgba(255, 255, 255, 0.3); font-size: 0.75rem; padding: 0.125rem 0.5rem; border-radius: 9999px;">{{ $withdrawals->total() }}</span>
                                    @endif
                                </a>

                                <a href="{{ route('admin.withdrawals.index', ['status' => 'pending']) }}"
                                    style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; border-radius: 0.75rem; font-size: 0.875rem; font-weight: 600; transition: all 200ms ease; transform: translate(1.02); {{ request('status') === 'pending' ? 'background: linear-gradient(to right, #8b5cf6, #7c3aed); color: #ffffff; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);' : 'background-color: #f3f4f6; color: #4b5563; border: 1px solid #e5e7eb; transform: translate(1.02);' }}">
                                    <svg style="width: 1rem; height: 1rem;" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM13 17H11V15H13V17ZM13 13H11V7H13V13Z" />
                                    </svg>
                                    Pending
                                </a>

                                <a href="{{ route('admin.withdrawals.index', ['status' => 'processing']) }}"
                                    style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; border-radius: 0.75rem; font-size: 0.875rem; font-weight: 600; transition: all 200ms ease; transform: translate(1.02); {{ request('status') === 'processing' ? 'background: linear-gradient(to right, #3b82f6, #2563eb); color: #ffffff; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);' : 'background-color: #f3f4f6; color: #4b5563; border: 1px solid #e5e7eb; transform: translate(1.02);' }}">

                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" style="width: 1rem; height: 1rem;">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                    </svg>
                                    Processing
                                </a>

                                <a href="{{ route('admin.withdrawals.index', ['status' => 'completed']) }}"
                                    style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; border-radius: 0.75rem; font-size: 0.875rem; font-weight: 600; transition: all 200ms ease; transform: translate(1.02); {{ request('status') === 'completed' ? 'background: linear-gradient(to right, #10b981, #059669); color: #ffffff; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);' : 'background-color: #f3f4f6; color: #4b5563; border: 1px solid #e5e7eb; transform: translate(1.02);' }}">
                                    <svg style="width: 1rem; height: 1rem;" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 16.17L4.83 12L3.41 13.41L9 19L21 7L19.59 5.59L9 16.17Z" />
                                    </svg>
                                    Completed
                                </a>

                                <a href="{{ route('admin.withdrawals.index', ['status' => 'failed']) }}"
                                    style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; border-radius: 0.75rem; font-size: 0.875rem; font-weight: 600; transition: all 200ms ease; transform: translate(1.02); {{ request('status') === 'failed' ? 'background: linear-gradient(to right, #ef4444, #dc2626); color: #ffffff; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);' : 'background-color: #f3f4f6; color: #4b5563; border: 1px solid #e5e7eb; transform: translate(1.02);' }}">
                                    <svg style="width: 1rem; height: 1rem;" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M13,14H11V10H13M13,18H11V16H13M1,21H23L12,2L1,21Z" />
                                    </svg>
                                    Failed
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Withdrawals Table -->
                <div class="bg-white rounded-2xl shadow-sm border border-neutral-200 overflow-hidden">
                    @if ($withdrawals->count() > 0)
                        <div class="bg-gradient-to-r from-primary-600 to-primary-700 px-6 py-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-heading font-semibold text-white">
                                        Withdrawal Requests
                                    </h3>
                                    <p class="text-primary-100 text-sm mt-1">
                                        Showing {{ $withdrawals->count() }} of {{ $withdrawals->total() }} requests
                                    </p>
                                </div>
                                <div class="bg-white/20 backdrop-blur-sm rounded-lg px-4 py-2">
                                    <span class="text-white font-semibold">{{ $withdrawals->total() }} Total</span>
                                </div>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-neutral-50 border-b border-neutral-200">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-neutral-800">
                                            Reference
                                        </th>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-neutral-800">User
                                        </th>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-neutral-800">Amount
                                        </th>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-neutral-800">Method
                                        </th>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-neutral-800">Status
                                        </th>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-neutral-800">
                                            Requested
                                        </th>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-neutral-800">Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-neutral-200">
                                    @foreach ($withdrawals as $withdrawal)
                                        <tr class="hover:bg-neutral-50 transition-colors">
                                            <td class="px-6 py-4">
                                                <a href="{{ route('admin.withdrawals.show', $withdrawal) }}"
                                                    class="text-primary-600 hover:text-primary-800 font-semibold transition-colors">
                                                    {{ $withdrawal->request_reference }}
                                                </a>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-600 rounded-lg flex items-center justify-center">
                                                        <span class="text-white font-semibold text-sm">
                                                            {{ substr($withdrawal->user->name, 0, 2) }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <div class="font-semibold text-neutral-800">
                                                            {{ $withdrawal->user->name }}</div>
                                                        <div class="text-sm text-neutral-500">
                                                            {{ $withdrawal->user->email }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="space-y-1">
                                                    <div class="font-semibold text-neutral-800">KES
                                                        {{ number_format($withdrawal->amount, 2) }}</div>
                                                    <div class="text-sm text-neutral-500">Fee: KES
                                                        {{ number_format($withdrawal->fee_amount, 2) }}</div>
                                                    <div class="font-bold text-success-600">Net: KES
                                                        {{ number_format($withdrawal->net_amount, 2) }}</div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                @if ($withdrawal->withdrawal_method === 'mpesa')
                                                    <span
                                                        class="inline-flex items-center gap-2 px-3 py-1 rounded-lg text-sm font-semibold bg-success-100 text-success-800 border border-success-200">
                                                        <div
                                                            class="w-4 h-4 bg-success-500 rounded flex items-center justify-center">
                                                            <span class="text-white text-xs font-bold">M</span>
                                                        </div>
                                                        M-Pesa
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center gap-2 px-3 py-1 rounded-lg text-sm font-semibold bg-primary-100 text-primary-800 border border-primary-200">
                                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M5 6H23V18H5V6ZM7 8V16H21V8H7ZM1 4H3V20H1V4Z" />
                                                        </svg>
                                                        Bank Transfer
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4">
                                                @php
                                                    $statusConfig = [
                                                        'pending' => [
                                                            'bg' => 'bg-secondary-100',
                                                            'text' => 'text-secondary-800',
                                                            'border' => 'border-secondary-200',
                                                        ],
                                                        'processing' => [
                                                            'bg' => 'bg-primary-100',
                                                            'text' => 'text-primary-800',
                                                            'border' => 'border-primary-200',
                                                        ],
                                                        'completed' => [
                                                            'bg' => 'bg-success-100',
                                                            'text' => 'text-success-800',
                                                            'border' => 'border-success-200',
                                                        ],
                                                        'failed' => [
                                                            'bg' => 'bg-danger-100',
                                                            'text' => 'text-danger-800',
                                                            'border' => 'border-danger-200',
                                                        ],
                                                        'cancelled' => [
                                                            'bg' => 'bg-neutral-100',
                                                            'text' => 'text-neutral-800',
                                                            'border' => 'border-neutral-200',
                                                        ],
                                                    ];
                                                    $config =
                                                        $statusConfig[$withdrawal->status] ?? $statusConfig['pending'];
                                                @endphp
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-semibold border {{ $config['bg'] }} {{ $config['text'] }} {{ $config['border'] }}">
                                                    {{ ucfirst($withdrawal->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="space-y-1">
                                                    <div class="font-semibold text-neutral-800">
                                                        {{ $withdrawal->created_at->format('M d, Y') }}</div>
                                                    <div class="text-sm text-neutral-500">
                                                        {{ $withdrawal->created_at->format('H:i') }}</div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-2">
                                                    <a href="{{ route('admin.withdrawals.show', $withdrawal) }}"
                                                        class="bg-neutral-100 hover:bg-neutral-200 text-neutral-700 p-2 rounded-lg transition-colors">
                                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                            <path
                                                                d="M12 4.5C7 4.5 2.73 7.61 1 12C2.73 16.39 7 19.5 12 19.5C17 19.5 21.27 16.39 23 12C21.27 7.61 17 4.5 12 4.5ZM12 17C9.24 17 7 14.76 7 12C7 9.24 9.24 7 12 7C14.76 7 17 9.24 17 12C17 14.76 14.76 17 12 17ZM12 9C10.34 9 9 10.34 9 12C9 13.66 10.34 15 12 15C13.66 15 15 13.66 15 12C15 10.34 13.66 9 12 9Z" />
                                                        </svg>
                                                    </a>
                                                    @if ($withdrawal->status === 'pending')
                                                        <button onclick="approveWithdrawal({{ $withdrawal->id }})"
                                                            class="bg-success-100 hover:bg-success-200 text-success-700 p-2 rounded-lg transition-colors">
                                                            <svg class="w-4 h-4" fill="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path
                                                                    d="M9 16.17L4.83 12L3.41 13.41L9 19L21 7L19.59 5.59L9 16.17Z" />
                                                            </svg>
                                                        </button>
                                                        <button onclick="rejectWithdrawal({{ $withdrawal->id }})"
                                                            class="bg-danger-100 hover:bg-danger-200 text-danger-700 p-2 rounded-lg transition-colors">
                                                            <svg class="w-4 h-4" fill="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path
                                                                    d="M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12L19 6.41Z" />
                                                            </svg>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if ($withdrawals->hasPages())
                            <div class="border-t border-neutral-200 px-6 py-4">
                                <div class="flex items-center justify-between">
                                    <div class="text-sm text-neutral-600">
                                        Showing {{ $withdrawals->firstItem() }} to {{ $withdrawals->lastItem() }} of
                                        {{ $withdrawals->total() }} results
                                    </div>
                                    <div class="pagination-wrapper">
                                        {{ $withdrawals->appends(request()->query())->links() }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
                        <!-- Empty State -->
                        <div class="text-center py-16">
                            <div
                                class="w-24 h-24 bg-gradient-to-br from-neutral-100 to-neutral-200 rounded-2xl flex items-center justify-center mx-auto mb-6">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-neutral-400">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                </svg>

                            </div>
                            <h3 class="text-xl font-heading font-bold text-neutral-800 mb-2">No Withdrawal Requests
                            </h3>
                            <p class="text-neutral-600 mb-6 max-w-md mx-auto">
                                @if (request('status'))
                                    No withdrawal requests with "{{ ucfirst(request('status')) }}" status found.
                                @else
                                    No withdrawal requests have been submitted yet.
                                @endif
                            </p>
                            {{-- <a href="{{ route('admin.withdrawals.index') }}"
                                class="bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-200 transform hover:scale-[1.02] flex items-center gap-2 mx-auto w-fit">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M17.65 6.35C16.2 4.9 14.21 4 12 4C7.58 4 4 7.58 4 12C4 16.42 7.58 20 12 20C16.42 20 20 16.42 20 12H18C18 15.31 15.31 18 12 18C8.69 18 6 15.31 6 12C6 8.69 8.69 6 12 6C13.66 6 15.14 6.69 16.22 7.78L13 11H20V4L17.65 6.35Z" />
                                </svg>
                                Clear Filters
                            </a> --}}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Approval Modal -->
    <div id="approvalModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl shadow-xl max-w-md w-full">
                <div class="bg-gradient-to-r from-success-600 to-success-700 p-6 rounded-t-2xl">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-heading font-semibold text-white flex items-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9 16.17L4.83 12L3.41 13.41L9 19L21 7L19.59 5.59L9 16.17Z" />
                            </svg>
                            Approve Withdrawal
                        </h3>
                        <button onclick="closeModal('approvalModal')"
                            class="text-white hover:text-success-200 transition-colors">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12L19 6.41Z" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-neutral-700 mb-4">Are you sure you want to approve this withdrawal request?</p>
                    <div id="withdrawalDetails" class="bg-neutral-50 rounded-lg p-4 mb-6"></div>
                    <form id="approvalForm" method="POST" class="flex gap-3">
                        @csrf
                        @method('PATCH')
                        <button type="button" onclick="closeModal('approvalModal')"
                            class="flex-1 bg-neutral-100 hover:bg-neutral-200 text-neutral-700 px-4 py-3 rounded-xl font-semibold transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                            class="flex-1 bg-gradient-to-r from-success-500 to-success-600 hover:from-success-600 hover:to-success-700 text-white px-4 py-3 rounded-xl font-semibold transition-all duration-200 transform hover:scale-[1.02] flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9 16.17L4.83 12L3.41 13.41L9 19L21 7L19.59 5.59L9 16.17Z" />
                            </svg>
                            Approve
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Rejection Modal -->
    <div id="rejectionModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl shadow-xl max-w-md w-full">
                <div class="bg-gradient-to-r from-danger-600 to-danger-700 p-6 rounded-t-2xl">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-heading font-semibold text-white flex items-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12L19 6.41Z" />
                            </svg>
                            Reject Withdrawal
                        </h3>
                        <button onclick="closeModal('rejectionModal')"
                            class="text-white hover:text-danger-200 transition-colors">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12L19 6.41Z" />
                            </svg>
                        </button>
                    </div>
                </div>
                <form id="rejectionForm" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="p-6">
                        <div class="space-y-3 mb-6">
                            <label for="rejection_reason" class="block text-sm font-semibold text-neutral-800">
                                Reason for Rejection <span class="text-danger-500">*</span>
                            </label>
                            <textarea id="rejection_reason" name="rejection_reason" rows="4" required
                                placeholder="Please provide a detailed reason for rejecting this withdrawal..."
                                class="w-full px-4 py-3 border-2 border-neutral-200 rounded-lg focus:border-danger-500 focus:ring-0 transition-colors resize-none"></textarea>
                        </div>
                        <div class="flex gap-3">
                            <button type="button" onclick="closeModal('rejectionModal')"
                                class="flex-1 bg-neutral-100 hover:bg-neutral-200 text-neutral-700 px-4 py-3 rounded-xl font-semibold transition-colors">
                                Cancel
                            </button>
                            <button type="submit"
                                class="flex-1 bg-gradient-to-r from-danger-500 to-danger-600 hover:from-danger-600 hover:to-danger-700 text-white px-4 py-3 rounded-xl font-semibold transition-all duration-200 transform hover:scale-[1.02] flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12L19 6.41Z" />
                                </svg>
                                Reject
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function approveWithdrawal(withdrawalId) {
            const form = document.getElementById('approvalForm');
            form.action = `/admin/withdrawals/${withdrawalId}/approve`;
            document.getElementById('approvalModal').classList.remove('hidden');
        }

        function rejectWithdrawal(withdrawalId) {
            const form = document.getElementById('rejectionForm');
            form.action = `/admin/withdrawals/${withdrawalId}/reject`;
            document.getElementById('rejectionModal').classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        // Close modals when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('fixed')) {
                closeModal(e.target.id);
            }
        });

        // Close modals with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal('approvalModal');
                closeModal('rejectionModal');
            }
        });
    </script>
</x-app-layout>

<style>
    /* Filter Badge Styles */
    .filter-badge {
        @apply inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 transform hover:scale-[1.02];
    }

    .filter-badge-active {
        @apply bg-gradient-to-r from-primary-500 to-primary-600 text-white shadow-md;
    }

    .filter-badge-active-secondary {
        @apply bg-gradient-to-r from-secondary-500 to-secondary-600 text-white shadow-md;
    }

    .filter-badge-active-primary {
        @apply bg-gradient-to-r from-primary-500 to-primary-600 text-white shadow-md;
    }

    .filter-badge-active-success {
        @apply bg-gradient-to-r from-success-500 to-success-600 text-white shadow-md;
    }

    .filter-badge-active-danger {
        @apply bg-gradient-to-r from-danger-500 to-danger-600 text-white shadow-md;
    }

    .filter-badge-inactive {
        @apply bg-neutral-100 text-neutral-600 hover:bg-neutral-200 hover:text-neutral-800 border border-neutral-200 hover:border-neutral-300;
    }

    /* Pagination Styles */
    .pagination-wrapper .pagination {
        @apply flex items-center gap-1;
    }

    .pagination-wrapper .page-link {
        @apply px-3 py-2 text-sm font-medium text-neutral-600 bg-white border border-neutral-200 rounded-lg hover:bg-neutral-50 hover:text-primary-600 transition-colors;
    }

    .pagination-wrapper .page-item.active .page-link {
        @apply bg-gradient-to-r from-primary-500 to-primary-600 text-white border-primary-500;
    }

    .pagination-wrapper .page-item.disabled .page-link {
        @apply text-neutral-400 cursor-not-allowed hover:bg-white hover:text-neutral-400;
    }
</style>
