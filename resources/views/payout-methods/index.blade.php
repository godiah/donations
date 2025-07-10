<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="space-y-1 py-2">
                <h2 class="text-2xl font-heading font-bold text-neutral-800">
                    Payout Methods
                </h2>
                <p class="text-sm font-medium text-neutral-500">
                    Manage how you receive donations from your campaigns
                </p>
            </div>
            <a href="{{ route('payout-methods.create') }}"
                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-primary-500 to-primary-600 text-white font-medium rounded-xl hover:from-primary-600 hover:to-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Payout Method
            </a>
        </div>
    </x-slot>

    <div class="pt-6 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if ($payoutMethods->count() > 0)
                <!-- Overview Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-gradient-to-br from-primary-50 via-white to-primary-50 rounded-2xl shadow-lg border border-primary-100 p-6">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-neutral-500 mb-1">Total Methods</p>
                                <p class="text-2xl font-heading font-bold text-neutral-800">{{ $payoutMethods->count() }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-success-50 via-white to-success-50 rounded-2xl shadow-lg border border-success-100 p-6">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-success-500 to-success-600 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-neutral-500 mb-1">Active Methods</p>
                                <p class="text-2xl font-heading font-bold text-neutral-800">{{ $payoutMethods->where('is_active', true)->count() }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-secondary-50 via-white to-secondary-50 rounded-2xl shadow-lg border border-secondary-100 p-6">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-secondary-500 to-secondary-600 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-neutral-500 mb-1">Primary Method</p>
                                <p class="text-lg font-heading font-bold text-neutral-800">
                                    {{ $payoutMethods->where('is_primary', true)->first() ? 'Set' : 'None' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payout Methods List -->
                <div class="bg-white rounded-2xl shadow-lg border border-neutral-100 overflow-hidden">
                    <div class="px-8 py-6 bg-gradient-to-r from-neutral-50 to-white border-b border-neutral-100">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-neutral-600 to-neutral-700 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                            </div>
                            <h2 class="text-xl font-heading font-bold text-neutral-800">Your Payout Methods</h2>
                        </div>
                    </div>

                    <div class="p-8">
                        <div class="space-y-4">
                            @foreach ($payoutMethods as $method)
                                <div class="method-card group">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-4 flex-1">
                                            <!-- Enhanced Icon -->
                                            <div class="method-icon">
                                                @if ($method->type === 'mobile_money')
                                                    <div class="w-12 h-12 bg-gradient-to-br from-success-500 to-success-600 rounded-xl flex items-center justify-center">
                                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                        </svg>
                                                    </div>
                                                @else
                                                    <div class="w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center">
                                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Method Details -->
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center space-x-3 mb-2">
                                                    <h3 class="text-lg font-heading font-bold text-neutral-800 truncate">
                                                        {{ $method->formatted_account }}
                                                    </h3>
                                                    <div class="flex items-center space-x-2">
                                                        <span class="method-status {{ $method->status_badge_color }}">
                                                            <span class="status-dot"></span>
                                                            {{ $method->status_text }}
                                                        </span>
                                                        @if ($method->is_primary)
                                                            <span class="primary-badge">
                                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                                                                </svg>
                                                                Primary
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                
                                                <div class="space-y-1">
                                                    <p class="text-sm font-medium text-neutral-600">{{ $method->account_name }}</p>
                                                    <div class="flex items-center space-x-4 text-xs text-neutral-500">
                                                        <span class="flex items-center space-x-1">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                                            </svg>
                                                            <span>{{ ucfirst(str_replace('_', ' ', $method->type)) }}</span>
                                                        </span>
                                                        <span class="flex items-center space-x-1">                                                            
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                                              </svg>                                                              
                                                            <span>Added {{ $method->created_at->format('M d, Y') }}</span>
                                                        </span>
                                                        @if ($method->type === 'bank_account' && $method->branch_name)
                                                            <span class="flex items-center space-x-1">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                                </svg>
                                                                <span>{{ $method->branch_name }}</span>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Action Buttons -->
                                        <div class="flex items-center space-x-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                            @if (!$method->is_primary)
                                                <form method="POST" action="{{ route('payout-methods.set-primary', $method->id) }}" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="action-btn primary-btn">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                                                        </svg>
                                                        Set Primary
                                                    </button>
                                                </form>
                                            @endif

                                            <form method="POST" action="{{ route('payout-methods.destroy', $method->id) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this payout method?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="action-btn danger-btn">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Information Card -->
                <div class="mt-8 bg-gradient-to-r from-primary-50 to-blue-50 border border-primary-200 rounded-2xl p-6">
                    <div class="flex items-start space-x-4">
                        <div class="w-10 h-10 bg-primary-500 rounded-xl flex items-center justify-center mt-0.5">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-heading font-bold text-neutral-800 mb-3">Important Information</h3>
                            <div class="space-y-2 text-sm text-neutral-700">
                                <div class="flex items-start space-x-2">
                                    <svg class="w-4 h-4 text-primary-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                    <span>Your <strong>primary payout method</strong> will be used for all donation payouts automatically</span>
                                </div>
                                <div class="flex items-start space-x-2">
                                    <svg class="w-4 h-4 text-primary-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                    <span>Double-check your <strong>account details</strong> to avoid payout delays or failures</span>
                                </div>
                                <div class="flex items-start space-x-2">
                                    <svg class="w-4 h-4 text-primary-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                    <span>You can have multiple payout methods but only <strong>one can be primary</strong> at a time</span>
                                </div>
                                <div class="flex items-start space-x-2">
                                    <svg class="w-4 h-4 text-primary-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                    <span>Payout processing times vary by method: <strong>Mobile Money (instant)</strong>, <strong>Bank Transfer (1-3 days)</strong></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-10">
                    <div class="max-w-md mx-auto">
                        <div class="w-20 h-20 bg-gradient-to-br from-neutral-100 to-neutral-200 rounded-2xl flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-heading font-bold text-neutral-800 mb-3">No Payout Methods Yet</h3>
                        <p class="text-neutral-600 mb-8 leading-relaxed">
                            You need to add at least one payout method to receive donations from your campaigns. 
                            Choose between mobile money for instant payments or bank transfer for traditional banking.
                        </p>
                        <a href="{{ route('payout-methods.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-primary-500 to-primary-600 text-white font-medium rounded-xl hover:from-primary-600 hover:to-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add Your First Payout Method
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Enhanced Styles -->
    <style>      

        /* Fixed navbar compensation */
        .pt-6 {
            padding-top: 1.5rem;
        }
    </style>
</x-app-layout>
