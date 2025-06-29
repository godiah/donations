@extends('layouts.donation')

@section('title', 'Donation Link Expired')

@section('content')
    <div class="p-6 sm:p-8">
        <div class="max-w-md mx-auto">
            <!-- Header with Icon -->
            <header class="text-center mb-8">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900" id="expired-heading">Donation Link Expired</h1>
            </header>

            <!-- Message Section -->
            <section class="text-center mb-8" aria-labelledby="expired-heading">
                @if ($donationLink->isExpired())
                    <p class="text-base text-gray-600" role="alert">
                        This donation link expired on {{ $donationLink->expires_at->format('M d, Y \a\t g:i A') }}.
                    </p>
                @elseif ($donationLink->status === 'inactive')
                    <p class="text-base text-gray-600" role="alert">
                        This donation link has been deactivated and is no longer accepting donations.
                    </p>
                @else
                    <p class="text-base text-gray-600" role="alert">
                        This donation link is no longer active.
                    </p>
                @endif
            </section>

            <!-- Link Details -->
            <section class="bg-gray-50 rounded-lg p-6 mb-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Link Information</h2>
                <dl class="text-sm text-gray-600 space-y-3">
                    <div class="flex justify-between">
                        <dt class="font-medium">Application</dt>
                        <dd>#{{ $donationLink->application->application_number ?? 'N/A' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="font-medium">Created</dt>
                        <dd>{{ $donationLink->created_at->format('M d, Y') }}</dd>
                    </div>
                    @if ($donationLink->expires_at)
                        <div class="flex justify-between">
                            <dt class="font-medium">Expired</dt>
                            <dd>{{ $donationLink->expires_at->format('M d, Y \a\t g:i A') }}</dd>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <dt class="font-medium">Status</dt>
                        <dd class="capitalize {{ $donationLink->status === 'active' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $donationLink->status }}
                        </dd>
                    </div>
                </dl>
            </section>

            <!-- Contact Information -->
            <section class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
                <h2 class="text-lg font-semibold text-blue-900 mb-4">Need Assistance?</h2>
                <p class="text-sm text-blue-800 mb-4">
                    If you believe this is an error or need help with your donation, please reach out to our support team.
                </p>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="mailto:support@example.com"
                        class="inline-flex items-center justify-center px-4 py-2 border border-blue-300 rounded-md text-sm font-medium text-blue-700 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors"
                        aria-label="Email support team">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                            </path>
                        </svg>
                        Email Support
                    </a>
                    <a href="tel:+254700000000"
                        class="inline-flex items-center justify-center px-4 py-2 border border-blue-300 rounded-md text-sm font-medium text-blue-700 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors"
                        aria-label="Call support team">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                            </path>
                        </svg>
                        Call Support
                    </a>
                </div>
            </section>

            <!-- Action Buttons -->
            <div class="space-y-3">
                <a href="{{ url('/') }}"
                    class="block w-full text-center py-3 px-4 rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
                    aria-label="Return to homepage">
                    Return to Homepage
                </a>
                <button onclick="window.history.back()"
                    class="block w-full text-center py-3 px-4 rounded-md text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
                    aria-label="Go back to previous page">
                    Go Back
                </button>
            </div>
        </div>
    </div>
@endsection
