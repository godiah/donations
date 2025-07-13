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
                                <div>
                                    <h2 class="text-2xl font-heading font-bold">Donation Management</h2>
                                    <p class="text-success-100 mt-1">{{ $application->applicant->contribution_name }}
                                    </p>
                                </div>
                            </div>
                            <a href="{{ route('donations') }}"
                                class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white px-4 py-2 rounded-xl font-semibold transition-all duration-200 transform hover:scale-[1.02] flex items-center gap-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20 11H7.83L13.42 5.41L12 4L4 12L12 20L13.41 18.59L7.83 13H20V11Z" />
                                </svg>
                                Back to Donations
                            </a>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    {{-- Main Content --}}
                    <div class="lg:col-span-2 space-y-8">
                        {{-- Progress Section --}}
                        <div
                            class="bg-gradient-to-br from-white via-neutral-50 to-primary-50 rounded-2xl shadow-lg border border-neutral-100 overflow-hidden">
                            <div class="px-8 py-6 bg-white/80 backdrop-blur-sm border-b border-neutral-100">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div
                                            class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                            </svg>
                                        </div>
                                        <h2 class="text-xl font-heading font-bold text-neutral-800">Collection Progress
                                        </h2>
                                    </div>
                                    <div class="text-sm text-neutral-500 bg-neutral-100 px-3 py-1 rounded-full">
                                        Updated {{ $combinedStats['last_updated']->diffForHumans() }}
                                    </div>
                                </div>
                            </div>

                            {{-- Progress Display --}}
                            <div class="p-8 space-y-8">
                                {{-- Circular Progress with Animation --}}
                                <div class="flex items-center justify-center">
                                    <div class="relative w-48 h-48">
                                        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                                            <circle cx="50" cy="50" r="40" stroke="currentColor"
                                                stroke-width="4" fill="none" class="text-neutral-200" />
                                            <circle cx="50" cy="50" r="40" stroke="currentColor"
                                                stroke-width="4" fill="none"
                                                class="text-gradient-to-r from-success-500 to-primary-500"
                                                stroke-dasharray="251.2"
                                                stroke-dashoffset="{{ 251.2 - (251.2 * min($combinedStats['progress_percentage'], 100)) / 100 }}"
                                                stroke-linecap="round"
                                                style="transition: stroke-dashoffset 2s ease-in-out;" />
                                        </svg>
                                        <div class="absolute inset-0 flex items-center justify-center">
                                            <div class="text-center">
                                                <div class="text-3xl font-heading font-bold text-neutral-800">
                                                    {{ number_format($combinedStats['progress_percentage'], 1) }}%
                                                </div>
                                                <div class="text-sm text-neutral-500 font-medium">Complete</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Progress Description with Better Typography --}}
                                <div class="text-center">
                                    @if ($combinedStats['progress_type'] === 'target_based')
                                        @if ($combinedStats['target_reached'])
                                            <div
                                                class="inline-flex items-center space-x-2 bg-success-50 text-success-700 px-4 py-2 rounded-full font-medium">
                                                <span class="text-lg">🎉</span>
                                                <span>Target reached! Congratulations!</span>
                                            </div>
                                        @elseif($combinedStats['remaining_to_target'] > 0)
                                            <div class="text-neutral-600">
                                                <span class="font-semibold text-primary-600">KES
                                                    {{ $combinedStats['remaining_to_target_formatted'] }}</span>
                                                <span class="text-sm"> remaining to reach your goal</span>
                                            </div>
                                        @endif
                                    @else
                                        <div class="text-neutral-600">
                                            @if ($combinedStats['total_contributors'] === 0)
                                                <span class="text-primary-600 font-medium">Campaign is ready to receive
                                                    contributions</span>
                                            @elseif($combinedStats['total_contributors'] < 20)
                                                <span>Building momentum with <span
                                                        class="font-semibold text-primary-600">{{ $combinedStats['total_contributors'] }}</span>
                                                    contribution{{ $combinedStats['total_contributors'] !== 1 ? 's' : '' }}</span>
                                            @elseif($combinedStats['total_contributors'] < 100)
                                                <span>Great progress with <span
                                                        class="font-semibold text-success-600">{{ $combinedStats['total_contributors'] }}</span>
                                                    supporters!</span>
                                            @else
                                                <span>Amazing support from <span
                                                        class="font-semibold text-success-600">{{ $combinedStats['total_contributors'] }}</span>
                                                    contributors!</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                {{-- Amount Display with Better Visual Hierarchy --}}
                                <div class="flex justify-center items-center space-x-12">
                                    <div class="text-center">
                                        <div class="text-3xl font-heading font-bold text-success-600 mb-1">
                                            KES {{ $combinedStats['total_raised_formatted'] }}
                                        </div>
                                        <div class="text-sm font-medium text-neutral-500 uppercase tracking-wide">Raised
                                        </div>
                                    </div>

                                    @if ($combinedStats['has_target'])
                                        <div class="w-px h-12 bg-neutral-200"></div>
                                        <div class="text-center">
                                            <div class="text-3xl font-heading font-bold text-neutral-700 mb-1">
                                                KES {{ $combinedStats['target_amount_formatted'] }}
                                            </div>
                                            <div class="text-sm font-medium text-neutral-500 uppercase tracking-wide">
                                                Goal</div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Enhanced Statistics Grid --}}
                            <div class="px-8 pb-8">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    {{-- Contributors --}}
                                    <div
                                        class="bg-white/70 backdrop-blur-sm rounded-xl p-6 border border-neutral-100 hover:shadow-md transition-all duration-200">
                                        <div class="flex items-center space-x-3">
                                            <div
                                                class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="text-2xl font-heading font-bold text-neutral-800">
                                                    {{ $combinedStats['total_contributors'] }}</div>
                                                <div class="text-sm font-medium text-neutral-500">
                                                    Contributor{{ $combinedStats['total_contributors'] !== 1 ? 's' : '' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Average Contribution --}}
                                    <div
                                        class="bg-white/70 backdrop-blur-sm rounded-xl p-6 border border-neutral-100 hover:shadow-md transition-all duration-200">
                                        <div class="flex items-center space-x-3">
                                            <div
                                                class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="text-2xl font-heading font-bold text-neutral-800">
                                                    {{ $combinedStats['average_contribution_formatted'] }}</div>
                                                <div class="text-sm font-medium text-neutral-500">Average</div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Active Links --}}
                                    <div
                                        class="bg-white/70 backdrop-blur-sm rounded-xl p-6 border border-neutral-100 hover:shadow-md transition-all duration-200">
                                        <div class="flex items-center space-x-3">
                                            <div
                                                class="w-10 h-10 bg-gradient-to-br from-success-500 to-success-600 rounded-xl flex items-center justify-center">
                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="text-2xl font-heading font-bold text-neutral-800">
                                                    {{ $combinedStats['active_donation_links'] }}</div>
                                                <div class="text-sm font-medium text-neutral-500">Active
                                                    Link{{ $combinedStats['active_donation_links'] !== 1 ? 's' : '' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Currency Breakdown (if multiple currencies) --}}
                            @if ($combinedStats['has_multiple_currencies'])
                                <div class="px-8 pb-8">
                                    <div class="bg-white/50 rounded-xl p-6 border border-neutral-100">
                                        <h3 class="font-heading font-bold text-neutral-800 mb-4 flex items-center">
                                            <svg class="w-5 h-5 mr-2 text-secondary-500" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                            </svg>
                                            Currency Breakdown
                                        </h3>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            @foreach ($combinedStats['currency_breakdown'] as $currency => $breakdown)
                                                <div
                                                    class="bg-white rounded-xl p-4 border border-neutral-100 flex justify-between items-center">
                                                    <div>
                                                        <div class="font-heading font-bold text-neutral-800">
                                                            {{ $currency }}
                                                            {{ number_format($breakdown['total_amount'], 2) }}
                                                        </div>
                                                        <div class="text-sm text-neutral-500">
                                                            {{ $breakdown['count'] }}
                                                            contribution{{ $breakdown['count'] !== 1 ? 's' : '' }}
                                                        </div>
                                                    </div>
                                                    @if ($currency !== 'KES')
                                                        <div class="text-right">
                                                            <div class="text-sm font-medium text-neutral-600">
                                                                ≈ KES
                                                                {{ number_format($breakdown['total_kes_equivalent'], 2) }}
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                        @if (isset($combinedStats['currency_breakdown']['USD']))
                                            <div
                                                class="mt-4 text-xs text-neutral-500 text-center bg-neutral-50 py-2 rounded-lg">
                                                Exchange rate: 1 USD = {{ $combinedStats['exchange_rate_used'] }} KES
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            {{-- Individual Donation Link Performance (Enhanced Collapsible) --}}
                            @if (count($combinedStats['donation_link_stats']) > 1)
                                <div class="px-8 pb-8">
                                    <details
                                        class="group bg-white/50 rounded-xl border border-neutral-100 overflow-hidden">
                                        <summary
                                            class="flex items-center justify-between cursor-pointer p-6 hover:bg-neutral-50 transition-colors">
                                            <div class="flex items-center space-x-3">
                                                <svg class="w-5 h-5 text-primary-500" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                                </svg>
                                                <span class="font-heading font-bold text-neutral-800">Individual Link
                                                    Performance</span>
                                            </div>
                                            <svg class="w-5 h-5 transition-transform group-open:rotate-180 text-neutral-400"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </summary>

                                        <div class="p-6 pt-0 space-y-4">
                                            @foreach ($combinedStats['donation_link_stats'] as $linkStat)
                                                <div
                                                    class="bg-white rounded-xl p-5 border border-neutral-100 hover:shadow-sm transition-all duration-200">
                                                    <div class="flex justify-between items-start mb-4">
                                                        <div>
                                                            <h4 class="font-heading font-bold text-neutral-800 mb-1">
                                                                {{ $linkStat['donation_link']->title ?: 'Donation Link' }}
                                                            </h4>
                                                            <code
                                                                class="text-sm bg-neutral-100 text-neutral-600 px-2 py-1 rounded-lg">
                                                                {{ $linkStat['donation_link']->code }}
                                                            </code>
                                                        </div>
                                                        <span
                                                            class="px-3 py-1 text-xs font-medium rounded-full {{ $linkStat['donation_link']->status === 'active' ? 'bg-success-100 text-success-700' : 'bg-neutral-100 text-neutral-600' }}">
                                                            {{ ucfirst($linkStat['donation_link']->status) }}
                                                        </span>
                                                    </div>

                                                    <div class="grid grid-cols-3 gap-6">
                                                        <div class="text-center">
                                                            <div
                                                                class="text-xl font-heading font-bold text-success-600">
                                                                KES {{ $linkStat['stats']['total_raised_formatted'] }}
                                                            </div>
                                                            <div class="text-sm text-neutral-500 font-medium">Raised
                                                            </div>
                                                        </div>
                                                        <div class="text-center">
                                                            <div class="text-xl font-heading font-bold text-blue-600">
                                                                {{ $linkStat['stats']['total_contributors'] }}
                                                            </div>
                                                            <div class="text-sm text-neutral-500 font-medium">
                                                                Contributors
                                                            </div>
                                                        </div>
                                                        <div class="text-center">
                                                            <div
                                                                class="text-xl font-heading font-bold text-purple-600">
                                                                {{ number_format($linkStat['stats']['progress_percentage'], 1) }}%
                                                            </div>
                                                            <div class="text-sm text-neutral-500 font-medium">Progress
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </details>
                                </div>
                            @endif

                            {{-- Error State --}}
                            @if (isset($combinedStats['error']) && $combinedStats['error'])
                                <div class="px-8 pb-8">
                                    <div class="bg-secondary-50 border border-secondary-200 rounded-xl p-4">
                                        <div class="flex items-center space-x-3">
                                            <svg class="w-5 h-5 text-secondary-600" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.99-.833-2.76 0L4.054 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                            </svg>
                                            <span class="text-sm text-secondary-800 font-medium">
                                                Statistics may not be completely accurate. Please refresh the page if
                                                needed.
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Fee Structure Section --}}
                        <div class="bg-white rounded-2xl shadow-lg border border-neutral-100 overflow-hidden">
                            <div
                                class="px-8 py-6 bg-gradient-to-r from-secondary-50 to-secondary-100 border-b border-neutral-100">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="w-10 h-10 bg-gradient-to-br from-secondary-500 to-secondary-600 rounded-xl flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-white">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
                                        </svg>
                                    </div>
                                    <h2 class="text-xl font-heading font-bold text-neutral-800">Fee Structure</h2>
                                </div>
                            </div>
                            <div class="p-8">
                                @php
                                    $feeStructure = $application->fee_structure ?? [
                                        'type' => 'percentage',
                                        'value' => '5%',
                                        'description' => '5% of each contribution',
                                    ];
                                @endphp

                                <div
                                    class="bg-gradient-to-r from-blue-50 to-primary-50 rounded-xl p-6 border border-blue-100">
                                    <div class="flex items-center space-x-4">
                                        <div
                                            class="w-12 h-12 bg-gradient-to-br from-blue-500 to-primary-600 rounded-xl flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                class="w-6 h-6 text-white">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="font-heading font-bold text-neutral-800 mb-1">
                                                {{ ucfirst($feeStructure['type']) }} Fee</h3>
                                            <p class="text-neutral-600">{{ $feeStructure['description'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Donation Links Section --}}
                        <div class="bg-white rounded-2xl shadow-lg border border-neutral-100 overflow-hidden">
                            <div
                                class="px-8 py-6 bg-gradient-to-r from-primary-50 to-blue-50 border-b border-neutral-100">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                        </svg>
                                    </div>
                                    <h2 class="text-xl font-heading font-bold text-neutral-800">Donation Link</h2>
                                </div>
                            </div>
                            <div class="p-8">
                                @if ($donationLinks->count() > 0)
                                    <div class="space-y-4">
                                        @foreach ($donationLinks as $link)
                                            <div
                                                class="bg-gradient-to-r from-neutral-50 to-white rounded-xl p-6 border border-neutral-200 hover:shadow-md transition-all duration-200">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex-1">
                                                        <div class="flex items-center space-x-3 mb-3">
                                                            <code
                                                                class="text-sm bg-neutral-100 text-neutral-700 px-3 py-1 rounded-lg font-medium">
                                                                {{ Str::limit($link->code, 12) }}...
                                                            </code>
                                                            <button onclick="copyToClipboard('{{ $link->full_url }}')"
                                                                class="text-neutral-400 hover:text-primary-600 transition-colors p-1 rounded-lg hover:bg-primary-50"
                                                                title="Copy full URL">
                                                                <svg class="w-4 h-4" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                                </svg>
                                                            </button>
                                                        </div>
                                                        <div
                                                            class="flex items-center space-x-6 text-sm text-neutral-600">
                                                            <div class="flex items-center space-x-1">
                                                                <svg class="w-4 h-4" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                                </svg>
                                                                <span class="font-medium">{{ $link->access_count }}
                                                                    visits</span>
                                                            </div>
                                                            <div class="flex items-center space-x-1">
                                                                <svg class="w-4 h-4" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4h3a1 1 0 011 1v1a1 1 0 01-1 1H5a1 1 0 01-1-1V8a1 1 0 011-1h3z" />
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8" />
                                                                </svg>
                                                                <span>Created
                                                                    {{ $link->created_at->format('M d, Y') }}</span>
                                                            </div>
                                                            @if ($link->expires_at)
                                                                <div class="flex items-center space-x-1">
                                                                    <svg class="w-4 h-4" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                    </svg>
                                                                    <span>Expires
                                                                        {{ $link->expires_at->format('M d, Y') }}</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center space-x-3">
                                                        <span
                                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $link->status === 'active' ? 'bg-success-100 text-success-700' : 'bg-danger-100 text-danger-700' }}">
                                                            {{ ucfirst($link->status) }}
                                                        </span>
                                                        @if ($link->isExpired())
                                                            <span
                                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-secondary-100 text-secondary-700">
                                                                Expired
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-12">
                                        <div
                                            class="w-20 h-20 bg-gradient-to-br from-neutral-100 to-neutral-200 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                            <svg class="w-10 h-10 text-neutral-400" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-heading font-bold text-neutral-800 mb-2">No donation
                                            links yet
                                        </h3>
                                        <p class="text-neutral-500 mb-6 max-w-sm mx-auto">Get started by creating your
                                            first
                                            donation link to begin collecting contributions.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Enhanced Sidebar --}}
                    <div class="space-y-8">
                        {{-- Modern Payout Methods Section --}}
                        <div class="bg-white rounded-2xl shadow-lg border border-neutral-100 overflow-hidden">
                            <div
                                class="px-6 py-5 bg-gradient-to-r from-success-50 to-green-50 border-b border-neutral-100">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div
                                            class="w-10 h-10 bg-gradient-to-br from-success-500 to-success-600 rounded-xl flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                            </svg>
                                        </div>
                                        <h2 class="text-lg font-heading font-bold text-neutral-800">Payout Methods</h2>
                                    </div>
                                    @if ($canViewPayoutMethods)
                                        <a href="{{ route('payout-methods.create') }}"
                                            class="text-sm font-medium text-success-600 hover:text-success-700 bg-success-100 hover:bg-success-200 px-3 py-1 rounded-lg transition-all duration-200">
                                            Add Method
                                        </a>
                                    @endif
                                </div>
                            </div>
                            <div class="p-6">
                                @if ($canViewPayoutMethods)
                                    @if ($payoutMethods->count() > 0)
                                        <div class="space-y-4">
                                            @foreach ($payoutMethods as $method)
                                                <div
                                                    class="bg-gradient-to-r from-neutral-50 to-white rounded-xl p-4 border border-neutral-200 hover:shadow-sm transition-all duration-200">
                                                    <div class="flex items-start justify-between">
                                                        <div class="flex-1">
                                                            <div class="flex items-center space-x-2 mb-2">
                                                                <span class="font-heading font-bold text-neutral-800">
                                                                    {{ $method->formatted_account }}
                                                                </span>
                                                                <span
                                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $method->status_badge_color }}">
                                                                    {{ $method->status_text }}
                                                                </span>
                                                                @if ($method->is_primary)
                                                                    <span
                                                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-success-100 text-success-700">
                                                                        Primary
                                                                    </span>
                                                                @endif
                                                            </div>

                                                            {{-- Display account name based on type --}}
                                                            <p class="text-sm font-medium text-neutral-600 mb-1">
                                                                @if ($method->type === 'paybill')
                                                                    {{ $method->paybill_account_name }}
                                                                @else
                                                                    {{ $method->account_name }}
                                                                @endif
                                                            </p>

                                                            {{-- Display type with proper labels --}}
                                                            <div class="flex items-center space-x-2 mb-2">
                                                                <p class="text-xs text-neutral-500">
                                                                    @if ($method->type === 'bank_account')
                                                                        Bank Account
                                                                    @elseif ($method->type === 'mobile_money')
                                                                        Mobile Money
                                                                    @elseif ($method->type === 'paybill')
                                                                        Paybill
                                                                    @else
                                                                        {{ $method->type_display }}
                                                                    @endif
                                                                </p>

                                                                {{-- Show provider for mobile money and paybill --}}
                                                                @if (in_array($method->type, ['mobile_money', 'paybill']) && $method->provider)
                                                                    <span class="text-xs text-neutral-400">•</span>
                                                                    <span
                                                                        class="text-xs text-neutral-500">{{ $method->provider }}</span>
                                                                @endif
                                                            </div>

                                                            {{-- Additional paybill information --}}
                                                            @if ($method->type === 'paybill')
                                                                <div class="text-xs text-neutral-400 space-y-1">
                                                                    <p>Paybill: {{ $method->paybill_number }}</p>
                                                                    @if ($method->paybill_description)
                                                                        <p class="italic">
                                                                            {{ $method->paybill_description }}</p>
                                                                    @endif
                                                                </div>
                                                            @endif

                                                            {{-- Bank information --}}
                                                            @if ($method->type === 'bank_account' && $method->bank)
                                                                <p class="text-xs text-neutral-400">
                                                                    {{ $method->bank->display_name }}</p>
                                                            @endif
                                                        </div>

                                                        {{-- Set Primary Button --}}
                                                        @if (!$method->is_primary)
                                                            <form method="POST"
                                                                action="{{ route('payout-methods.set-primary', $method->id) }}"
                                                                class="inline mt-2">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit"
                                                                    class="text-xs font-medium text-primary-600 hover:text-primary-700 bg-primary-50 hover:bg-primary-100 px-2 py-1 rounded-lg transition-all duration-200">
                                                                    Set Primary
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="mt-6 pt-4 border-t border-neutral-100">
                                            <a href="{{ route('payout-methods.index') }}"
                                                class="inline-flex items-center text-sm font-medium text-primary-600 hover:text-primary-700 transition-colors">
                                                <span>Manage all methods</span>
                                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M9 5l7 7-7 7" />
                                                </svg>
                                            </a>
                                        </div>
                                    @else
                                        <div class="text-center py-8">
                                            <div
                                                class="w-16 h-16 bg-gradient-to-br from-neutral-100 to-neutral-200 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                                <svg class="w-8 h-8 text-neutral-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                                </svg>
                                            </div>
                                            <h3 class="text-lg font-heading font-bold text-neutral-800 mb-2">No payout
                                                methods
                                            </h3>
                                            <p class="text-sm text-neutral-500 mb-6">Add a payout method to withdraw
                                                donations.
                                            </p>
                                            <a href="{{ route('payout-methods.create') }}"
                                                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-success-500 to-success-600 border border-transparent rounded-xl text-sm font-medium text-white hover:from-success-600 hover:to-success-700 transition-all duration-200 shadow-sm">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M12 4v16m8-8H4" />
                                                </svg>
                                                Set Up Payout Method
                                            </a>
                                        </div>
                                    @endif
                                @else
                                    <div class="text-center py-8">
                                        <div
                                            class="w-16 h-16 bg-gradient-to-br from-danger-100 to-red-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                            <svg class="w-8 h-8 text-danger-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.664-.833-2.464 0L5.268 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-heading font-bold text-neutral-800 mb-2">Access Not
                                            Authorized
                                        </h3>
                                        <p class="text-sm text-neutral-600 mb-4">
                                            You are not authorized to view or manage payout methods for this
                                            application.
                                        </p>
                                        @if ($checkerInfo)
                                            <div class="bg-primary-50 rounded-xl p-4 border border-primary-100">
                                                <p class="text-sm text-neutral-700">
                                                    <span class="font-medium">Payout Checker:</span>
                                                    {{ $checkerInfo['name'] }}
                                                    <br>
                                                    <a href="mailto:{{ $checkerInfo['email'] }}"
                                                        class="text-primary-600 hover:text-primary-700 font-medium">{{ $checkerInfo['email'] }}</a>
                                                    <br>
                                                    <span class="text-xs text-neutral-500">Will set up payout methods
                                                        and
                                                        approve payments</span>
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Enhanced Quick Stats --}}
                        <div class="bg-white rounded-2xl shadow-lg border border-neutral-100 overflow-hidden">
                            <div
                                class="px-6 py-5 bg-gradient-to-r from-purple-50 to-blue-50 border-b border-neutral-100">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                    </div>
                                    <h2 class="text-lg font-heading font-bold text-neutral-800">Quick Stats</h2>
                                </div>
                            </div>
                            <div class="p-6">
                                <div class="space-y-4">
                                    <div
                                        class="flex justify-between items-center p-3 bg-gradient-to-r from-neutral-50 to-white rounded-xl border border-neutral-100">
                                        <div class="flex items-center space-x-3">
                                            <div
                                                class="w-8 h-8 bg-success-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-4 h-4 text-success-600" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                                </svg>
                                            </div>
                                            <span class="text-sm font-medium text-neutral-600">Active Links</span>
                                        </div>
                                        <span class="text-lg font-heading font-bold text-neutral-800">
                                            {{ $donationLinks->where('status', 'active')->count() }}
                                        </span>
                                    </div>

                                    <div
                                        class="flex justify-between items-center p-3 bg-gradient-to-r from-neutral-50 to-white rounded-xl border border-neutral-100">
                                        <div class="flex items-center space-x-3">
                                            <div
                                                class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-4 h-4 text-blue-600" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </div>
                                            <span class="text-sm font-medium text-neutral-600">Total Visits</span>
                                        </div>
                                        <span class="text-lg font-heading font-bold text-neutral-800">
                                            {{ $donationLinks->sum('access_count') }}
                                        </span>
                                    </div>

                                    <div
                                        class="flex justify-between items-center p-3 bg-gradient-to-r from-neutral-50 to-white rounded-xl border border-neutral-100">
                                        <div class="flex items-center space-x-3">
                                            <div
                                                class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-4 h-4 text-purple-600" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                                </svg>
                                            </div>
                                            <span class="text-sm font-medium text-neutral-600">Payout Methods</span>
                                        </div>
                                        <span class="text-lg font-heading font-bold text-neutral-800">
                                            {{ $payoutMethods->count() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
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
                // Create enhanced toast notification
                const toast = document.createElement('div');
                toast.className =
                    'fixed top-6 right-6 bg-gradient-to-r from-success-500 to-success-600 text-white px-6 py-3 rounded-xl shadow-lg z-50 flex items-center space-x-3 transform translate-x-full opacity-0 transition-all duration-300';
                toast.innerHTML = `
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="font-medium">Link copied to clipboard!</span>
                `;
                document.body.appendChild(toast);

                // Animate in
                setTimeout(() => {
                    toast.classList.remove('translate-x-full', 'opacity-0');
                }, 10);

                // Animate out and remove
                setTimeout(() => {
                    toast.classList.add('translate-x-full', 'opacity-0');
                    setTimeout(() => {
                        if (document.body.contains(toast)) {
                            document.body.removeChild(toast);
                        }
                    }, 300);
                }, 3000);
            }).catch(function(err) {
                console.error('Could not copy text: ', err);

                // Error toast
                const errorToast = document.createElement('div');
                errorToast.className =
                    'fixed top-6 right-6 bg-gradient-to-r from-danger-500 to-danger-600 text-white px-6 py-3 rounded-xl shadow-lg z-50 flex items-center space-x-3';
                errorToast.innerHTML = `
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    <span class="font-medium">Failed to copy link</span>
                `;
                document.body.appendChild(errorToast);

                setTimeout(() => {
                    if (document.body.contains(errorToast)) {
                        document.body.removeChild(errorToast);
                    }
                }, 3000);
            });
        }
    </script>
</x-app-layout>
