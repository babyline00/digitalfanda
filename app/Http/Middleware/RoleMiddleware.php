<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if ($user->status !== 'active') {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Your account has been suspended.');
        }

        if (in_array('superadmin', $roles) && $user->isSuperAdmin()) {
            return $next($request);
        }

        if (in_array('admin', $roles) && $user->isAdmin()) {
            return $next($request);
        }

        if (in_array('seller', $roles) && $user->isSeller()) {
            return $next($request);
        }

        if (in_array('cashier', $roles) && $user->isCashier()) {
            return $next($request);
        }

        if (in_array('customer', $roles) && $user->role === 'customer') {
            return $next($request);
        }

        abort(403, 'Unauthorized. Required role: ' . implode(', ', $roles));
    }
}