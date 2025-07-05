<?php

use App\Http\Controllers\Admin\ApplicationController as AdminApplicationController;
use App\Http\Controllers\Admin\KycController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\CompanyDonationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\IndividualDonationController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\PayoutMethodController;
use App\Http\Controllers\ProfileController;
use App\Services\CyberSourceService;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-cybersource', function () {
    $service = new CyberSourceService();
    return $service->testCredentials();
});

Route::get('/compute-signature', function () {
    $dataString = "access_key=ad01490521e53168ba4538f196840243,profile_id=00EAC2E6-F58C-4C1B-824E-A134DFF42EC8,transaction_uuid=REQ_1751624850_QwDwwOzk0z,signed_date_time=2025-07-04T10:27:30Z,locale=en,transaction_type=sale,reference_number=29,amount=100.00,currency=KES,payment_method=card,bill_to_forename=Donor,bill_to_email=mosesgodiah@gmail.com,override_custom_receipt_page=https://9030-102-213-251-139.ngrok-free.app/donate/success,override_custom_cancel_page=https://9030-102-213-251-139.ngrok-free.app/donate/cancel,override_custom_error_page=https://9030-102-213-251-139.ngrok-free.app/donate/error,bill_to_phone=+254745548093";
    $secretKey = hex2bin('ab7e7228626745bd9a6f11d0842d7c8f17518d0de0504aaab9dcc0cb44604f011ea1a5a4344a40b3909733007ed41b63abaef30c56664e548c61e542b80e6188088fed2f33714a7083ccb42e91b795cd45a6534c5e264efa932521a763f7a773979d7376bbb64edd9fa256f7c264d9a04e839fd008c340a5b4506573bada9bb6');
    $signature = base64_encode(hash_hmac('sha256', $dataString, $secretKey, true));

    return response()->json(['signature' => $signature]);
});

// Invitation routes
Route::get('/invitation/{token}/register', [InvitationController::class, 'showRegistrationForm'])->name('invitation.register');
Route::post('/invitation/{token}/register', [InvitationController::class, 'register'])->name('invitation.register.submit');

