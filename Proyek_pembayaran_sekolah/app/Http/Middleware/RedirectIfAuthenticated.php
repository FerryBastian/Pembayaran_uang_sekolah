<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? ['web'] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return redirect($this->redirectPath(Auth::guard($guard)->user()->role));
            }
        }

        return $next($request);
    }

    private function redirectPath(?string $role): string
    {
        return match ($role) {
            'admin' => route('admin.dashboard'),
            'guru' => route('guru.dashboard'),
            'orang_tua' => route('orang-tua.dashboard'),
            'siswa' => route('siswa.dashboard'),
            default => route('home'),
        };
    }
}
