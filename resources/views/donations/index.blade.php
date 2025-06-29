<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    My Donations
                </h2>
                <p class="mt-1 text-sm text-gray-600">Your approved donation setups</p>
            </div>
            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                ← Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <!-- Donations List -->
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if ($donations->count() > 0)
                        <div class="space-y-6">
                            @foreach ($donations as $donation)
                                <div
                                    class="p-6 transition-shadow bg-green-50 border border-green-200 rounded-lg hover:shadow-md">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center mb-3 space-x-3">
                                                <span class="text-lg font-semibold text-gray-900">
                                                    #{{ $donation->application_number }}
                                                </span>
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                                    ✓ {{ $donation->status->getDisplayName() }}
                                                </span>
                                                @if ($donation->payoutMandate)
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                        Payout Setup
                                                    </span>
                                                @endif
                                            </div>

                                            <h3 class="mb-2 text-xl font-medium text-gray-900">
                                                {{ $donation->applicant->contribution_name }}
                                            </h3>

                                            <div class="grid grid-cols-1 gap-4 mb-4 md:grid-cols-4">
                                                <div>
                                                    <span class="text-sm font-medium text-gray-500">Donation
                                                        Target</span>
                                                    <p class="text-lg font-semibold text-green-600">KSh
                                                        {{ number_format($donation->applicant->target_amount, 2) }}</p>
                                                </div>
                                                <div>
                                                    <span class="text-sm font-medium text-gray-500">Target Date</span>
                                                    <p class="text-sm text-gray-900">
                                                        {{ $donation->applicant->target_date->format('M j, Y') }}</p>
                                                </div>
                                                <div>
                                                    <span class="text-sm font-medium text-gray-500">Approved On</span>
                                                    <p class="text-sm text-gray-900">
                                                        {{ $donation->reviewed_at->format('M j, Y') }}</p>
                                                </div>
                                                <div>
                                                    <span class="text-sm font-medium text-gray-500">Type</span>
                                                    <p class="text-sm text-gray-900">
                                                        {{ $donation->applicant_type === \App\Models\Individual::class ? 'Individual' : 'Company' }}
                                                    </p>
                                                </div>
                                            </div>

                                            @if ($donation->applicant->contribution_description)
                                                <div class="pt-4 mt-4 border-t border-green-200">
                                                    <p class="text-sm text-gray-700">
                                                        {{ $donation->applicant->contribution_description }}
                                                    </p>
                                                </div>
                                            @endif

                                            @if ($donation->payoutMandate)
                                                <div class="pt-4 mt-4 border-t border-green-200">
                                                    <div class="flex items-center space-x-2">
                                                        <svg class="w-4 h-4 text-green-600" fill="currentColor"
                                                            viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd"
                                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                clip-rule="evenodd"></path>
                                                        </svg>
                                                        <span class="text-sm font-medium text-green-700">Payout mandate
                                                            configured</span>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="flex flex-col ml-6 space-y-2">
                                            @if ($donation->applicant_type === \App\Models\Individual::class)
                                                <a href="{{ route('individual.applications.show', $donation->application_number) }}"
                                                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-green-700 transition-colors">
                                                    View Details
                                                </a>
                                            @else
                                                <a href="{{ route('company.applications.show', $donation->application_number) }}"
                                                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-green-700 transition-colors">
                                                    View Details
                                                </a>
                                            @endif

                                            {{-- Manage Donations Button --}}
                                            @if ($donation->applicant_type === \App\Models\Individual::class)
                                                <a href="{{ route('donations.show', $donation->application_number) }}"
                                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 transition-colors">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                                        </path>
                                                    </svg>
                                                    Manage Donations
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-8">
                            {{ $donations->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="py-12 text-center">
                            <div class="mb-4 text-4xl text-gray-400">💝</div>
                            <h3 class="mb-2 text-lg font-medium text-gray-900">No donations yet</h3>
                            <p class="text-gray-600">
                                You don't have any approved donation setups yet. Once your applications are approved,
                                they'll appear here.
                            </p>
                            <div class="mt-6">
                                <a href="{{ route('dashboard') }}"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700 transition-colors">
                                    Back to Dashboard
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Summary Cards -->
            @if ($donations->count() > 0)
                <div class="grid grid-cols-1 gap-6 mt-8 md:grid-cols-3">
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="p-3 bg-green-100 rounded-full">
                                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                            </path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Total Donation Value</dt>
                                        <dd class="text-lg font-medium text-gray-900">
                                            KSh
                                            {{ number_format($donations->sum(function ($donation) {return $donation->applicant->target_amount;}),2) }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="p-3 bg-blue-100 rounded-full">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                            </path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Active Donations</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ $donations->total() }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="p-3 bg-purple-100 rounded-full">
                                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                                            </path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">With Payout Setup</dt>
                                        <dd class="text-lg font-medium text-gray-900">
                                            {{ $donations->filter(function ($donation) {return $donation->payoutMandate;})->count() }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