// Donation routes
Route::prefix('donate')->group(function () {
    // Process donation form
    Route::get('/{code}', [DonationController::class, 'show'])->name('donation.show');
    Route::post('/{code}', [DonationController::class, 'process'])->name('donation.process');

    // CyberSource return URLs
    Route::get('/success', [DonationController::class, 'success'])->name('donation.success');
    Route::get('/cancel', [DonationController::class, 'cancel'])->name('donation.cancel');
    Route::get('/error', [DonationController::class, 'error'])->name('donation.error');

    // CyberSource webhook (should be POST)
    Route::post('/webhook/cybersource', [DonationController::class, 'webhook'])->name('donation.webhook.cybersource');
})->withoutMiddleware(['auth', 'verified', 'csrf']);

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('verified')->name('dashboard');

    // Individual applications
    Route::prefix('individual/application')->name('individual.')->group(function () {
        Route::get('/', [DashboardController::class, 'showIndividual'])->name('application');
        Route::post('/store', [IndividualDonationController::class, 'store'])->name('store');
        Route::get('/success/{application}', [IndividualDonationController::class, 'success'])->name('success');
        Route::get('/{application_number}', [ApplicationController::class, 'showIndividual'])->name('applications.show');
        Route::get('/{application_number}/update', [ApplicationController::class, 'updateIndividual'])->name('applications.update');
        Route::patch('/{application_number}/update', [ApplicationController::class, 'updateIndividualStore'])->name('applications.update.store');

        // API route for getting document types
        Route::get('document-types/{contributionReason}', [IndividualDonationController::class, 'getDocumentTypes'])->name('document-types');
    });

    // Company applications
    Route::prefix('company/application')->name('company.')->group(function () {
        Route::get('/', [DashboardController::class, 'showCompany'])->name('application');
        Route::post('/store', [CompanyDonationController::class, 'store'])->name('store');
        Route::get('/success/{application}', [CompanyDonationController::class, 'success'])->name('success');
        Route::get('/{application_number}', [ApplicationController::class, 'showCompany'])->name('applications.show');
        Route::get('/{application_number}/update', [ApplicationController::class, 'updateCompany'])->name('applications.update');
        Route::patch('/{application_number}/update', [ApplicationController::class, 'updateCompanyStore'])->name('applications.update.store');

        // API route for getting document types
        Route::get('document-types/{contributionReason}', [CompanyDonationController::class, 'getDocumentTypes'])
            ->name('document-types');
    });

    // All active applications (submitted/under review)
    Route::get('/active', [ApplicationController::class, 'active'])->name('active');

    // Pending/problem applications (rejected, resubmitted, additional info)
    Route::get('/pending', [ApplicationController::class, 'pending'])->name('pending');

    // My donations (approved applications)
    Route::get('/donations', [ApplicationController::class, 'donations'])->name('donations');
    Route::get('/donations/{applicationNumber}', [DonationController::class, 'showDonation'])->name('donations.show');
    // Payout methods
    Route::get('/payout-methods', [PayoutMethodController::class, 'index'])->name('payout-methods.index');
    Route::get('/payout-methods/create', [PayoutMethodController::class, 'create'])->name('payout-methods.create');
    Route::post('/payout-methods', [PayoutMethodController::class, 'store'])->name('payout-methods.store');
    Route::patch('/payout-methods/{id}/set-primary', [PayoutMethodController::class, 'setPrimary'])->name('payout-methods.set-primary');
    Route::delete('/payout-methods/{id}', [PayoutMethodController::class, 'destroy'])->name('payout-methods.destroy');

    // pending downloads
    Route::get('/company/applications/{application}/download/{file}', [CompanyDonationController::class, 'download'])
        ->name('company.download');
    Route::get('/company/applications/{application}/download-support/{document}', [CompanyDonationController::class, 'downloadSupport'])
        ->name('company.download.support');


    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /**
     * Administrator Routes  
     * Admin middleware   
     */
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::prefix('/applications')->name('applications.')->group(function () {
            Route::get('/', [AdminApplicationController::class, 'index'])->name('index');
            Route::get('/{application}', [AdminApplicationController::class, 'show'])->name('show');
            Route::patch('/{application}/update-status', [AdminApplicationController::class, 'updateStatus'])->name('update-status');
            Route::get('/type/{type}', [AdminApplicationController::class, 'filterByType'])->name('filter.type');
            Route::get('/status/{status}', [AdminApplicationController::class, 'filterByStatus'])->name('filter.status');
            Route::get('/type/{type}/status/{status}', [AdminApplicationController::class, 'filterByTypeAndStatus'])->name('filter.type_status');
            Route::post('/{application}/start-review', [AdminApplicationController::class, 'startReview'])->name('start-review');
            Route::post('/documents/{document}/update-status', [AdminApplicationController::class, 'updateDocumentStatus'])->name('document.update-status');
            Route::get('/documents/{document}/serve', [AdminApplicationController::class, 'serveDocument'])->name('document.serve');
            Route::get('/companies/{company}/documents/{field}/serve', [AdminApplicationController::class, 'serveCompanyDocument'])->name('company.document-serve');

            // Approve donation application
            Route::post('/{application}/approve', [AdminApplicationController::class, 'approve'])->name('approve');

            // KYC verification
            Route::post('{application}/kyc/initiate', [KycController::class, 'initiateVerification'])->name('kyc.initiate');
            Route::get('{application}/kyc/status', [KycController::class, 'getVerificationStatus'])->name('kyc.status');
            Route::get('kyc/verification/{verification}', [KycController::class, 'showVerification'])->name('kyc.verification.show');
        });

        Route::get('/donation-links', [DonationController::class, 'index'])->name('donation-links.index');
        Route::get('/donation-links/{donationLink}', [DonationController::class, 'showLink'])->name('donation-links.show');
        Route::patch('/donation-links/{donationLink}/toggle-status', [DonationController::class, 'toggleStatus'])->name('donation-links.toggle-status');

        Route::post('/payout-mandates/{payoutMandate}/invitations', [InvitationController::class, 'createAndSendInvitation'])->name('invitations.create');
    });
});

// Public webhook endpoint for Smile Identity callbacks
Route::post('/api/kyc/callback', [KycController::class, 'handleCallback'])->name('kyc.callback');

require __DIR__ . '/auth.php';
