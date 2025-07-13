@extends('layouts.app')

@section('title', 'Donation Failed')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-times-circle"></i> Donation Failed
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-danger">
                            <strong>Payment Failed</strong> We were unable to process your donation.
                        </div>

                        @if ($contribution && $contribution->cybersource_reason_code)
                            <div class="mt-3">
                                <h6>Error Details:</h6>
                                <p class="text-muted">
                                    <strong>Reference:</strong> {{ $contribution->id }}<br>
                                    @if ($contribution->cybersource_reason_code)
                                        <strong>Reason Code:</strong> {{ $contribution->cybersource_reason_code }}
                                    @endif
                                </p>
                            </div>
                        @endif

                        <div class="mt-3">
                            <h6>Common reasons for payment failure:</h6>
                            <ul>
                                <li>Insufficient funds in your account</li>
                                <li>Credit card expired or blocked</li>
                                <li>Incorrect billing information</li>
                                <li>Bank security restrictions</li>
                            </ul>
                        </div>

                        <div class="mt-4">
                            <p>
                                <strong>What you can do:</strong>
                            </p>
                            <ul>
                                <li>Check your payment information and try again</li>
                                <li>Contact your bank to ensure the transaction is authorized</li>
                                <li>Try using a different payment method</li>
                                <li>Contact us if you continue to experience issues</li>
                            </ul>
                        </div>

                        <div class="mt-4 text-center">
                            <a href="{{ route('donation.show', $donationLink->code) }}" class="btn btn-primary">
                                Try Again
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
