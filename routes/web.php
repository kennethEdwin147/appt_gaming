<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Home\HomeController;

// Home Route
Route::get('/', [HomeController::class, 'index'])->name('home');

// Include Auth Routes
require __DIR__ . '/auth/auth.php';
require __DIR__ . '/auth/registration.php';
require __DIR__ . '/auth/email-verification.php';
require __DIR__ . '/auth/password-reset.php';

// Include Creator Routes
require __DIR__ . '/creator/setup.php';
require __DIR__ . '/creator/dashboard.php';

// Include Customer Routes
require __DIR__ . '/customer/dashboard.php';
