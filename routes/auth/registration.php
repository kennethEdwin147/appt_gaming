<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\CreatorRegistrationController;
use App\Http\Controllers\Auth\CustomerRegistrationController;

// Registration Routes
Route::get('/register/creator', [CreatorRegistrationController::class, 'showRegistrationForm'])->name('creator.register');
Route::post('/register/creator', [CreatorRegistrationController::class, 'register'])->name('creator.register.submit');

Route::get('/register/client', [CustomerRegistrationController::class, 'showRegistrationForm'])->name('customer.register');
Route::post('/register/client', [CustomerRegistrationController::class, 'register'])->name('customer.register.submit');

// Role selection
Route::post('/process-role-choice', function () {
    $role = request('role');
    
    if ($role === 'creator') {
        return redirect()->route('creator.register');
    } elseif ($role === 'client') {
        return redirect()->route('customer.register');
    }
    
    return redirect()->back()->with('error', 'Please select a role');
})->name('process-role-choice');