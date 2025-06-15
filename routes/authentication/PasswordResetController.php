<?php

namespace AppointmentApp\Authentication\Http\Controllers;

use App\Services\EmailService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordResetController
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * Affiche le formulaire de demande de réinitialisation de mot de passe
     *
     * @return \Illuminate\View\View
     */
    public function showForgotPasswordForm()
    {
        return view('authentication::password.forgot-password');
    }

    /**
     * Traite la demande de réinitialisation de mot de passe
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    /**
     * Affiche le formulaire de réinitialisation de mot de passe
     *
     * @param  string  $token
     * @return \Illuminate\View\View
     */
    public function showResetForm(Request $request, $token)
    {
        return view('authentication::password.reset-password', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    /**
     * Traite la réinitialisation du mot de passe
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));

                // Envoyer un email de confirmation
                $this->emailService->sendPasswordChangeConfirmation($user);
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('password.reset.success')
            : back()->withErrors(['email' => [__($status)]]);
    }

    /**
     * Affiche la page de succès après réinitialisation du mot de passe
     *
     * @return \Illuminate\View\View
     */
    public function showResetSuccessPage()
    {
        return view('authentication::password.password-reset-success');
    }
}
