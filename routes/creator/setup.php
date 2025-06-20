<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\CreatorSetupController;

// Creator Setup Routes (require auth + creator role)
Route::middleware(['auth', 'creator'])->group(function () {
    // Nouvelle route combinée
    Route::get('/creator/setup', [CreatorSetupController::class, 'showCombinedForm'])->name('creator.setup');
    Route::post('/creator/setup', [CreatorSetupController::class, 'saveCombined'])->name('creator.setup.combined.save');
    
    // Anciennes routes redirigées vers la nouvelle
    Route::get('/creator/setup/timezone', function() {
        return redirect()->route('creator.setup');
    })->name('creator.setup.timezone');
    
    Route::get('/creator/setup/profile', function() {
        return redirect()->route('creator.setup');
    })->name('creator.setup.profile');
    
    // Garder les anciennes routes POST pour la compatibilité
    Route::post('/creator/setup/timezone', [CreatorSetupController::class, 'saveTimezone'])->name('creator.setup.timezone.save');
    Route::post('/creator/setup/profile', [CreatorSetupController::class, 'saveProfile'])->name('creator.setup.profile.save');
});
