<?php

use Illuminate\Support\Facades\Route;

// Customer Dashboard Routes (temporary placeholders)
Route::middleware(['auth'])->group(function () {
    Route::get('/customer/dashboard', function () {
        return view('customer.dashboard');
    })->name('customer.dashboard');
    
    // Add more customer routes here as needed
    // Route::get('/customer/browse', [CustomerBrowseController::class, 'index'])->name('customer.browse');
    // Route::get('/customer/reservations', [CustomerReservationController::class, 'index'])->name('customer.reservations');
    // Route::get('/customer/profile', [CustomerProfileController::class, 'show'])->name('customer.profile');
});