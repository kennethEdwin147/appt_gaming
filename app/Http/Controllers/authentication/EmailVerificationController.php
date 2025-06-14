<?php

namespace AppointmentApp\Authentication\Http\Controllers;

use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

class EmailVerificationController
{
    /**
     * Affiche la vue de notification de vérification d'email (pour utilisateurs connectés)
     *
     * @return \Illuminate\View\View
     */
    public function notice()
    {
        return view('authentication::verify-email');
    }

    /**
     * Affiche la vue de vérification d'email requise (pour utilisateurs non connectés)
     *
     * @return \Illuminate\View\View
     */
    public function verifyRequired()
    {
        return view('authentication::auth.verify-email-required');
    }

    /**
     * Marque l'email de l'utilisateur authentifié comme vérifié
     *
     * @param  \Illuminate\Foundation\Auth\EmailVerificationRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verify(EmailVerificationRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            // Si c'est un créateur sans timezone, rediriger vers la sélection
            if ($request->user()->role === 'creator' &&
                $request->user()->creator &&
                !$request->user()->creator->timezone) {
                return redirect()->route('creator.timezone.select', ['user_id' => $request->user()->id]);
            }
            return redirect()->route('verification.verified');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        // Après vérification, vérifier si c'est un créateur qui a besoin de sélectionner son timezone
        if ($request->user()->role === 'creator' &&
            $request->user()->creator &&
            !$request->user()->creator->timezone) {
            return redirect()->route('creator.timezone.select', ['user_id' => $request->user()->id])
                ->with('success', 'Votre email a été vérifié. Veuillez maintenant sélectionner votre fuseau horaire.');
        }

        return redirect()->route('verification.verified');
    }

    /**
     * Renvoie un lien de vérification d'email
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function send(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('verification.verified');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }

    /**
     * Affiche la page de confirmation après vérification de l'email
     *
     * @return \Illuminate\View\View
     */
    public function verified()
    {
        return view('authentication::auth.email-verified');
    }
}
