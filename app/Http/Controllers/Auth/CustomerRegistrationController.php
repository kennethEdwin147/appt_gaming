<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CustomerRegistrationController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register-client');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            $user = User::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'customer',
            ]);

            Customer::create(['user_id' => $user->id]);
            
            $user->sendEmailVerificationNotification();
            Auth::login($user);

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'redirect' => route('verification.notice')]);
            }

            return redirect()->route('verification.notice')
                           ->with('success', 'Compte créé ! Vérifiez votre email.');
                           
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Erreur lors de l\'inscription'], 422);
            }

            return redirect()->back()
                           ->with('error', 'Une erreur est survenue')
                           ->withInput();
        }
    }
}