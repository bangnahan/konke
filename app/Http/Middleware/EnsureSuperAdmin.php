<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return redirect()->route('admin.login')->with('warning', 'Silakan login terlebih dahulu.');
        }

        if (! auth()->user()->isSuperAdmin()) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin Superadmin.');
        }

        return $next($request);
    }
}
