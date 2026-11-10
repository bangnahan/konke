<?php

namespace App\Http\Middleware;

use App\Models\Event;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEventPasscodeVerified
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $slug = $request->route('slug');
        $event = Event::where('slug', $slug)->firstOrFail();

        // 1. Superadmin has automatic access
        if (auth()->check() && auth()->user()->isSuperAdmin()) {
            return $next($request);
        }

        // 2. Check session passcode
        $sessionKey = 'event_passcode_'.$event->id;
        if (! session()->has($sessionKey)) {
            // Check if current route is for checkin panel or general report
            $intendedRoute = $request->routeIs('event.checkin.*')
                ? route('event.checkin.login', ['slug' => $slug])
                : route('event.report.login', ['slug' => $slug]);

            return redirect($intendedRoute)
                ->with('warning', 'Masukkan password event terlebih dahulu untuk mengakses data.');
        }

        return $next($request);
    }
}
