<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Simple file-based or cache-based maintenance check
        if (file_exists(storage_path('framework/maintenance_mode')) && auth()->check() && auth()->user()->role === 'student') {
            return response()->view('maintenance', [], 503);
        }

        return $next($request);
    }
}
