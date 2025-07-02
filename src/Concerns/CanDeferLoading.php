<?php

declare(strict_types=1);

namespace Honed\Honed\Concerns;

use Inertia\DeferProp;
use Inertia\Inertia;
use Inertia\LazyProp;
use LogicException;

/**
 * @phpstan-require-implements \Illuminate\Contracts\Support\Arrayable
 */
trait CanDeferLoading
{
    /**
     * Determine if the class supports deferrable props, indicating that the class
     * is compatible with Inertia.js version 2.
     */
    public static function supportsDeferrableProps(): bool
    {
        return class_exists(DeferProp::class);
    }

    /**
     * Determine if the class does not support deferrable props, indicating that
     * the class is compatible with Inertia.js version 1.
     */
    public static function doesNotSupportDeferrableProps(): bool
    {
        return ! static::supportsDeferrableProps();
    }

    /**
     * Defer the loading of the instance.
     *
     * @throws LogicException
     */
    public function defer(string $group = 'default'): DeferProp
    {
        if (static::doesNotSupportDeferrableProps()) {
            throw new LogicException(
                'Deferrable props are not supported, please upgrade to Inertia.js version 2.',
            );
        }

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
