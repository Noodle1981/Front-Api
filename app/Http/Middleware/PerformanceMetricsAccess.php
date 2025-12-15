<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PerformanceMetricsAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->user() || !auth()->user()->canAccessPerformanceMetrics()) {
            abort(403, 'No tienes permiso para acceder a las métricas de rendimiento.');
        }

        return $next($request);
    }
}