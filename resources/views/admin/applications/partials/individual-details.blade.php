@props(['individual', 'application'])

@php
    $kycVerification = $individual->getLatestKycVerification($application);
    $kycStatus = $individual->getKycStatus($application);
@endphp

<div class="grid grid-cols-1 gap-6 md:grid-cols-2">
    <div>
        <dt class="text-sm font-medium text-gray-500">Full Name</dt>
        <dd class="flex items-center mt-1 text-sm text-gray-900">
            {{ $individual->getFullNameAttribute() }}
            @if ($kycVerification && $kycVerification->isVerified())
                <svg class="w-4 h-4 ml-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd"></path>
                </svg>
            @endif
        </dd>
    </div>
    <div>
        <dt class="text-sm font-medium text-gray-500">Email</dt>
        <dd class="mt-1 text-sm text-gray-900">{{ $individual->email }}</dd>
    </div>
    <div>
        <dt class="text-sm font-medium text-gray-500">Phone</dt>
        <dd class="mt-1 text-sm text-gray-900">{{ $individual->phone }}</dd>
    </div>
    <div>
        <dt class="text-sm font-medium text-gray-500">ID Type</dt>
        <dd class="mt-1 text-sm text-gray-900">{{ $individual->idType->display_name ?? 'N/A' }}</dd>
    </div>
    <div>
        <dt class="text-sm font-medium text-gray-500">ID Number</dt>
        <dd class="flex items-center justify-between mt-1 text-sm text-gray-900">
            <span>{{ $individual->id_number }}</span>
            <div class="flex items-center space-x-2">
                @if ($kycVerification)
                    <span
                        class="kyc-status-badge inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $kycVerification->getStatusBadgeClass() }}">
                        {{ $kycVerification->getStatusText() }}
                    </span>
                @endif

                <div class="kyc-button-container" data-application-id="{{ $application->id }}">
                    @if (!$kycVerification || $kycVerification->isRejected() || $kycVerification->hasFailed())
                        <button type="button"
                            class="kyc-verify-btn inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                            data-application-id="{{ $application->id }}">
                            <svg class="mr-1.5 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Verify ID
                        </button>
                    @elseif($kycVerification->isPending() || $kycVerification->isProcessing())
                        <button type="button"
                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-100 rounded-md cursor-not-allowed"
                            disabled>
                            <svg class="animate-spin mr-1.5 h-3 w-3" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Verifying...
                        </button>
                    @elseif($kycVerification->isVerified())
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <svg class="mr-1.5 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            Verified
                        </span>
                    @endif
                </div>
            </div>
        </dd>
    </div>
    <div>
        <dt class="text-sm font-medium text-gray-500">KRA PIN</dt>
        <dd class="mt-1 text-sm text-gray-900">{{ $individual->kra_pin ?? 'Not provided' }}</dd>
    </div>
    <div>
        <dt class="text-sm font-medium text-gray-500">Amount Raised</dt>
        <dd class="mt-1 text-sm text-gray-900">
            KES {{ number_format($individual->amount_raised ?? 0, 2) }}
            @if ($individual->fees_charged)
                <div class="text-xs text-gray-500">Fees: KES {{ number_format($individual->fees_charged, 2) }}</div>
            @endif
        </dd>
    </div>
</div>

