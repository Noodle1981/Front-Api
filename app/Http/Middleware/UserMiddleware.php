<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserMiddleware
{
    /**
     * Handle an incoming request.
     * Asegura que solo usuarios NO admin puedan acceder al CRM
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Si es admin, denegar acceso
        if (auth()->user()->isAdmin()) {
            abort(403, 'Los administradores solo pueden acceder al panel administrativo.');
        }

        return $next($request);
    }
}
