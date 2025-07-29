<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApplicantIsLoggedIn
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, \Closure $next)
    {
        $token = session('access_token');
        $user = session('user');
        $expiresAt = session('token_expires_at');

        if (!$token || !$user || !$expiresAt) {
            return redirect()->route('admissions.starting')->with('error', 'Please log in.');
        }

        // Check if the token is expired
        if (now()->greaterThan(\Carbon\Carbon::parse($expiresAt))) {
            session()->flush(); // Clear session
            return redirect()->route('admissions.starting')->with('error', 'Your session has expired. Please log in again.');
        }

        return $next($request);
    }
}
