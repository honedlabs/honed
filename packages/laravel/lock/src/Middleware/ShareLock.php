<?php

declare(strict_types=1);

namespace Honed\Lock\Middleware;

use Closure;
use Honed\Lock\Facades\Lock;
use Honed\Lock\Support\Parameters;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ShareLock
{
    /**
     * Handle the incoming request.
     *
     * @return \Closure
     */
    public function handle(Request $request, Closure $next)
    {
        Inertia::share(
            Parameters::PROP,
            static fn () => Lock::all()
        );

        return $next($request);
    }
}
