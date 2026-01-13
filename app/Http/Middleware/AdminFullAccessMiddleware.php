<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Permission;
use App\Models\User;

class AdminFullAccessMiddleware
{
    /**
     * Handle an incoming request.
     * Middleware ini hanya mengizinkan admin penuh (bukan input_hadiah)
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if ($user->isAdmin()) {
            return $next($request);
        }

        $route = $request->route()->getName();

        // Permission: admin (Kelola Admin)
        if (str_contains($route, 'admins') && $user->hasPermission('admin')) {
            return $next($request);
        }

        // Permission: laporan (Laporan)
        if (str_contains($route, 'laporan') && $user->hasPermission('laporan')) {
            return $next($request);
        }

        // Permission: undian (Undian, Winner Actions, Candidates)
        if ((str_contains($route, 'undian') || str_contains($route, 'winner.') || str_contains($route, 'candidates')) && $user->hasPermission('undian')) {
            return $next($request);
        }

        // Permission: settings (Pengaturan)
        if (str_contains($route, 'settings') && $user->hasPermission('settings')) {
            return $next($request);
        }

        // Permission: pemenang/hadiah (Halaman Pemenang, Winners List, Prize Actions)
        // Note: 'undian' permission also often needs access to winners list
        if (
            (str_contains($route, 'pemenang') || str_contains($route, 'prizes') || str_contains($route, 'winners')) &&
            ($user->hasPermission('pemenang') || $user->hasPermission('hadiah') || $user->hasPermission('undian'))
        ) {
            return $next($request);
        }

        abort(403, 'Anda tidak memiliki akses ke halaman ini.');

        return $next($request);
    }
}
