<?php

namespace AppointmentApp\Authentication\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticatedOnly
{
    /**
     * Handle an incoming request.
     * Redirige les utilisateurs non connectés vers la page de login.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Si l'utilisateur n'est pas connecté, rediriger vers login
        if (!auth()->check()) {
            // Vérifier si c'est une session expirée ou un accès direct
            if ($request->hasSession() && $request->session()->has('_token')) {
                // Session existe mais utilisateur pas connecté = session expirée
                return redirect()->route('login')->with('warning', 'Votre session a expiré. Veuillez vous reconnecter.');
            } else {
                // Pas de session = accès direct à une page protégée
                return redirect()->route('login')->with('info', 'Veuillez vous connecter pour accéder à cette page.');
            }
        }

        // Si connecté, continuer vers la page demandée
        return $next($request);
    }
}
