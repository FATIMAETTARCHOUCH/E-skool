<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use PDOException;
use Symfony\Component\HttpFoundation\Response;

class CheckDatabaseConnection
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Attempt a simple query to verify database connection
            \DB::connection()->getPdo();
        } catch (QueryException|PDOException $e) {
            return response()->view('errors.database-offline', [], 503);
        }

        return $next($request);
    }
}
