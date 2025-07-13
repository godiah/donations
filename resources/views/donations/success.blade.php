@extends('layouts.app')

@section('title', 'Donation Successful')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border-success">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-check-circle"></i> Donation Successful
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-success">
                            <strong>Thank you!</strong> Your donation has been processed successfully.
                        </div>

                        @if ($contribution)
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Donation Details:</h6>
                                    <ul class="list-unstyled">
                                        <li><strong>Amount:</strong> {{ $contribution->currency }}
                                            {{ number_format($contribution->amount, 2) }}</li>
                                        <li><strong>Transaction ID:</strong>
                                            {{ $contribution->cybersource_transaction_id ?? $contribution->id }}</li>
                                        <li><strong>Date:</strong>
                                            {{ $contribution->processed_at ? $contribution->processed_at->format('M j, Y g:i A') : 'Processing...' }}
                                        </li>
                                        @if ($contribution->cybersource_auth_code)
                                            <li><strong>Authorization Code:</strong>
                                                {{ $contribution->cybersource_auth_code }}</li>
                                        @endif
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6>Donor Information:</h6>
                                    <ul class="list-unstyled">
                                        <li><strong>Email:</strong> {{ $contribution->email }}</li>
                                        @if ($contribution->billing_name)
                                            <li><strong>Name:</strong> {{ $contribution->billing_name }}</li>
                                        @endif
                                        @if ($contribution->phone)
                                            <li><strong>Phone:</strong> {{ $contribution->phone }}</li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        @endif

                        <div class="mt-4">
                            <p class="text-muted">
                                <i class="fas fa-envelope"></i>
                                A confirmation email will be sent to your email address shortly.
                            </p>
                        </div>

                        <div class="mt-4 text-center">
                            <a href="{{ route('donation.show', $donationLink->code) }}" class="btn btn-primary">
                                Make Another Donation
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
