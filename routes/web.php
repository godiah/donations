<?php

use App\Http\Controllers\Admin\ApplicationController as AdminApplicationController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\CompanyDonationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IndividualDonationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
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
     */
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::prefix('/applications')->name('applications.')->group(function () {
            Route::get('/', [AdminApplicationController::class, 'index'])->name('index');
        });
    });
});

require __DIR__ . '/auth.php';
