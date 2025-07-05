<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Donation Management
                </h2>
                <p class="text-sm font-medium text-gray-900">{{ $application->applicant->contribution_name }}</p>
            </div>
            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                ← Back to Dashboard
            </a>
        </div>
    </x-slot>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-8">
                {{-- Progress Section --}}
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Collection Progress</h2>
                    <div class="space-y-4">
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Amount Collected</span>
                            <span>{{ number_format($targetAmount > 0 ? $progressPercentage : 0, 1) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full transition-all duration-300"
                                style="width: {{ min($progressPercentage, 100) }}%"></div>
                        </div>
                        <div class="flex justify-between text-lg font-semibold">
                            <span class="text-green-600">KES {{ number_format($totalCollected) }}</span>
                            <span class="text-gray-900">KES {{ number_format($targetAmount) }}</span>
                        </div>
                    </div>
                </div>

                {{-- Fee Structure Section --}}
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Fee Structure</h2>
                    <div class="space-y-3">
                        @php
                            $feeStructure = $application->fee_structure ?? [
                                'type' => 'percentage',
                                'value' => '5%',
                                'description' => '5% of total contribution',
                            ];
                        @endphp

                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ ucfirst($feeStructure['type']) }} Fee</p>
                                <p class="text-sm text-gray-600">{{ $feeStructure['description'] }}</p>
                            </div>
                            {{-- <div class="ml-auto">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    {{ $feeStructure['value'] }}
                                </span>
                            </div> --}}
                        </div>
                    </div>
                </div>

                {{-- Donation Links Section --}}
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-900">Donation Link</h2>
                    </div>
                    <div class="p-6">
                        @if ($donationLinks->count() > 0)
                            <div class="space-y-4">
                                @foreach ($donationLinks as $link)
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1">
                                                <div class="flex items-center space-x-3 mb-2">
                                                    <code class="text-sm bg-gray-100 px-2 py-1 rounded">
                                                        {{ Str::limit($link->code, 12) }}...
                                                    </code>
                                                    <button onclick="copyToClipboard('{{ $link->full_url }}')"
                                                        class="text-gray-400 hover:text-gray-600" title="Copy full URL">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                </div>
                                                <div class="flex items-center space-x-4 text-sm text-gray-600">
                                                    <span>{{ $link->access_count }} visits</span>
                                                    <span>Created {{ $link->created_at->format('M d, Y') }}</span>
                                                    @if ($link->expires_at)
                                                        <span>Expires {{ $link->expires_at->format('M d, Y') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-3">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                    {{ $link->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ ucfirst($link->status) }}
                                                </span>
                                                @if ($link->isExpired())
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        Expired
                                                    </span>
                                                @endif
                                                {{-- <form method="POST" action="{{ route('donations.toggle-link', [$application->application_number, $link->id]) }}" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" 
                                                        class="text-sm text-blue-600 hover:text-blue-800">
                                                        {{ $link->status === 'active' ? 'Deactivate' : 'Activate' }}
                                                    </button>
                                                </form> --}}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1">
                                    </path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No donation links</h3>
                                <p class="mt-1 text-sm text-gray-500">Get started by creating your first donation link.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-8">
                {{-- Payout Methods Section --}}
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-gray-900">Payout Methods</h2>
                            @if ($canViewPayoutMethods)
                                <a href="{{ route('payout-methods.create') }}"
                                    class="text-sm text-blue-600 hover:text-blue-800">
                                    Add Method
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="p-6">
                        @if ($canViewPayoutMethods)
                            {{-- Authorized users can view and manage payout methods --}}
                            @if ($payoutMethods->count() > 0)
                                <div class="space-y-4">
                                    @foreach ($payoutMethods as $method)
                                        <div class="border border-gray-200 rounded-lg p-4">
                                            <div class="flex items-start justify-between">
                                                <div class="flex-1">
                                                    <div class="flex items-center space-x-2 mb-2">
                                                        <span class="font-medium text-gray-900">
                                                            {{ $method->formatted_account }}
                                                        </span>
                                                        <span
                                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $method->status_badge_color }}">
                                                            {{ $method->status_text }}
                                                        </span>
                                                        @if ($method->is_primary)
                                                            <span
                                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                                Primary
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <p class="text-sm text-gray-600">{{ $method->account_name }}</p>
                                                    @if ($method->type === 'bank_account')
                                                        <p class="text-xs text-gray-500">Bank Account</p>
                                                    @else
                                                        <p class="text-xs text-gray-500">Mobile Money</p>
                                                    @endif
                                                </div>
                                                @if (!$method->is_primary)
                                                    <form method="POST"
                                                        action="{{ route('payout-methods.set-primary', $method->id) }}"
                                                        class="inline-flex items-center ml-1 mt-3 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit"
                                                            class="text-xs text-blue-600 hover:text-blue-800">
                                                            Set Primary
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <a href="{{ route('payout-methods.index') }}"
                                        class="text-sm text-blue-600 hover:text-blue-800">
                                        Manage all methods →
                                    </a>
                                </div>
                            @else
                                <div class="text-center py-6">
                                    <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                                        </path>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">No payout methods</h3>
                                    <p class="mt-1 text-sm text-gray-500">Add a payout method to receive donations.</p>
                                    <div class="mt-4">
                                        <a href="{{ route('payout-methods.create') }}"
                                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 transition-colors">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4"></path>
                                            </svg>
                                            Set Up Payout Method
                                        </a>
                                    </div>
                                </div>
                            @endif
                        @else
                            {{-- Unauthorized users (payout_maker) see this message --}}
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-red-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.664-.833-2.464 0L5.268 16.5c-.77.833.192 2.5 1.732 2.5z">
                                    </path>
                                </svg>
                                <h3 class="mt-4 text-lg font-medium text-gray-900">Access Not Authorized</h3>
                                <p class="mt-2 text-sm text-gray-600">
                                    You are not authorized to view or manage payout methods for this application.
                                </p>
                                @if ($checkerInfo)
                                    <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                                        <p class="text-sm text-gray-700">
                                            <strong>Payout Checker:</strong> {{ $checkerInfo['name'] }}
                                            (<a href="mailto:{{ $checkerInfo['email'] }}"
                                                class="text-blue-600 hover:text-blue-800">{{ $checkerInfo['email'] }}</a>)
                                            will set up payout methods and approve payments for this application.
                                        </p>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Quick Stats --}}
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Stats</h2>
                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Active Links</span>
                            <span class="text-sm font-medium text-gray-900">
                                {{ $donationLinks->where('status', 'active')->count() }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Total Visits</span>
                            <span class="text-sm font-medium text-gray-900">
                                {{ $donationLinks->sum('access_count') }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Payout Methods</span>
                            <span class="text-sm font-medium text-gray-900">
                                {{ $payoutMethods->count() }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Copy to Clipboard Script --}}
    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                // Show success message
                const toast = document.createElement('div');
                toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-md shadow-lg z-50';
                toast.textContent = 'Link copied to clipboard!';
                document.body.appendChild(toast);

                setTimeout(() => {
                    document.body.removeChild(toast);
                }, 3000);
            }).catch(function(err) {
                console.error('Could not copy text: ', err);
            });
        }
    </script>
</x-app-layout>
