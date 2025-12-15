<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if ($role === 'user' && $request->user()->is_admin) {
            abort(403, 'Esta sección es solo para usuarios regulares.');
        }
        
        if ($role === 'admin' && !$request->user()->is_admin) {
            abort(403, 'Esta sección requiere privilegios de administrador.');
        }

        return $next($request);
    }
}