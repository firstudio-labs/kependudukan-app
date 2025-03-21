<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // For web users (admin, superadmin, operator)
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            if ($user->role === $role) {
                return $next($request);
            }

            // If not the correct role, redirect to their appropriate page
            return redirect('/' . $user->role . '/index')
                ->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        // For penduduk users (treated as having 'user' role)
        if ($role === 'user' && Auth::guard('penduduk')->check()) {
            return $next($request);
        }

        // Not authenticated with either guard
        return redirect('/login')->with('error', 'Anda harus login terlebih dahulu.');
    }
}
