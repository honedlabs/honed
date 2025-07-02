<?php

declare(strict_types=1);

namespace Honed\Honed\Concerns;

use Inertia\DeferProp;
use Inertia\Inertia;
use Inertia\LazyProp;

/**
 * @phpstan-require-implements \Illuminate\Contracts\Support\Arrayable
 */
trait CanDeferLoading
{
    /**
     * Defer the loading of the instance.
     */
    public function defer(string $group = 'default'): DeferProp
    {
        return Inertia::defer(fn () => $this->toArray(), $group);
    }

    /**
     * Lazily load the instance.
     */
    public function lazy(): LazyProp
    {
        return Inertia::lazy(fn () => $this->toArray());
    }
}
