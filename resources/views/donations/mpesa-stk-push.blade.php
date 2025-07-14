@extends('layouts.donation')

@section('title', 'M-Pesa Payment - Complete Your Transaction')

@section('content')
    <div class="min-h-screen py-8">
        <div class="max-w-5xl px-4 mx-auto">
            <div class="overflow-hidden bg-white border shadow-2xl rounded-2xl border-neutral-100">
                <!-- Enhanced Header -->
                <div class="relative px-8 py-6 overflow-hidden text-center bg-gradient-to-r from-success-500 to-success-600">
                    <!-- Decorative Background Pattern -->
                    <div class="absolute inset-0 origin-top-left transform -skew-y-6 bg-white/10"></div>

                    <div class="relative z-10">
                        <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-white shadow-lg rounded-2xl">
                            <svg class="w-8 h-8 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h1 class="mb-2 text-2xl font-bold text-white font-heading">M-Pesa Payment</h1>
                        <p class="text-success-100">Complete your donation payment</p>
                    </div>
                </div>

                <div class="p-8">
                    <!-- Enhanced Payment Details -->
                    <div
                        class="p-6 mb-8 border bg-gradient-to-r from-success-50 to-green-50 rounded-2xl border-success-200">
                        <div class="flex items-center mb-4 space-x-3">
                            <div class="flex items-center justify-center w-8 h-8 bg-success-500 rounded-xl">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h3 class="font-bold font-heading text-success-800">Payment Details</h3>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 bg-white rounded-xl">
                                <span class="font-medium text-neutral-600">Amount:</span>
                                <span class="text-lg font-bold font-heading text-success-700">KES
                                    {{ number_format($contribution->amount, 2) }}</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-white rounded-xl">
                                <span class="font-medium text-neutral-600">Reference:</span>
                                <code
                                    class="px-2 py-1 font-mono text-sm rounded bg-neutral-100 text-neutral-700">DON_{{ $contribution->id }}</code>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-white rounded-xl">
                                <span class="font-medium text-neutral-600">Environment:</span>
                                <span class="font-medium capitalize text-neutral-800">{{ $environment }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Enhanced Status Display -->
                    <div class="mb-8">
                        <div id="payment-status" class="text-center">
                            <div class="relative w-16 h-16 mx-auto mb-4">
                                <div class="w-16 h-16 border-4 rounded-full border-primary-200 animate-spin">
                                    <div
                                        class="w-16 h-16 border-4 rounded-full border-primary-500 border-t-transparent animate-spin">
                                    </div>
                                </div>
                            </div>
                            <p class="mb-2 text-xl font-bold font-heading text-neutral-800">Processing Payment...</p>
                            <p class="text-neutral-600">Please check your phone for the M-Pesa prompt</p>
                        </div>
                    </div>

                    <!-- Enhanced Instructions -->
                    <div class="p-6 mb-8 border bg-gradient-to-r from-primary-50 to-blue-50 rounded-2xl border-primary-200">
                        <div class="flex items-center mb-4 space-x-3">
                            <div class="flex items-center justify-center w-8 h-8 bg-primary-500 rounded-xl">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="font-bold font-heading text-primary-800">Instructions</h3>
                        </div>
                        <ol class="space-y-4">
                            <li class="flex items-start space-x-3">
                                <div
                                    class="w-8 h-8 bg-primary-500 text-white rounded-full flex items-center justify-center text-sm font-bold mt-0.5">
                                    1</div>
                                <div class="flex-1">
                                    <p class="font-medium text-neutral-800">Check your phone for the M-Pesa payment prompt
                                    </p>
                                    <p class="mt-1 text-sm text-neutral-600">You should receive an SMS notification shortly
                                    </p>
                                </div>
                            </li>
                            <li class="flex items-start space-x-3">
                                <div
                                    class="w-8 h-8 bg-primary-500 text-white rounded-full flex items-center justify-center text-sm font-bold mt-0.5">
                                    2</div>
                                <div class="flex-1">
                                    <p class="font-medium text-neutral-800">Enter your M-Pesa PIN to complete the payment
                                    </p>
                                    <p class="mt-1 text-sm text-neutral-600">Follow the prompts on your phone screen</p>
                                </div>
                            </li>
                            <li class="flex items-start space-x-3">
                                <div
                                    class="w-8 h-8 bg-primary-500 text-white rounded-full flex items-center justify-center text-sm font-bold mt-0.5">
                                    3</div>
                                <div class="flex-1">
                                    <p class="font-medium text-neutral-800">Wait for confirmation - this page will update
                                        automatically</p>
                                    <p class="mt-1 text-sm text-neutral-600">No need to refresh the page manually</p>
                                </div>
                            </li>
                        </ol>
                    </div>

                    <!-- Enhanced Action Buttons -->
                    <div class="mb-8 space-y-4">
                        <button id="check-status-btn" onclick="checkPaymentStatus()"
                            class="w-full px-6 py-4 font-medium text-white transition-all duration-200 shadow-lg bg-gradient-to-r from-primary-500 to-primary-600 rounded-xl hover:from-primary-600 hover:to-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="inline w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Check Payment Status
                        </button>

                        <button onclick="window.location.reload()"
                            class="w-full px-6 py-4 font-medium transition-all duration-200 border bg-neutral-100 text-neutral-700 rounded-xl hover:bg-neutral-200 focus:outline-none focus:ring-2 focus:ring-neutral-500 focus:ring-offset-2 border-neutral-200">
                            <svg class="inline w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Refresh Page
                        </button>
                    </div>

                    <!-- Enhanced Help Section -->
                    <div
                        class="p-6 border bg-gradient-to-r from-secondary-50 to-orange-50 rounded-2xl border-secondary-200">
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 bg-secondary-500 rounded-xl flex items-center justify-center mt-0.5">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="mb-2 font-bold font-heading text-neutral-800">Need Help?</h4>
                                <p class="text-sm leading-relaxed text-neutral-700">
                                    If you don't receive the M-Pesa prompt or encounter any issues, please contact our
                                    support team for assistance.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Success Modal -->
    <div id="success-modal" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="w-full max-w-md overflow-hidden bg-white border shadow-2xl rounded-2xl border-neutral-100">
                <div class="px-8 py-6 text-center bg-gradient-to-r from-success-500 to-success-600">
                    <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-white shadow-lg rounded-2xl">
                        <svg class="w-8 h-8 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 class="mb-2 text-2xl font-bold text-white font-heading">Payment Successful!</h3>
                    <p class="text-success-100">Thank you for your generous donation!</p>
                </div>
                <div class="p-8">
                    <div id="success-details"
                        class="p-6 mb-6 border bg-gradient-to-r from-success-50 to-green-50 rounded-2xl border-success-200">
                        <!-- Success details will be populated here -->
                    </div>
                    <button onclick="redirectToSuccess()"
                        class="w-full px-6 py-4 font-medium text-white transition-all duration-200 shadow-lg bg-gradient-to-r from-success-500 to-success-600 rounded-xl hover:from-success-600 hover:to-success-700 focus:outline-none focus:ring-2 focus:ring-success-500 focus:ring-offset-2 hover:shadow-xl">
                        <svg class="inline w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Continue
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Error Modal -->
    <div id="error-modal" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="w-full max-w-md overflow-hidden bg-white border shadow-2xl rounded-2xl border-neutral-100">
                <div class="px-8 py-6 text-center bg-gradient-to-r from-danger-500 to-danger-600">
                    <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-white shadow-lg rounded-2xl">
                        <svg class="w-8 h-8 text-danger-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <h3 class="mb-2 text-2xl font-bold text-white font-heading">Payment Failed</h3>
                    <p id="error-message" class="text-danger-100">Something went wrong with your payment.</p>
                </div>
                <div class="p-8">
                    <div class="space-y-4">
                        <button onclick="tryAgain()"
                            class="w-full px-6 py-4 font-medium text-white transition-all duration-200 shadow-lg bg-gradient-to-r from-primary-500 to-primary-600 rounded-xl hover:from-primary-600 hover:to-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 hover:shadow-xl">
                            <svg class="inline w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Try Again
                        </button>
                        <button onclick="closeModal('error-modal')"
                            class="w-full px-6 py-4 font-medium transition-all duration-200 border bg-neutral-100 text-neutral-700 rounded-xl hover:bg-neutral-200 focus:outline-none focus:ring-2 focus:ring-neutral-500 focus:ring-offset-2 border-neutral-200">
                            <svg class="inline w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Global variables
        const checkoutRequestId = '{{ $checkout_request_id }}';
        const contributionId = '{{ $contribution->id }}';
        const donationName = '{{ $contribution->donationLink->name ?? 'this cause' }}';
        const contributionAmount = '{{ number_format($contribution->amount, 2) }}';
        let statusCheckInterval;
        let statusCheckCount = 0;
        const maxStatusChecks = 60; // 5 minutes with 5-second intervals

        // Start automatic status checking
        document.addEventListener('DOMContentLoaded', function() {
            // Check if payment is already completed
            @if (isset($payment_completed) && $payment_completed)
                showSuccessModal({
                    ResultDesc: 'Payment completed successfully'
                });
            @else
                startStatusChecking();
            @endif
        });

        // Start automatic status checking
        function startStatusChecking() {
            statusCheckInterval = setInterval(checkPaymentStatus, 5000); // Check every 5 seconds
        }

        // Stop automatic status checking
        function stopStatusChecking() {
            if (statusCheckInterval) {
                clearInterval(statusCheckInterval);
            }
        }

        // Check payment status
        async function checkPaymentStatus() {
            if (statusCheckCount >= maxStatusChecks) {
                stopStatusChecking();
                showTimeoutMessage();
                return;
            }

            statusCheckCount++;

            try {
                const response = await fetch('{{ route('mpesa.status.check') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        checkout_request_id: checkoutRequestId
                    })
                });

                const data = await response.json();

                if (data.success && data.status) {
                    if (data.status === 'completed') {
                        // Payment completed successfully
                        stopStatusChecking();
                        // Show success modal instead of redirecting
                        showSuccessModal(data.data);
                        return;
                    } else if (data.status === 'failed' || data.status === 'cancelled') {
                        // Payment failed or cancelled
                        stopStatusChecking();
                        const message = data.status === 'cancelled' ?
                            'Payment was cancelled. Please try again.' :
                            (data.data.ResultDesc || 'Payment failed');
                        showErrorModal(message);
                        return;
                    } else if (data.status === 'processing') {
                        // Payment still processing, continue checking
                        console.log('Payment still processing...');
                    }
                } else {
                    // API call failed, but don't stop checking yet
                    console.log('Status check API failed:', data.error);
                }

                // Update button text to show checking progress
                updateStatusButton();

            } catch (error) {
                console.error('Status check error:', error);
                // Don't stop checking on network errors, just continue
            }
        }

        // Update status button text
        function updateStatusButton() {
            const button = document.getElementById('check-status-btn');
            const remaining = maxStatusChecks - statusCheckCount;
            button.innerHTML = `
                <svg class="inline w-5 h-5 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Checking Status... (${remaining} checks remaining)
            `;
        }

        // Show success modal
        function showSuccessModal(result) {
            const modal = document.getElementById('success-modal');
            const detailsDiv = document.getElementById('success-details');

            detailsDiv.innerHTML = `
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-white rounded-xl">
                        <span class="font-medium text-neutral-600">Status:</span>
                        <span class="font-bold font-heading text-success-700">Completed</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-white rounded-xl">
                        <span class="font-medium text-neutral-600">Amount:</span>
                        <span class="font-bold font-heading text-success-700">KES {{ number_format($contribution->amount, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-white rounded-xl">
                        <span class="font-medium text-neutral-600">Reference:</span>
                        <code class="px-2 py-1 font-mono text-sm rounded bg-neutral-100 text-neutral-700">DON_{{ $contribution->id }}</code>
                    </div>
                </div>
            `;

            modal.classList.remove('hidden');
            updatePaymentStatus('success');
        }

        // Show error modal
        function showErrorModal(message) {
            const modal = document.getElementById('error-modal');
            const messageElement = document.getElementById('error-message');

            messageElement.textContent = message || 'Payment failed. Please try again.';
            modal.classList.remove('hidden');
            updatePaymentStatus('failed');
        }

        // Show timeout message
        function showTimeoutMessage() {
            updatePaymentStatus('timeout');
            // Also show error modal for timeout
            showErrorModal('Payment request timed out. Please try again or check your M-Pesa messages.');
        }

        // Update payment status display
        function updatePaymentStatus(status) {
            const statusDiv = document.getElementById('payment-status');

            if (status === 'success') {
                statusDiv.innerHTML = `
                    <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-success-100 rounded-2xl">
                        <svg class="w-8 h-8 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <p class="mb-2 text-xl font-bold font-heading text-success-700">Payment Successful!</p>
                    <p class="text-success-600">Your donation has been processed</p>
                `;
            } else if (status === 'failed') {
                statusDiv.innerHTML = `
                    <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-danger-100 rounded-2xl">
                        <svg class="w-8 h-8 text-danger-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <p class="mb-2 text-xl font-bold font-heading text-danger-700">Payment Failed</p>
                    <p class="text-danger-600">Please try again or contact support</p>
                `;
            } else if (status === 'timeout') {
                statusDiv.innerHTML = `
                    <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-secondary-100 rounded-2xl">
                        <svg class="w-8 h-8 text-secondary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <p class="mb-2 text-xl font-bold font-heading text-secondary-700">Payment Timeout</p>
                    <p class="text-secondary-600">Please check your M-Pesa messages or try again</p>
                `;
            }
        }

        // Close modal
        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.add('hidden');
        }

        // Try again function - Creates new STK push request
        function tryAgain() {
            // Redirect to donation form to start fresh payment process
            window.location.href = '{{ route('donation.show', $contribution->donationLink->code) }}';
        }

        // Redirect to success page
        function redirectToSuccess() {
            // Close modal and redirect back to donation form
            closeModal('success-modal');
            setTimeout(() => {
                window.location.href = '{{ route('donation.show', $contribution->donationLink->code) }}';
            }, 1000);
        }

        // Handle page visibility changes
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                stopStatusChecking();
            } else {
                startStatusChecking();
            }
        });

        // Clean up on page unload
        window.addEventListener('beforeunload', function() {
            stopStatusChecking();
        });
    </script>
@endsection
