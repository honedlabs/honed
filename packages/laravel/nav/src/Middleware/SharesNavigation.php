<?php

namespace Honed\Nav\Middleware;

use Closure;
use Honed\Nav\Facades\Nav;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SharesNavigation
{
    /**
     * Handle the incoming request.
     *
     * @return \Closure
     */
    public function handle(Request $request, Closure $next)
    {
        Inertia::share([
            'nav' => Nav::get(),
        ]);

        return $next($request);
    }
}
