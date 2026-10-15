<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SellerMiddleware
{
    /**
     * Handle an incoming request.
     * Only allow users with role = 'seller' (or 'admin') through.
     * Regular 'user' accounts are redirected with a clear message.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('toast_warning', 'Please sign in to access your Freelancer Dashboard.');
        }

        $role = Auth::user()->role;

        if (!in_array($role, ['seller', 'admin'])) {
            if ($adminId = session('admin_impersonator_id')) {
                $admin = \App\Models\User::find($adminId);
                if ($admin && in_array($admin->role, ['seller', 'admin'])) {
                    return $next($request);
                }
            }

            return redirect()->route('dashboard')
                ->with('toast_error', 'Access denied. This area is for freelancers only. Upgrade your account to become a seller.');
        }

        return $next($request);
    }
}
