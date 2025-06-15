<?php

namespace AppointmentApp\Authentication\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController
{
    /**
     * Show the registration form for users.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        return view('authentication::register');
    }

    /**
     * Handle a registration request for users.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', new Password(8), 'confirmed'],
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user', // Rôle par défaut pour l'inscription utilisateur
        ]);

        event(new Registered($user));

        // Connecter l'utilisateur pour qu'il puisse accéder à la page de vérification
        Auth::login($user);

        // Rediriger vers la page de vérification email
        return redirect()->route('verification.notice')
            ->with('success', 'Votre compte a été créé. Veuillez vérifier votre email pour confirmer votre compte avant de continuer.');
    }

    /**
     * Show the login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('authentication::login');
    }

    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        \Log::info('=== LOGIN ATTEMPT ===');
        \Log::info('Email: ' . $credentials['email']);
        \Log::info('Session ID before: ' . session()->getId());

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            \Log::info('Auth::attempt SUCCESS');
            // Régénérer la session si disponible (pas dans les tests)
            if ($request->hasSession()) {
                $request->session()->regenerate();
            }

            // Vérifier si l'email est vérifié
            $user = Auth::user();

            // Pour les créateurs, vérifier aussi la confirmation créateur
            if ($user->role === 'creator') {
                $creator = $user->creator;

                // Si le créateur n'est pas confirmé, rediriger vers vérification
                if (!$creator || !$creator->confirmed_at) {
                    return redirect()->route('verification.notice')
                        ->with('error', 'Vous devez confirmer votre compte créateur via l\'email reçu.');
                }

                // Si confirmé mais email_verified_at est null, le marquer comme vérifié
                if (!$user->hasVerifiedEmail()) {
                    $user->markEmailAsVerified();
                }
            } else {
                // Pour les utilisateurs normaux, vérifier l'email classique
                if (!$user->hasVerifiedEmail()) {
                    return redirect()->route('verification.notice')
                        ->with('error', 'Vous devez vérifier votre email avant de pouvoir accéder à votre compte.');
                }
            }

            if (Auth::user()->role === 'creator') {
                $creator = Auth::user()->creator;

                // Si le créateur n'a pas encore configuré son timezone, rediriger
                if (!$creator->timezone) {
                    return redirect()->route('creator.timezone.select', ['user_id' => Auth::user()->id])
                        ->with('info', 'Veuillez sélectionner votre fuseau horaire pour terminer la configuration.');
                }

                return redirect()->intended(route('creator.dashboard'));
            } elseif (Auth::user()->role === 'customer') {
                return redirect()->intended(route('customer.dashboard'));
            }

            return redirect()->intended(route('home'));
        }

        \Log::info('Auth::attempt FAILED');
        return back()->withErrors([
            'email' => 'Les informations d\'identification fournies ne correspondent pas à nos enregistrements.',
        ])->onlyInput('email');
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();

        // Invalider la session si disponible (pas dans les tests)
        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect('/');
    }
}
