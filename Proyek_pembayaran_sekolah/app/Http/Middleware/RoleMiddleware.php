<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Usage di route: middleware('role:admin')
     * Atau multi role: middleware('role:admin,guru')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            if (!$request->expectsJson()) {
                return redirect()->route('login');
            }

            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Token tidak valid.',
            ], 401);
        }

        // Flatten roles in case they are passed as comma-separated strings
        $flattenedRoles = [];
        foreach ($roles as $role) {
            if (str_contains($role, ',')) {
                $flattenedRoles = array_merge($flattenedRoles, explode(',', $role));
            } else {
                $flattenedRoles[] = $role;
            }
        }

        if (!in_array($request->user()->role, $flattenedRoles)) {
            if (!$request->expectsJson()) {
                abort(403, 'Anda tidak memiliki akses ke halaman ini.');
            }

            return response()->json([
                'success' => false,
                'message' => 'Forbidden. Anda tidak memiliki akses.',
            ], 403);
        }

        return $next($request);
    }
}
