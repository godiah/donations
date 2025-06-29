<?php

use App\Http\Controllers\Admin\ApplicationController as AdminApplicationController;
use App\Http\Controllers\Admin\KycController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\CompanyDonationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IndividualDonationController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Invitation routes
Route::get('/invitation/{token}/register', [InvitationController::class, 'showRegistrationForm'])
    ->name('invitation.register');

Route::post('/invitation/{token}/register', [InvitationController::class, 'register'])
    ->name('invitation.register.submit');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('verified')->name('dashboard');

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

        Route::post('/payout-mandates/{payoutMandate}/invitations', [InvitationController::class, 'createAndSendInvitation'])->name('invitations.create');
    });
});

// Public webhook endpoint for Smile Identity callbacks
Route::post('/api/kyc/callback', [KycController::class, 'handleCallback'])->name('kyc.callback');

require __DIR__ . '/auth.php';
