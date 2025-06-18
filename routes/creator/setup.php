<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\CreatorSetupController;

// Creator Setup Routes (require auth + creator role)
Route::middleware(['auth', 'creator'])->group(function () {
    Route::get('/creator/setup/timezone', [CreatorSetupController::class, 'showTimezoneForm'])->name('creator.setup.timezone');
    Route::post('/creator/setup/timezone', [CreatorSetupController::class, 'saveTimezone'])->name('creator.setup.timezone.save');
    Route::get('/creator/setup/profile', [CreatorSetupController::class, 'showProfileForm'])->name('creator.setup.profile');
    Route::post('/creator/setup/profile', [CreatorSetupController::class, 'saveProfile'])->name('creator.setup.profile.save');
});