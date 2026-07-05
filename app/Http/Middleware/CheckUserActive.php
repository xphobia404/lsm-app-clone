<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserActive
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && !auth()->user()->is_active && auth()->user()->role === 'user') {
            auth()->logout();
            return redirect()->route('login')->withErrors(['username' => 'Akun Anda telah dinonaktifkan oleh Admin.']);
        }

        return $next($request);
    }
}
