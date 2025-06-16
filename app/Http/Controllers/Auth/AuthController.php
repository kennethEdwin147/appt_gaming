<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Creator;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showChooseRole()
    {
        return view('auth.choose-role');
    }

    public function showClientRegister()
    {
        return view('auth.register-client');
    }

    public function showCreatorRegister()
    {
        return view('auth.register-creator');
    }

    public function registerClient(Request $request)
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

    public function registerCreator(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'gaming_pseudo' => 'required|string|max:50|unique:creators,gaming_pseudo',
        ]);

        try {
            $user = User::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'creator',
            ]);

            Creator::create([
                'user_id' => $user->id,
                'gaming_pseudo' => $validated['gaming_pseudo'],
            ]);
            
            $user->sendEmailVerificationNotification();
            Auth::login($user);

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'redirect' => route('verification.notice')]);
            }

            return redirect()->route('verification.notice')
                           ->with('success', 'Compte créateur créé ! Vérifiez votre email.');
                           
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Erreur lors de l\'inscription'], 422);
            }

            return redirect()->back()
                           ->with('error', 'Une erreur est survenue')
                           ->withInput();
        }
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // Vérifier si email vérifié
            if (!$user->hasVerifiedEmail()) {
                return redirect()->route('verification.notice');
            }
            
            // Redirection selon rôle et état
            if ($user->role === 'creator') {
                $creator = $user->creator;
                
                // Si setup pas terminé, rediriger vers setup
                if (!$creator->timezone || !$creator->bio) {
                    return redirect()->route('creator.setup.timezone');
                }
                
                return redirect()->route('creator.dashboard');
            }
            
            if ($user->role === 'customer') {
                return redirect()->route('customer.dashboard');
            }
            
            return redirect()->intended('/');
        }
        
        return back()->withErrors([
            'email' => 'Identifiants incorrects.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}