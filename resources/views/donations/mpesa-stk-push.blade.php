@extends('layouts.donation')

@section('title', 'M-Pesa Payment - Complete Your Transaction')

@section('content')
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-md mx-auto">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <!-- Header -->
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 18l9-9-9-9-3 3 6 6-6 6 3 3z" />
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900">M-Pesa Payment</h1>
                    <p class="text-gray-600 mt-2">Complete your donation payment</p>
                </div>

                <!-- Payment Details -->
                <div class="bg-green-50 rounded-lg p-4 mb-6">
                    <h3 class="font-semibold text-green-800 mb-3">Payment Details</h3>
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
                            class="animate-spin w-8 h-8 border-4 border-blue-500 border-t-transparent rounded-full mx-auto mb-4">
                        </div>
                        <p class="text-lg font-semibold text-gray-800">Processing Payment...</p>
                        <p class="text-sm text-gray-600 mt-2">Please check your phone for the M-Pesa prompt</p>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="bg-blue-50 rounded-lg p-4 mb-6">
                    <h3 class="font-semibold text-blue-800 mb-3">Instructions</h3>
                    <ol class="text-sm text-blue-700 space-y-2">
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
                        class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        Check Payment Status
                    </button>

                    <button onclick="window.location.reload()"
                        class="w-full bg-gray-600 text-white py-3 px-4 rounded-lg hover:bg-gray-700 transition-colors">
                        Refresh Page
                    </button>
                </div>

                <!-- Help Section -->
                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                    <h4 class="font-semibold text-gray-800 mb-2">Need Help?</h4>
                    <p class="text-sm text-gray-600">
                        If you don't receive the M-Pesa prompt or encounter any issues, please contact our support team.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="success-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg max-w-md w-full p-6">
                <div class="text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Payment Successful!</h3>
                    <p class="text-gray-600 mb-4">Your donation has been processed successfully.</p>
                    <div id="success-details" class="bg-green-50 rounded-lg p-4 mb-4 text-sm">
                        <!-- Success details will be populated here -->
                    </div>
                    <button onclick="redirectToSuccess()"
                        class="w-full bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 transition-colors">
                        Continue
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Modal -->
    <div id="error-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg max-w-md w-full p-6">
                <div class="text-center">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Payment Failed</h3>
                    <p id="error-message" class="text-gray-600 mb-4">Something went wrong with your payment.</p>
                    <div class="space-y-3">
                        <button onclick="tryAgain()"
                            class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                            Try Again
                        </button>
                        <button onclick="closeModal('error-modal')"
                            class="w-full bg-gray-600 text-white py-3 px-4 rounded-lg hover:bg-gray-700 transition-colors">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

<script>
    // Global variables
    const checkoutRequestId = '{{ $checkout_request_id }}';
    const contributionId = '{{ $contribution->id }}';
    let statusCheckInterval;
    let statusCheckCount = 0;
    const maxStatusChecks = 60; // 5 minutes with 5-second intervals

    // Start automatic status checking
    document.addEventListener('DOMContentLoaded', function() {
        startStatusChecking();
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

            if (data.success && data.data) {
                const result = data.data;

                // Check if payment is complete
                if (result.ResultCode === '0' || result.ResultCode === 0) {
                    stopStatusChecking();
                    showSuccessModal(result);
                } else if (result.ResultCode && result.ResultCode !== '500.001.1001') {
                    // Payment failed
                    stopStatusChecking();
                    showErrorModal(result.ResultDesc || 'Payment failed');
                }
                // If ResultCode is '500.001.1001', payment is still processing, continue checking
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

        messageElement.textContent = message;
        modal.classList.remove('hidden');
        updatePaymentStatus('failed');
    }

    // Show timeout message
    function showTimeoutMessage() {
        updatePaymentStatus('timeout');
    }

    // Update payment status display
    function updatePaymentStatus(status) {
        const statusDiv = document.getElementById('payment-status');

        if (status === 'success') {
            statusDiv.innerHTML = `
            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <p class="text-lg font-semibold text-green-800">Payment Successful!</p>
            <p class="text-sm text-green-600 mt-2">Your donation has been processed</p>
        `;
        } else if (status === 'failed') {
            statusDiv.innerHTML = `
            <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <p class="text-lg font-semibold text-red-800">Payment Failed</p>
            <p class="text-sm text-red-600 mt-2">Please try again or contact support</p>
        `;
        } else if (status === 'timeout') {
            statusDiv.innerHTML = `
            <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
            </div>
            <p class="text-lg font-semibold text-yellow-800">Status Check Timeout</p>
            <p class="text-sm text-yellow-600 mt-2">Please check your M-Pesa messages or contact support</p>
        `;
        }
    }

    // Close modal
    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.classList.add('hidden');
    }

    // Try again function
    function tryAgain() {
        window.location.reload();
    }

    // Redirect to success page
    function redirectToSuccess() {
        // Redirect to success page or back to donation form
        window.location.href = '/donate/success;
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
