<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(
        Request $request,
        Closure $next,
        string $role
    ): Response {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!$user->is_active) {
            abort(403, 'Your account is inactive.');
        }

        if ($user->role !== $role) {
            abort(403, 'You are not authorized to access this page.');
        }

        return $next($request);
    }
}
