<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showChooseRole()
    {
        return view('authentication.choose-role.choose-role');
    }

    public function showLogin()
    {
        return view('authentication.login.login');
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
                return redirect('/email/verify');
            }
            
            // Redirection selon rôle et état
            if ($user->role === 'creator') {
                $creator = $user->creator;
                
                // Si setup pas terminé, rediriger vers setup
                if (!$creator || !$creator->timezone || !$creator->bio) {
                    return redirect('/creator/setup/timezone');
                }
                
                return redirect('/creator/dashboard');
            }
            
            if ($user->role === 'customer') {
                return redirect('/customer/dashboard');
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