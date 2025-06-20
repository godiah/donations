<?php

use App\Http\Controllers\ApplicationController;
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

        Route::get('/{application_number}', [ApplicationController::class, 'show'])
            ->name('applications.show');

        // API route for getting document types
        Route::get('document-types/{contributionReason}', [IndividualDonationController::class, 'getDocumentTypes'])
            ->name('document-types');
    });


    Route::prefix('company')->group(function () {
        Route::get('/application', [DashboardController::class, 'showCompany'])->name('company.application');
    });


    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