@if ($kycVerification)
    <!-- KYC Verification Details -->
    <div class="pt-6 mt-6 border-t border-gray-200 kyc-details-section">
        <h4 class="mb-4 text-lg font-medium text-gray-900">KYC Verification Details</h4>

        <div class="p-4 rounded-lg bg-gray-50">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Verification Status</dt>
                    <dd class="mt-1">
                        <span
                            class="kyc-status-badge inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium {{ $kycVerification->getStatusBadgeClass() }}">
                            {{ $kycVerification->getStatusText() }}
                        </span>
                    </dd>
                </div>

                @if ($kycVerification->result_text)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Match Result</dt>
                        <dd class="mt-1 text-sm text-gray-900 match-result">{{ $kycVerification->result_text }}</dd>
                    </div>
                @endif

                <div>
                    <dt class="text-sm font-medium text-gray-500">Initiated</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $kycVerification->submitted_at?->format('M j, Y g:i A') ?? 'N/A' }}</dd>
                </div>

                @if ($kycVerification->completed_at)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Completed</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $kycVerification->completed_at->format('M j, Y g:i A') }}</dd>
                    </div>
                @endif

                @if ($kycVerification->smile_job_id)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Smile Job ID</dt>
                        <dd class="mt-1 font-mono text-sm text-gray-900">{{ $kycVerification->smile_job_id }}</dd>
                    </div>
                @endif
            </div>

            @if ($kycVerification->isVerified() || $kycVerification->isRejected())
                @php $resultSummary = $kycVerification->getResultSummary(); @endphp
                @if (!empty($resultSummary))
                    <div class="pt-4 mt-4 border-t border-gray-200">
                        <h5 class="mb-3 text-sm font-medium text-gray-900">Verification Results</h5>
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            @foreach ($resultSummary as $field => $result)
                                @if ($result !== 'N/A')
                                    <div class="flex items-center justify-between">
                                        <span
                                            class="text-sm text-gray-600">{{ ucwords(str_replace('_', ' ', $field)) }}:</span>
                                        <span
                                            class="text-sm font-medium 
                                    @if ($result === 'Exact Match') text-green-600
                                    @elseif($result === 'Partial Match') text-yellow-600  
                                    @elseif($result === 'No Match') text-red-600
                                    @elseif($result === 'Verified') text-green-600
                                    @elseif($result === 'Not Verified') text-red-600
                                    @else text-gray-600 @endif
                                ">
                                            {{ $result }}
                                        </span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif

            @if ($kycVerification->failure_reason)
                <div class="pt-4 mt-4 border-t border-gray-200">
                    <h5 class="mb-2 text-sm font-medium text-red-900">Failure Reason</h5>
                    <p class="p-3 text-sm text-red-700 rounded-md bg-red-50">{{ $kycVerification->failure_reason }}</p>
                </div>
            @endif
        </div>
    </div>
@endif


