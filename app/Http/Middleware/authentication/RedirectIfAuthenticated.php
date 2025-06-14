<?php

namespace AppointmentApp\Authentication\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Rediriger vers la page appropriée en fonction du rôle de l'utilisateur
                if (Auth::user()->role === 'creator') {
                    return redirect()->route('creator.dashboard');
                } elseif (Auth::user()->role === 'customer') {
                    return redirect()->route('customer.dashboard');
                }

                return redirect()->route('home');
            }
        }

        return $next($request);
    }
}
