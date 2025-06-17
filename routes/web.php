<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Home\HomeController;
use App\Http\Controllers\Auth\CreatorRegistrationController;

Route::get('/', [HomeController::class, 'index']);

Route::get('/choose-role', function () {
    return view('authentication.choose-role.choose-role');
})->name('choose-role');

Route::get('/login', function () {
    return view('authentication.login.login');
})->name('login');

Route::get('/register/creator', function () {
    return view('authentication.register.register-creator');
})->name('register.creator');

Route::post('/register/creator', [CreatorRegistrationController::class, 'register']);

Route::get('/register/client', function () {
    return view('authentication.register.register-customer');
})->name('register.client');

Route::post('/process-role-choice', function () {
    $role = request('role');
    
    if ($role === 'creator') {
        return redirect()->route('register.creator');
    } elseif ($role === 'client') {
        return redirect()->route('register.client');
    }
    
    return redirect()->back()->with('error', 'Please select a role');
})->name('process-role-choice');

Route::get('/password/reset', function () {
    return view('authentication.password.forgot-password');
})->name('password.request');