@if ($individual->additional_info)
    <div class="mt-6">
        <dt class="text-sm font-medium text-gray-500">Additional Information</dt>
        <dd class="p-3 mt-1 text-sm text-gray-900 rounded bg-gray-50">
            @if (is_array($individual->additional_info))
                @foreach ($individual->additional_info as $key => $value)
                    <div class="mb-2">
                        <strong>{{ ucwords(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}
                    </div>
                @endforeach
            @else
                {{ $individual->additional_info }}
            @endif
        </dd>
    </div>
@endif

<!-- KYC Verification JavaScript -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const verifyButtons = document.querySelectorAll('.kyc-verify-btn');
        let pollingInterval = null;

        attachVerifyButtonListeners();

        function attachVerifyButtonListeners() {
            const buttons = document.querySelectorAll('.kyc-verify-btn:not([data-listener-attached])');
            buttons.forEach(button => {
                button.setAttribute('data-listener-attached', 'true');
                button.addEventListener('click', function() {
                    const applicationId = this.dataset.applicationId;
                    initiateVerification(applicationId, this);
                });
            });
        }

        function initiateVerification(applicationId, button) {
            // Disable button and show loading state
            button.disabled = true;
            button.innerHTML = `
                <svg class="animate-spin mr-1.5 h-3 w-3" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Initiating...
            `;

            // Make API call to initiate verification
            fetch(`/admin/applications/${applicationId}/kyc/initiate`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw new Error(data.message || 'Failed to initiate verification');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        startPollingForStatus(applicationId);
                        button.innerHTML = `
                        <svg class="animate-spin mr-1.5 h-3 w-3" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Verifying...
                    `;
                    } else {
                        showNotification(data.message || 'Failed to initiate verification', 'failed');
                        resetButtonState(button);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification(error.message || 'An error occurred while initiating verification',
                        'failed');
                    resetButtonState(button);
                });
        }

        function startPollingForStatus(applicationId) {
            if (pollingInterval) {
                clearInterval(pollingInterval);
            }

            pollingInterval = setInterval(() => {
                checkVerificationStatus(applicationId);
            }, 3000);

            // Timeout after 5 minutes
            setTimeout(() => {
                if (pollingInterval) {
                    clearInterval(pollingInterval);
                    pollingInterval = null;
                    console.warn('Polling stopped after 5 minutes');
                    showNotification('Verification timed out', 'failed');
                    const buttonContainer = document.querySelector(
                        `.kyc-button-container[data-application-id="${applicationId}"]`);
                    if (buttonContainer) {
                        resetButtonContainerToVerifyState(buttonContainer, applicationId);
                    }
                }
            }, 300000);
        }

        function checkVerificationStatus(applicationId) {
            fetch(`/admin/applications/${applicationId}/kyc/status`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        if (response.status === 400 || response.status === 404) {
                            return response.json().then(data => {
                                showNotification(data.message ||
                                    'KYC verification is not available for this application',
                                    'failed');
                                clearInterval(pollingInterval);
                                pollingInterval = null;
                                const buttonContainer = document.querySelector(
                                    `.kyc-button-container[data-application-id="${applicationId}"]`
                                    );
                                if (buttonContainer) {
                                    resetButtonContainerToVerifyState(buttonContainer,
                                        applicationId);
                                }
                                throw new Error(data.message || 'Invalid applicant');
                            });
                        }
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success && data.verification) {
                        const status = data.verification.status;
                        if (status !== 'processing' && status !== 'pending') {
                            clearInterval(pollingInterval);
                            pollingInterval = null;
                            updateKycUI(data.verification);
                        }
                    } else {
                        showNotification(data.message || 'Failed to check verification status', 'failed');
                        clearInterval(pollingInterval);
                        pollingInterval = null;
                        const buttonContainer = document.querySelector(
                            `.kyc-button-container[data-application-id="${applicationId}"]`);
                        if (buttonContainer) {
                            resetButtonContainerToVerifyState(buttonContainer, applicationId);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error checking verification status:', error);
                    showNotification('Error checking verification status', 'failed');
                    clearInterval(pollingInterval);
                    pollingInterval = null;
                    const buttonContainer = document.querySelector(
                        `.kyc-button-container[data-application-id="${applicationId}"]`);
                    if (buttonContainer) {
                        resetButtonContainerToVerifyState(buttonContainer, applicationId);
                    }
                });
        }

        function updateKycUI(verification) {
            // Update all status badges
            const statusBadges = document.querySelectorAll('.kyc-status-badge');
            statusBadges.forEach(badge => {
                badge.textContent = getStatusText(verification.status);
                badge.className =
                    `kyc-status-badge inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getStatusBadgeClass(verification.status)}`;
            });

            // Update button container based on status
            const buttonContainer = document.querySelector(
                `.kyc-button-container[data-application-id="${verification.application_id}"]`);
            if (buttonContainer) {
                if (verification.status === 'verified') {
                    buttonContainer.innerHTML = `
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <svg class="mr-1.5 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Verified
                        </span>
                    `;
                } else if (verification.status === 'rejected' || verification.status === 'failed') {
                    resetButtonContainerToVerifyState(buttonContainer, verification.application_id);
                }
            }

            // Update KYC details section if it exists
            updateKycDetailsSection(verification);

            // Show notification
            showNotification(
                `KYC verification ${getStatusText(verification.status).toLowerCase()}`,
                verification.status,
                verification.failure_reason
            );
        }

        function resetButtonContainerToVerifyState(buttonContainer, applicationId) {
            buttonContainer.innerHTML = `
                <button type="button"
                    class="kyc-verify-btn inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                    data-application-id="${applicationId}">
                    <svg class="mr-1.5 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Verify ID
                </button>
            `;
            // Re-attach event listeners to new button
            attachVerifyButtonListeners();
        }

        function updateKycDetailsSection(verification) {
            const detailsSection = document.querySelector('.kyc-details-section');
            if (detailsSection && verification.result_text) {
                const matchResultElement = detailsSection.querySelector('.match-result');
                if (matchResultElement) {
                    matchResultElement.textContent = verification.result_text;
                }
            }
        }

        function showNotification(message, type, failureReason = null) {
            Swal.fire({
                title: type === 'verified' ? 'Success' : (type === 'rejected' || type === 'failed') ?
                    'Error' : 'Info',
                text: failureReason ? `${message}: ${failureReason}` : message,
                icon: type === 'verified' ? 'success' : (type === 'rejected' || type === 'failed') ?
                    'error' : 'info',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });
        }

        function getStatusText(status) {
            const statusMap = {
                'verified': 'Verified',
                'rejected': 'Rejected',
                'processing': 'Processing',
                'failed': 'Failed',
                'pending': 'Pending'
            };
            return statusMap[status] || 'Pending';
        }

        function getStatusBadgeClass(status) {
            const classMap = {
                'verified': 'bg-green-100 text-green-800',
                'rejected': 'bg-red-100 text-red-800',
                'processing': 'bg-blue-100 text-blue-800',
                'failed': 'bg-red-100 text-red-800',
                'pending': 'bg-blue-100 text-blue-800'
            };
            return classMap[status] || 'bg-yellow-100 text-yellow-800';
        }

        function resetButtonState(button) {
            if (button) {
                button.disabled = false;
                button.innerHTML = `
                    <svg class="mr-1.5 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Verify ID
                `;
            }
        }
    });
</script>
