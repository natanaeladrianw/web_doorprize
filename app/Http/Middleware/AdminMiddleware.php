<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Permission;
use App\Models\User;


class AdminMiddleware
{
    /**
     * Handle an incoming request.
     * Middleware ini mengizinkan admin dan input_hadiah
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Belum login
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }
        // Bukan admin atau input_hadiah
        if (!Auth::user()->canAccessAdmin()) {
            abort(403, 'Anda tidak memiliki akses ke halaman admin.');
        }

        return $next($request);
    }
}