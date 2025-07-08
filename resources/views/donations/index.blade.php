<x-app-layout>
    <div class="pt-16">
        <div class="relative py-8 sm:py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Header Section -->
                <div class="mb-8">
                    <div class="bg-gradient-to-r from-success-600 to-success-700 rounded-2xl p-6 text-white shadow-xl">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                                    </svg>
                                </div>
                                <div class="space-y-2">
                                    <h2 class="text-2xl font-heading font-bold">My Donations</h2>
                                    <p
                                        class="text-xs font-medium text-neutral-500 bg-neutral-100 px-3 py-1 rounded-full inline-block">
                                        Your approved donation setups
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($donations->count() > 0)
                    <!-- Summary Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <!-- Total Donation Value -->
                        <div
                            class="bg-gradient-to-br from-success-50 via-white to-success-50 rounded-2xl shadow-lg border border-success-100 overflow-hidden">
                            <div class="p-6">
                                <div class="flex items-center space-x-4">
                                    <div
                                        class="w-12 h-12 bg-gradient-to-br from-success-500 to-success-600 rounded-xl flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-white">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-neutral-500 mb-1">Total Donation Value</p>
                                        <p class="text-2xl font-heading font-bold text-neutral-800">
                                            KSh
                                            {{ number_format($donations->sum(function ($donation) {return $donation->applicant->target_amount;}),2) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Active Donations -->
                        <div
                            class="bg-gradient-to-br from-primary-50 via-white to-primary-50 rounded-2xl shadow-lg border border-primary-100 overflow-hidden">
                            <div class="p-6">
                                <div class="flex items-center space-x-4">
                                    <div
                                        class="w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-neutral-500 mb-1">Active Donations</p>
                                        <p class="text-2xl font-heading font-bold text-neutral-800">
                                            {{ $donations->total() }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- With Payout Setup -->
                        <div
                            class="bg-gradient-to-br from-purple-50 via-white to-purple-50 rounded-2xl shadow-lg border border-purple-100 overflow-hidden">
                            <div class="p-6">
                                <div class="flex items-center space-x-4">
                                    <div
                                        class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-white">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-neutral-500 mb-1">With Payout Setup</p>
                                        <p class="text-2xl font-heading font-bold text-neutral-800">
                                            {{ $donations->filter(function ($donation) {return $donation->payoutMandate;})->count() }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Donations List -->
                    <div class="bg-white rounded-2xl shadow-lg border border-neutral-100 overflow-hidden">
                        <div class="px-8 py-6 bg-gradient-to-r from-neutral-50 to-white border-b border-neutral-100">
                            <div class="flex items-center space-x-3">
                                <div
                                    class="w-10 h-10 bg-gradient-to-br from-neutral-600 to-neutral-700 rounded-xl flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-white">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0 1 18 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.375m-8.25-3 1.5 1.5 3-3.75" />
                                    </svg>
                                </div>
                                <h2 class="text-xl font-heading font-bold text-neutral-800">Your Active Donations</h2>
                            </div>
                        </div>

                        <div class="p-8">
                            <div class="space-y-6">
                                @foreach ($donations as $donation)
                                    <div
                                        class="bg-gradient-to-br from-white via-success-50/30 to-white rounded-2xl border border-success-200 p-6 hover:shadow-xl hover:scale-[1.01] transition-all duration-300">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <!-- Header Section with Enhanced Design -->
                                                <div class="flex items-center space-x-4 mb-4">
                                                    <div class="flex items-center space-x-3">
                                                        <div
                                                            class="w-10 h-10 bg-gradient-to-br from-neutral-600 to-neutral-700 rounded-xl flex items-center justify-center">
                                                            <span class="text-white font-bold text-sm">#</span>
                                                        </div>
                                                        <span class="text-lg font-heading font-bold text-neutral-800">
                                                            {{ $donation->application_number }}
                                                        </span>
                                                    </div>

                                                    <div class="flex items-center space-x-2">
                                                        <span
                                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-success-100 text-success-700">
                                                            <svg class="w-4 h-4 mr-1" fill="currentColor"
                                                                viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd"
                                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                    clip-rule="evenodd" />
                                                            </svg>
                                                            {{ $donation->status->getDisplayName() }}
                                                        </span>

                                                        @if ($donation->payoutMandate)
                                                            <span
                                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-primary-100 text-primary-700">
                                                                <svg class="w-3 h-3 mr-1" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                                                </svg>
                                                                Payout Setup
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <!-- Title Section -->
                                                <h3 class="text-xl font-heading font-bold text-neutral-800 mb-4">
                                                    {{ $donation->applicant->contribution_name }}
                                                </h3>

                                                <!-- Info Grid -->
                                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                                                    <div class="bg-white rounded-xl p-4 border border-neutral-100">
                                                        <div class="flex items-center space-x-2">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                viewBox="0 0 24 24" stroke-width="1.5"
                                                                stroke="currentColor"
                                                                class="w-4 h-4 text-success-500">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                                                            </svg>

                                                            <span class="text-sm font-medium text-neutral-500">Donation
                                                                Target</span>
                                                        </div>
                                                        <p class="text-lg font-heading font-bold text-success-600">
                                                            KSh
                                                            {{ number_format($donation->applicant->target_amount, 2) }}
                                                        </p>
                                                    </div>

                                                    <div class="bg-white rounded-xl p-4 border border-neutral-100">
                                                        <div class="flex items-center space-x-2">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                viewBox="0 0 24 24" stroke-width="1.5"
                                                                stroke="currentColor"
                                                                class="w-4 h-4 text-primary-500">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                                            </svg>
                                                            <span class="text-sm font-medium text-neutral-500">Target
                                                                Date</span>
                                                        </div>
                                                        <p class="text-sm font-medium text-neutral-800">
                                                            {{ $donation->applicant->target_date->format('M j, Y') }}
                                                        </p>
                                                    </div>

                                                    <div class="bg-white rounded-xl p-4 border border-neutral-100">
                                                        <div class="flex items-center space-x-2">
                                                            <svg class="w-4 h-4 text-purple-500" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                            <span class="text-sm font-medium text-neutral-500">Approved
                                                                On</span>
                                                        </div>
                                                        <p class="text-sm font-medium text-neutral-800">
                                                            {{ $donation->reviewed_at->format('M j, Y') }}
                                                        </p>
                                                    </div>

                                                    <div class="bg-white rounded-xl p-4 border border-neutral-100">
                                                        <div class="flex items-center space-x-2">
                                                            <svg class="w-4 h-4 text-secondary-500" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                            </svg>
                                                            <span
                                                                class="text-sm font-medium text-neutral-500">Type</span>
                                                        </div>
                                                        <p class="text-sm font-medium text-neutral-800">
                                                            {{ $donation->applicant_type === \App\Models\Individual::class ? 'Individual' : 'Company' }}
                                                        </p>
                                                    </div>
                                                </div>

                                                <!-- Description Section -->
                                                @if ($donation->applicant->contribution_description)
                                                    <div
                                                        class="bg-neutral-50 rounded-xl p-4 border border-neutral-100 mb-4">
                                                        <div class="flex items-start space-x-2">
                                                            <svg class="w-6 h-6 text-neutral-500 mt-0.5"
                                                                fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                            <p class="text-sm text-neutral-700 leading-relaxed">
                                                                {{ $donation->applicant->contribution_description }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                @endif

                                                <!-- Payout Mandate Status -->
                                                @if ($donation->payoutMandate)
                                                    <div
                                                        class="bg-success-50 rounded-xl p-4 border border-success-100">
                                                        <div class="flex items-center space-x-3">
                                                            <div
                                                                class="w-8 h-8 bg-success-500 rounded-full flex items-center justify-center">
                                                                <svg class="w-4 h-4 text-white" fill="currentColor"
                                                                    viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd"
                                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                        clip-rule="evenodd" />
                                                                </svg>
                                                            </div>
                                                            <div>
                                                                <p class="text-sm font-medium text-success-700">Payout
                                                                    mandate configured</p>
                                                                <p class="text-xs text-success-600">Ready to receive
                                                                    donations</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Action Buttons -->
                                            <div class="flex flex-col ml-6 space-y-3">
                                                @if ($donation->applicant_type === \App\Models\Individual::class)
                                                    <a href="{{ route('individual.applications.show', $donation->application_number) }}"
                                                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-success-500 to-success-600 border border-transparent rounded-xl text-sm font-medium text-white hover:from-success-600 hover:to-success-700 transition-all duration-200 shadow-sm">
                                                        <svg class="w-4 h-4 mr-2" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                        View Details
                                                    </a>
                                                @else
                                                    <a href="{{ route('company.applications.show', $donation->application_number) }}"
                                                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-success-500 to-success-600 border border-transparent rounded-xl text-sm font-medium text-white hover:from-success-600 hover:to-success-700 transition-all duration-200 shadow-sm">
                                                        <svg class="w-4 h-4 mr-2" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                        View Details
                                                    </a>
                                                @endif

                                                <a href="{{ route('donations.show', $donation->application_number) }}"
                                                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-primary-500 to-primary-600 border border-transparent rounded-xl text-sm font-medium text-white hover:from-primary-600 hover:to-primary-700 transition-all duration-200 shadow-sm">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                                    </svg>
                                                    Manage Donation
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Pagination -->
                            @if ($donations->hasPages())
                                <div class="mt-8 pt-6 border-t border-neutral-100">
                                    <div class="flex items-center justify-center">
                                        {{ $donations->appends(request()->query())->links() }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="bg-white rounded-2xl shadow-lg border border-neutral-100 overflow-hidden">
                        <div class="py-16 text-center">
                            <div
                                class="w-24 h-24 bg-gradient-to-br from-neutral-100 to-neutral-200 rounded-2xl flex items-center justify-center mx-auto mb-6">
                                <div class="text-4xl">💝</div>
                            </div>
                            <h3 class="text-2xl font-heading font-bold text-neutral-800 mb-3">No donations yet</h3>
                            <p class="text-neutral-600 max-w-md mx-auto mb-8 leading-relaxed">
                                You don't have any approved donation setups yet. Once your applications are approved,
                                they'll appear here and you can start collecting donations.
                            </p>
                            <a href="{{ route('dashboard') }}"
                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-primary-500 to-primary-600 border border-transparent rounded-xl text-sm font-medium text-white hover:from-primary-600 hover:to-primary-700 transition-all duration-200 shadow-lg">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Back to Dashboard
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
