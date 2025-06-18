<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\creator\Creator;

class CreatorRegistrationController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register.register-creator');
    }

    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'creator',
        ]);

        Creator::create([
            'user_id' => $user->id,
            'confirmation_token' => \Str::random(60),
        ]);

        $user->sendEmailVerificationNotification();

        auth()->login($user);

        return redirect()->route('verification.notice');
    }
}
