<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Payout Methods
                </h2>
                <p class="mt-1 text-sm text-gray-600">Manage how you receive donations</p>
            </div>
            <a href="{{ route('payout-methods.create') }}"
                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Payout Method
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($payoutMethods->count() > 0)
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">Your Payout Methods</h2>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @foreach ($payoutMethods as $method)
                            <div class="px-6 py-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-2">
                                            {{-- Icon based on type --}}
                                            <div class="flex-shrink-0">
                                                @if ($method->type === 'mobile_money')
                                                    <div
                                                        class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                                        <svg class="w-4 h-4 text-green-600" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z">
                                                            </path>
                                                        </svg>
                                                    </div>
                                                @else
                                                    <div
                                                        class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                                        <svg class="w-4 h-4 text-blue-600" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                                                            </path>
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="flex-1">
                                                <h3 class="text-lg font-medium text-gray-900">
                                                    {{ $method->formatted_account }}
                                                </h3>
                                                <p class="text-sm text-gray-600">{{ $method->account_name }}</p>
                                                @if ($method->type === 'bank_account' && $method->branch_name)
                                                    <p class="text-xs text-gray-500">Branch:
                                                        {{ $method->branch_name }}
                                                    </p>
                                                @endif
                                            </div>

                                            <div class="flex items-center space-x-2">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $method->status_badge_color }}">
                                                    {{ $method->status_text }}
                                                </span>
                                                @if ($method->is_primary)
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        Primary
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                                            <span>{{ ucfirst(str_replace('_', ' ', $method->type)) }}</span>
                                            <span>Added {{ $method->created_at->format('M d, Y') }}</span>
                                        </div>
                                    </div>

                                    <div class="flex items-center space-x-2 ml-4">
                                        @if (!$method->is_primary)
                                            <form method="POST"
                                                action="{{ route('payout-methods.set-primary', $method->id) }}"
                                                class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                    class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                                    Set Primary
                                                </button>
                                            </form>
                                        @endif

                                        <form method="POST" action="{{ route('payout-methods.destroy', $method->id) }}"
                                            class="inline"
                                            onsubmit="return confirm('Are you sure you want to delete this payout method?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-sm text-red-600 hover:text-red-800 font-medium">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Information Card --}}
                <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Important Information</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    <li>Your primary payout method will be used for all donation payouts</li>
                                    <li>Ensure your account details are accurate to avoid payout delays</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

</x-app-layout>
