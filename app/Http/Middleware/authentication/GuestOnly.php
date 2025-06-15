<?php

namespace AppointmentApp\Authentication\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GuestOnly
{
    /**
     * Handle an incoming request.
     * Redirige les utilisateurs connectés vers leur dashboard approprié.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Si l'utilisateur est connecté, le rediriger
        if (auth()->check()) {
            $user = auth()->user();
            
            // Rediriger en fonction du rôle
            if ($user->role === 'creator') {
                return redirect()->route('creator.dashboard');
            } elseif ($user->role === 'customer') {
                return redirect()->route('customer.dashboard');
            }

            // Pour les autres rôles, rediriger vers home
            return redirect()->route('home');
        }

        // Si pas connecté, continuer vers la page demandée (login, register, etc.)
        return $next($request);
    }
}
