<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     * Only allow users with role = 'admin' through. Everyone else is redirected.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('toast_warning', 'Please sign in to continue.');
        }

        if (Auth::user()->role !== 'admin') {
            if ($adminId = session('admin_impersonator_id')) {
                $admin = \App\Models\User::find($adminId);
                if ($admin && $admin->role === 'admin') {
                    Auth::login($admin);
                    return $next($request);
                }
            }

            return redirect()->route('dashboard')
                ->with('toast_error', 'Access denied. Admin privileges required.');
        }

        return $next($request);
    }
}
