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
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Invitation routes
Route::get('/invitation/{token}/register', [InvitationController::class, 'showRegistrationForm'])->name('invitation.register');
Route::post('/invitation/{token}/register', [InvitationController::class, 'register'])->name('invitation.register.submit');

// Donation routes
Route::prefix('donate')->group(function () {
    Route::get('/{code}', [DonationController::class, 'show'])->name('donation.show');
    Route::post('/{code}', [DonationController::class, 'process'])->name('donation.process');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('verified')->name('dashboard');

    // Individual applications
    Route::prefix('individual/application')->name('individual.')->group(function () {
        Route::get('/', [DashboardController::class, 'showIndividual'])->name('application');
        Route::post('/store', [IndividualDonationController::class, 'store'])->name('store');
        Route::get('/success/{application}', [IndividualDonationController::class, 'success'])->name('success');

        Route::get('/{application_number}', [ApplicationController::class, 'showIndividual'])
            ->name('applications.show');

        // API route for getting document types
        Route::get('document-types/{contributionReason}', [IndividualDonationController::class, 'getDocumentTypes'])
            ->name('document-types');
    });

    // Company applications
    Route::prefix('company/application')->name('company.')->group(function () {
        Route::get('/', [DashboardController::class, 'showCompany'])->name('application');
        Route::post('/store', [CompanyDonationController::class, 'store'])->name('store');
        Route::get('/success/{application}', [CompanyDonationController::class, 'success'])->name('success');

        Route::get('/{application_number}', [ApplicationController::class, 'showCompany'])
            ->name('applications.show');


        // API route for getting document types
        Route::get('document-types/{contributionReason}', [CompanyDonationController::class, 'getDocumentTypes'])
            ->name('document-types');
    });

    // All active applications (submitted/under review)
    Route::get('/active', [ApplicationController::class, 'active'])->name('active');

    // Pending/problem applications (rejected, cancelled, additional info)
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
