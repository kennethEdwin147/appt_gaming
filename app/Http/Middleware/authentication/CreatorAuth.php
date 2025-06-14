<?php

namespace AppointmentApp\Authentication\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CreatorAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Debug
        \Log::info('CreatorAuth middleware - Start', [
            'url' => $request->url(),
            'auth_check' => auth()->check(),
            'session_id' => $request->session()->getId(),
        ]);

        // Vérifier si l'utilisateur est connecté
        if (!auth()->check()) {
            \Log::info('CreatorAuth middleware - User not authenticated');
            // Vérifier si c'est une session expirée ou un accès direct
            if ($request->hasSession() && $request->session()->has('_token')) {
                // Session existe mais utilisateur pas connecté = session expirée
                \Log::info('CreatorAuth middleware - Session expired');
                return redirect()->route('login')->with('warning', 'Votre session a expiré. Veuillez vous reconnecter pour accéder à votre espace créateur.');
            } else {
                // Pas de session = accès direct à une page protégée
                \Log::info('CreatorAuth middleware - No session');
                return redirect()->route('login')->with('info', 'Veuillez vous connecter pour accéder à votre espace créateur.');
            }
        }

        $user = auth()->user();

        // Vérifier si l'utilisateur a le rôle créateur
        if ($user->role !== 'creator') {
            // Si pas créateur, rediriger vers home avec erreur
            return redirect()->route('home')->with('error', 'Accès refusé. Cette section est réservée aux créateurs.');
        }

        // Vérifier si le créateur existe et est confirmé
        $creator = $user->creator;
        if (!$creator) {
            // Si pas de profil créateur, rediriger vers inscription créateur
            return redirect()->route('creator.register')->with('error', 'Vous devez d\'abord créer votre profil créateur.');
        }

        if (!$creator->confirmed_at) {
            // Si créateur pas confirmé, rediriger vers vérification
            return redirect()->route('verification.notice')->with('error', 'Vous devez confirmer votre compte créateur via l\'email reçu.');
        }

        // Si timezone pas configuré, rediriger vers configuration
        if (!$creator->timezone) {
            return redirect()->route('creator.timezone.select', ['user_id' => $user->id])
                ->with('info', 'Veuillez sélectionner votre fuseau horaire pour terminer la configuration.');
        }

        // Tout est OK, continuer
        return $next($request);
    }
}
