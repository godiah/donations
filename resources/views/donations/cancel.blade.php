@extends('layouts.app')

@section('title', 'Donation Cancelled')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h4 class="mb-0">
                            <i class="fas fa-exclamation-triangle"></i> Donation Cancelled
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <strong>Payment Cancelled</strong> Your donation was cancelled and no charges were made.
                        </div>

                        <p>You chose to cancel your donation. No payment has been processed.</p>

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
