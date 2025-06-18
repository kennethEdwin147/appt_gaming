<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

// Authentication Routes
Route::get('/choose-role', [AuthController::class, 'showChooseRole'])->name('choose-role');
Route::get('/register', [AuthController::class, 'showChooseRole'])->name('register'); // Alias pour les tests
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');