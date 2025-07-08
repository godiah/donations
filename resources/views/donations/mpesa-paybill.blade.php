<x-app-layout>

    <style>
        .paybill-details-card {
            background: linear-gradient(135deg, #e8f5e8 0%, #f0f8f0 100%);
            border: 2px solid #28a745;
            border-radius: 15px;
        }

        .payment-info-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: none;
            border-radius: 15px;
        }

        .instruction-step {
            display: flex;
            align-items: flex-start;
            padding: 1rem;
            margin-bottom: 0.5rem;
            background: rgba(40, 167, 69, 0.1);
            border-radius: 10px;
            border-left: 4px solid #28a745;
            transition: all 0.3s ease;
        }

        .instruction-step:hover {
            background: rgba(40, 167, 69, 0.15);
            transform: translateX(5px);
        }

        .instruction-step .step-number {
            background: #28a745;
            color: white;
            border-radius: 50%;
            width: 2rem;
            height: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 1rem;
            flex-shrink: 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .copy-button {
            transition: all 0.3s ease;
        }

        .copy-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .important-note {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            border: 2px solid #ffc107;
            border-radius: 10px;
            padding: 1rem;
            margin: 1rem 0;
        }

        .reference-highlight {
            background: #fff;
            border: 2px dashed #007bff;
            border-radius: 8px;
            padding: 0.5rem;
            font-family: 'Courier New', monospace;
            font-weight: bold;
            color: #007bff;
        }

        .phone-mockup {
            background: #333;
            border-radius: 20px;
            padding: 1rem;
            color: white;
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
            margin: 1rem 0;
        }

        .success-indicator {
            display: none;
            color: #28a745;
            margin-left: 0.5rem;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }

        .pulse-animation {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(40, 167, 69, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(40, 167, 69, 0);
            }
        }
    </style>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow-lg">
                    <div class="card-header text-center bg-success text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-receipt me-2"></i>
                            Complete Your Paybill Payment
                        </h4>
                        <p class="mb-0 mt-2">Follow the instructions below to complete your donation</p>
                    </div>
                    <div class="card-body">

                        <!-- Payment Summary -->
                        <div class="payment-info-card p-4 mb-4 fade-in-up">
                            <div class="row text-center">
                                <div class="col-md-3">
                                    <div class="mb-2">
                                        <i class="fas fa-money-bill-wave fa-3x text-success"></i>
                                    </div>
                                    <h6 class="text-muted">Amount</h6>
                                    <h4 class="text-success font-weight-bold">KES
                                        {{ number_format($contribution->amount, 2) }}</h4>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-2">
                                        <i class="fas fa-hashtag fa-3x text-primary"></i>
                                    </div>
                                    <h6 class="text-muted">Reference</h6>
                                    <h5 class="text-primary font-weight-bold">{{ $instructions['reference'] }}</h5>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-2">
                                        <i class="fas fa-user fa-3x text-info"></i>
                                    </div>
                                    <h6 class="text-muted">Donor</h6>
                                    <h6 class="text-info">{{ $contribution->email }}</h6>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-2">
                                        <i class="fas fa-clock fa-3x text-warning"></i>
                                    </div>
                                    <h6 class="text-muted">Time</h6>
                                    <h6 class="text-warning">{{ $contribution->created_at->format('M d, H:i') }}</h6>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Paybill Details -->
                            <div class="col-lg-6">
                                <div class="paybill-details-card p-4 mb-4 fade-in-up pulse-animation">
                                    <div class="text-center mb-4">
                                        <h5 class="text-success mb-3">
                                            <i class="fas fa-credit-card me-2"></i>
                                            Paybill Payment Details
                                        </h5>
                                    </div>

                                    <div class="row mb-4">
                                        <div class="col-6">
                                            <div class="text-center p-3 bg-white rounded shadow-sm">
                                                <h6 class="text-muted mb-2">Paybill Number</h6>
                                                <h3 class="text-success font-weight-bold mb-2">
                                                    {{ $paybill_details['paybill_number'] }}</h3>
                                                <button class="btn btn-sm btn-outline-success copy-button"
                                                    onclick="copyToClipboard('{{ $paybill_details['paybill_number'] }}', this)">
                                                    <i class="fas fa-copy me-1"></i>Copy
                                                    <span class="success-indicator">
                                                        <i class="fas fa-check"></i>
                                                    </span>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center p-3 bg-white rounded shadow-sm">
                                                <h6 class="text-muted mb-2">Account Number</h6>
                                                <h4 class="text-primary font-weight-bold mb-2">
                                                    {{ $paybill_details['account_number'] }}</h4>
                                                <button class="btn btn-sm btn-outline-primary copy-button"
                                                    onclick="copyToClipboard('{{ $paybill_details['account_number'] }}', this)">
                                                    <i class="fas fa-copy me-1"></i>Copy
                                                    <span class="success-indicator">
                                                        <i class="fas fa-check"></i>
                                                    </span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-center p-3 bg-white rounded shadow-sm mb-3">
                                        <h6 class="text-muted mb-2">Account Name</h6>
                                        <h5 class="text-info font-weight-bold mb-2">
                                            {{ $paybill_details['account_name'] }}</h5>
                                        <button class="btn btn-sm btn-outline-info copy-button"
                                            onclick="copyToClipboard('{{ $paybill_details['account_name'] }}', this)">
                                            <i class="fas fa-copy me-1"></i>Copy
                                            <span class="success-indicator">
                                                <i class="fas fa-check"></i>
                                            </span>
                                        </button>
                                    </div>

                                    <div class="important-note">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                            <strong>IMPORTANT:</strong>
                                        </div>
                                        <p class="mb-0">Please ensure you enter the <strong>exact</strong> account
                                            number and amount to avoid payment delays or failures.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Instructions -->
                            <div class="col-lg-6">
                                <div class="mb-4 fade-in-up">
                                    <h5 class="mb-3">
                                        <i class="fas fa-list-ol me-2 text-primary"></i>
                                        Step-by-Step Payment Instructions
                                    </h5>

                                    @foreach ($instructions['steps'] as $index => $step)
                                        <div class="instruction-step">
                                            <div class="step-number">{{ $index + 1 }}</div>
                                            <div>
                                                <span>{{ $step }}</span>
                                                @if (str_contains($step, 'Business Number'))
                                                    <div class="reference-highlight mt-2">
                                                        {{ $paybill_details['paybill_number'] }}</div>
                                                @elseif(str_contains($step, 'Account Number'))
                                                    <div class="reference-highlight mt-2">
                                                        {{ $paybill_details['account_number'] }}</div>
                                                @elseif(str_contains($step, 'Amount'))
                                                    <div class="reference-highlight mt-2">KES
                                                        {{ number_format($contribution->amount, 2) }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Phone Mockup -->
                                <div class="phone-mockup fade-in-up">
                                    <div class="text-center mb-2">
                                        <i class="fas fa-mobile-alt"></i> M-Pesa Menu Example
                                    </div>
                                    <div>
                                        1. Lipa na M-PESA<br>
                                        2. Pay Bill<br>
                                        3. Enter Business No: <span
                                            class="text-success">{{ $paybill_details['paybill_number'] }}</span><br>
                                        4. Enter Account No: <span
                                            class="text-info">{{ $paybill_details['account_number'] }}</span><br>
                                        5. Enter Amount: <span
                                            class="text-warning">{{ number_format($contribution->amount, 2) }}</span><br>
                                        6. Enter PIN: ****<br>
                                        7. Send
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Important Notes -->
                        <div class="card border-warning mb-4 fade-in-up">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="mb-0">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Important Notes & Requirements
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <ul class="list-unstyled">
                                            @foreach (array_slice($instructions['important_notes'], 0, 2) as $note)
                                                <li class="mb-2">
                                                    <i class="fas fa-check-circle text-success me-2"></i>
                                                    {{ $note }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <ul class="list-unstyled">
                                            @foreach (array_slice($instructions['important_notes'], 2) as $note)
                                                <li class="mb-2">
                                                    <i class="fas fa-check-circle text-success me-2"></i>
                                                    {{ $note }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Verification Process -->
                        <div class="card border-info mb-4 fade-in-up">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="fas fa-shield-alt me-2 text-info"></i>
                                    Payment Verification Process
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 text-center">
                                        <div class="mb-2">
                                            <i class="fas fa-paper-plane fa-2x text-primary"></i>
                                        </div>
                                        <h6>1. Make Payment</h6>
                                        <p class="text-muted small">Complete the M-Pesa paybill payment using the
                                            details above</p>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <div class="mb-2">
                                            <i class="fas fa-search fa-2x text-warning"></i>
                                        </div>
                                        <h6>2. Automatic Verification</h6>
                                        <p class="text-muted small">Our system will automatically verify your payment
                                            within 1-24 hours</p>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <div class="mb-2">
                                            <i class="fas fa-envelope fa-2x text-success"></i>
                                        </div>
                                        <h6>3. Confirmation</h6>
                                        <p class="text-muted small">You'll receive an email confirmation once payment
                                            is verified</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="text-center fade-in-up">
                            <div class="alert alert-success">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Payment Instructions Displayed Successfully!</strong><br>
                                Please complete the payment using the details above. You will receive email confirmation
                                once verified.
                            </div>

                            <div class="btn-group-vertical btn-group-lg" role="group">
                                <button type="button" class="btn btn-success btn-lg mb-2"
                                    onclick="confirmPaymentMade()">
                                    <i class="fas fa-check-circle me-2"></i>
                                    I've Completed the Payment
                                </button>
                                <button type="button" class="btn btn-outline-primary mb-2"
                                    onclick="printInstructions()">
                                    <i class="fas fa-print me-2"></i>
                                    Print Instructions
                                </button>
                                <button type="button" class="btn btn-outline-info mb-2"
                                    onclick="shareInstructions()">
                                    <i class="fas fa-share-alt me-2"></i>
                                    Share Instructions
                                </button>
                            </div>

                            <div class="mt-3">
                                <a href="{{ route('donation.show', $contribution->donationLink->code) }}"
                                    class="btn btn-secondary me-2">
                                    <i class="fas fa-arrow-left me-2"></i>Go Back
                                </a>
                                <a href="/" class="btn btn-outline-secondary">
                                    <i class="fas fa-home me-2"></i>Go Home
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Confirmation Modal -->
    <div class="modal fade" id="paymentConfirmationModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-check-circle me-2"></i>
                        Payment Confirmation
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center py-3">
                        <div class="mb-4">
                            <i class="fas fa-receipt fa-4x text-success"></i>
                        </div>
                        <h5 class="mb-3">Thank You for Your Payment!</h5>
                        <p class="mb-3">We have recorded that you've completed the paybill payment. Our team will
                            verify your payment within 24 hours.</p>

                        <div class="alert alert-info">
                            <strong>Next Steps:</strong>
                            <ul class="list-unstyled mb-0 mt-2">
                                <li><i class="fas fa-clock me-2"></i>Payment verification: 1-24 hours</li>
                                <li><i class="fas fa-envelope me-2"></i>Email confirmation will be sent</li>
                                <li><i class="fas fa-phone me-2"></i>SMS confirmation to {{ $contribution->phone }}
                                </li>
                            </ul>
                        </div>

                        <div class="mt-4">
                            <p class="small text-muted">
                                <strong>Reference Number:</strong> {{ $instructions['reference'] }}<br>
                                <strong>Transaction ID:</strong> {{ $transaction->id ?? 'N/A' }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="goHome()">
                        <i class="fas fa-home me-2"></i>Continue
                    </button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Share Instructions Modal -->
    <div class="modal fade" id="shareModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-share-alt me-2"></i>
                        Share Payment Instructions
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Share these payment instructions:</p>
                    <div class="form-group mb-3">
                        <label for="shareText" class="form-label">Payment Details</label>
                        <textarea id="shareText" class="form-control" rows="8" readonly></textarea>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-primary" onclick="copyShareText()">
                            <i class="fas fa-copy me-2"></i>Copy to Clipboard
                        </button>
                        <button type="button" class="btn btn-success" onclick="shareViaWhatsApp()">
                            <i class="fab fa-whatsapp me-2"></i>Share via WhatsApp
                        </button>
                        <button type="button" class="btn btn-info" onclick="shareViaEmail()">
                            <i class="fas fa-envelope me-2"></i>Share via Email
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Payment details for JavaScript use
        const paymentDetails = {
            paybillNumber: '{{ $paybill_details['paybill_number'] }}',
            accountNumber: '{{ $paybill_details['account_number'] }}',
            accountName: '{{ $paybill_details['account_name'] }}',
            amount: '{{ $contribution->amount }}',
            reference: '{{ $instructions['reference'] }}',
            contributionId: '{{ $contribution->id }}'
        };

        // Copy text to clipboard
        function copyToClipboard(text, button) {
            navigator.clipboard.writeText(text).then(function() {
                // Show success indicator
                const successIndicator = button.querySelector('.success-indicator');
                const icon = button.querySelector('i');

                // Hide copy icon and show success
                icon.style.display = 'none';
                successIndicator.style.display = 'inline';

                // Reset after 2 seconds
                setTimeout(() => {
                    icon.style.display = 'inline';
                    successIndicator.style.display = 'none';
                }, 2000);

                // Show toast notification
                showToast('Copied to clipboard!', 'success');
            }).catch(function(err) {
                console.error('Could not copy text: ', err);
                showToast('Failed to copy. Please copy manually.', 'error');
            });
        }

        // Show toast notification
        function showToast(message, type = 'info') {
            // Create toast element
            const toast = document.createElement('div');
            toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} position-fixed`;
            toast.style.cssText = `
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 250px;
        animation: slideInRight 0.3s ease;
    `;
            toast.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check' : 'exclamation'}-circle me-2"></i>
        ${message}
    `;

            document.body.appendChild(toast);

            // Remove after 3 seconds
            setTimeout(() => {
                toast.style.animation = 'slideOutRight 0.3s ease';
                setTimeout(() => {
                    document.body.removeChild(toast);
                }, 300);
            }, 3000);
        }

        // Confirm payment made
        function confirmPaymentMade() {
            $('#paymentConfirmationModal').modal('show');

            // Optional: Send confirmation to backend
            fetch('{{ route('donation.confirm-paybill', $contribution->id ?? 0) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    contribution_id: paymentDetails.contributionId,
                    payment_confirmed: true,
                    confirmation_time: new Date().toISOString()
                })
            }).catch(error => {
                console.log('Optional confirmation logging failed:', error);
            });
        }

        // Print instructions
        function printInstructions() {
            const printContent = `
        <html>
        <head>
            <title>M-Pesa Paybill Payment Instructions</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; border-bottom: 2px solid #28a745; padding-bottom: 10px; margin-bottom: 20px; }
                .details { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0; }
                .steps { margin: 20px 0; }
                .step { margin: 10px 0; padding: 10px; border-left: 3px solid #28a745; }
                .important { background: #fff3cd; padding: 10px; border: 1px solid #ffc107; border-radius: 5px; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>M-Pesa Paybill Payment Instructions</h1>
                <p>Please follow these instructions to complete your donation</p>
            </div>
            
            <div class="details">
                <h3>Payment Details</h3>
                <p><strong>Paybill Number:</strong> ${paymentDetails.paybillNumber}</p>
                <p><strong>Account Number:</strong> ${paymentDetails.accountNumber}</p>
                <p><strong>Account Name:</strong> ${paymentDetails.accountName}</p>
                <p><strong>Amount:</strong> KES ${parseFloat(paymentDetails.amount).toFixed(2)}</p>
                <p><strong>Reference:</strong> ${paymentDetails.reference}</p>
            </div>
            
            <div class="steps">
                <h3>Step-by-Step Instructions</h3>
                ${Array.from(document.querySelectorAll('.instruction-step')).map((step, index) => 
                    `<div class="step">${index + 1}. ${step.querySelector('span').textContent}</div>`
                ).join('')}
            </div>
            
            <div class="important">
                <h4>Important Notes:</h4>
                <ul>
                    <li>Please enter the exact account number and amount</li>
                    <li>Payment verification may take up to 24 hours</li>
                    <li>Keep your M-Pesa confirmation message</li>
                    <li>You will receive email confirmation once verified</li>
                </ul>
            </div>
            
            <p style="text-align: center; margin-top: 30px; font-size: 12px; color: #666;">
                Printed on ${new Date().toLocaleDateString()} at ${new Date().toLocaleTimeString()}
            </p>
        </body>
        </html>
    `;

            const printWindow = window.open('', '_blank');
            printWindow.document.write(printContent);
            printWindow.document.close();
            printWindow.print();
        }

        // Share instructions
        function shareInstructions() {
            const shareText = `M-Pesa Paybill Payment Instructions

Payment Details:
• Paybill Number: ${paymentDetails.paybillNumber}
• Account Number: ${paymentDetails.accountNumber}
• Account Name: ${paymentDetails.accountName}
• Amount: KES ${parseFloat(paymentDetails.amount).toFixed(2)}
• Reference: ${paymentDetails.reference}

Steps:
1. Go to M-Pesa menu on your phone
2. Select "Lipa na M-Pesa"
3. Select "Pay Bill"
4. Enter Business Number: ${paymentDetails.paybillNumber}
5. Enter Account Number: ${paymentDetails.accountNumber}
6. Enter Amount: KES ${parseFloat(paymentDetails.amount).toFixed(2)}
7. Enter your M-Pesa PIN
8. Confirm payment details
9. Wait for confirmation SMS

IMPORTANT: Please enter the exact account number and amount to avoid delays.`;

            document.getElementById('shareText').value = shareText;
            $('#shareModal').modal('show');
        }

        // Copy share text
        function copyShareText() {
            const shareText = document.getElementById('shareText');
            shareText.select();
            document.execCommand('copy');
            showToast('Instructions copied to clipboard!', 'success');
        }

        // Share via WhatsApp
        function shareViaWhatsApp() {
            const text = encodeURIComponent(document.getElementById('shareText').value);
            window.open(`https://wa.me/?text=${text}`, '_blank');
        }

        // Share via Email
        function shareViaEmail() {
            const subject = encodeURIComponent('M-Pesa Paybill Payment Instructions');
            const body = encodeURIComponent(document.getElementById('shareText').value);
            window.location.href = `mailto:?subject=${subject}&body=${body}`;
        }

        // Go to home page
        function goHome() {
            window.location.href = '/';
        }

        // Add CSS animations
        const style = document.createElement('style');
        style.textContent = `
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
        document.head.appendChild(style);

        // Initialize fade-in animations
        document.addEventListener('DOMContentLoaded', function() {
            const elements = document.querySelectorAll('.fade-in-up');
            elements.forEach((el, index) => {
                setTimeout(() => {
                    el.style.opacity = '1';
                    el.style.transform = 'translateY(0)';
                }, index * 200);
            });
        });
    </script>
</x-app-layout>
