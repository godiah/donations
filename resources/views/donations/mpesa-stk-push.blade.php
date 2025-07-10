@extends('layouts.donation')

@section('title', 'M-Pesa Payment - Complete Your Transaction')

@section('content')
    <div class="min-h-screen py-8 bg-gray-50">
        <div class="max-w-md mx-auto">
            <div class="p-6 bg-white rounded-lg shadow-lg">
                <!-- Header -->
                <div class="mb-6 text-center">
                    <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-green-100 rounded-full">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 18l9-9-9-9-3 3 6 6-6 6 3 3z" />
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900">M-Pesa Payment</h1>
                    <p class="mt-2 text-gray-600">Complete your donation payment</p>
                </div>

                <!-- Payment Details -->
                <div class="p-4 mb-6 rounded-lg bg-green-50">
                    <h3 class="mb-3 font-semibold text-green-800">Payment Details</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-green-700">Amount:</span>
                            <span class="font-semibold text-green-900">KES
                                {{ number_format($contribution->amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-green-700">Reference:</span>
                            <span class="font-semibold text-green-900">DON_{{ $contribution->id }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-green-700">Environment:</span>
                            <span class="font-semibold text-green-900 capitalize">{{ $environment }}</span>
                        </div>
                    </div>
                </div>

                <!-- Status Display -->
                <div class="mb-6">
                    <div id="payment-status" class="text-center">
                        <div
                            class="w-8 h-8 mx-auto mb-4 border-4 border-blue-500 rounded-full animate-spin border-t-transparent">
                        </div>
                        <p class="text-lg font-semibold text-gray-800">Processing Payment...</p>
                        <p class="mt-2 text-sm text-gray-600">Please check your phone for the M-Pesa prompt</p>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="p-4 mb-6 rounded-lg bg-blue-50">
                    <h3 class="mb-3 font-semibold text-blue-800">Instructions</h3>
                    <ol class="space-y-2 text-sm text-blue-700">
                        <li class="flex items-start">
                            <span
                                class="bg-blue-200 text-blue-800 rounded-full w-5 h-5 flex items-center justify-center text-xs font-semibold mr-3 mt-0.5">1</span>
                            Check your phone for the M-Pesa payment prompt
                        </li>
                        <li class="flex items-start">
                            <span
                                class="bg-blue-200 text-blue-800 rounded-full w-5 h-5 flex items-center justify-center text-xs font-semibold mr-3 mt-0.5">2</span>
                            Enter your M-Pesa PIN to complete the payment
                        </li>
                        <li class="flex items-start">
                            <span
                                class="bg-blue-200 text-blue-800 rounded-full w-5 h-5 flex items-center justify-center text-xs font-semibold mr-3 mt-0.5">3</span>
                            Wait for confirmation - this page will update automatically
                        </li>
                    </ol>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-3">
                    <button id="check-status-btn" onclick="checkPaymentStatus()"
                        class="w-full px-4 py-3 text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
                        Check Payment Status
                    </button>

                    <button onclick="window.location.reload()"
                        class="w-full px-4 py-3 text-white transition-colors bg-gray-600 rounded-lg hover:bg-gray-700">
                        Refresh Page
                    </button>
                </div>

                <!-- Help Section -->
                <div class="p-4 mt-6 rounded-lg bg-gray-50">
                    <h4 class="mb-2 font-semibold text-gray-800">Need Help?</h4>
                    <p class="text-sm text-gray-600">
                        If you don't receive the M-Pesa prompt or encounter any issues, please contact our support team.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="success-modal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="w-full max-w-md p-6 bg-white rounded-lg">
                <div class="text-center">
                    <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-green-100 rounded-full">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 class="mb-2 text-xl font-bold text-gray-900">Payment Successful!</h3>
                    <p class="mb-4 text-gray-600">Thank you for your generous donation!</p>
                    <div id="success-details" class="p-4 mb-4 text-sm rounded-lg bg-green-50">
                        <!-- Success details will be populated here -->
                    </div>
                    <button onclick="redirectToSuccess()"
                        class="w-full px-4 py-3 text-white transition-colors bg-green-600 rounded-lg hover:bg-green-700">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Modal -->
    <div id="error-modal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="w-full max-w-md p-6 bg-white rounded-lg">
                <div class="text-center">
                    <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <h3 class="mb-2 text-xl font-bold text-gray-900">Payment Failed</h3>
                    <p id="error-message" class="mb-4 text-gray-600">Something went wrong with your payment.</p>
                    <div class="space-y-3">
                        <button onclick="tryAgain()"
                            class="w-full px-4 py-3 text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
                            Try Again
                        </button>
                        <button onclick="closeModal('error-modal')"
                            class="w-full px-4 py-3 text-white transition-colors bg-gray-600 rounded-lg hover:bg-gray-700">
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
            button.textContent = `Checking Status... (${remaining} checks remaining)`;
        }

        // Show success modal
        function showSuccessModal(result) {
            const modal = document.getElementById('success-modal');
            const detailsDiv = document.getElementById('success-details');

            detailsDiv.innerHTML = `
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-green-700">Status:</span>
                        <span class="font-semibold text-green-900">Completed</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-green-700">Amount:</span>
                        <span class="font-semibold text-green-900">KES {{ number_format($contribution->amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-green-700">Reference:</span>
                        <span class="font-semibold text-green-900">DON_{{ $contribution->id }}</span>
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
                    <div class="flex items-center justify-center w-8 h-8 mx-auto mb-4 bg-green-100 rounded-full">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <p class="text-lg font-semibold text-green-800">Payment Successful!</p>
                    <p class="mt-2 text-sm text-green-600">Your donation has been processed</p>
                `;
            } else if (status === 'failed') {
                statusDiv.innerHTML = `
                    <div class="flex items-center justify-center w-8 h-8 mx-auto mb-4 bg-red-100 rounded-full">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <p class="text-lg font-semibold text-red-800">Payment Failed</p>
                    <p class="mt-2 text-sm text-red-600">Please try again or contact support</p>
                `;
            } else if (status === 'timeout') {
                statusDiv.innerHTML = `
                    <div class="flex items-center justify-center w-8 h-8 mx-auto mb-4 bg-yellow-100 rounded-full">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <p class="text-lg font-semibold text-yellow-800">Payment Timeout</p>
                    <p class="mt-2 text-sm text-yellow-600">Please check your M-Pesa messages or try again</p>
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

        // Redirect to success page - REMOVED: Now just closes modal and stays on page
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
