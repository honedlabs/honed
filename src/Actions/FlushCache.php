<?php

declare(strict_types=1);

namespace Honed\Honed\Actions;

use Honed\Action\Contracts\Action;
use Illuminate\Database\Eloquent\Model;

/**
 * @template TInput
 */
abstract class FlushCache implements Action
{
    /**
     * The cache to flush from.
     *
     * @return class-string<\Honed\Command\CacheManager>
     */
    abstract public function cache(): string;

    /**
     * Flush the cache using the given input.
     *
     * @param  TInput  $input
     */
    public function handle($input): void
    {
        $key = $this->prepare($input);

        $this->cache()::forget($key);

        $this->after($input, $key);
    }

    /**
     * Create the cache key from the input.
     *
     * @param  TInput  $input
     * @return mixed
     */
    protected function prepare($input)
    {
        if ($input instanceof Model) {
            return $input->getRouteKey();
        }

        return $input;
    }

    /**
     * Perform actions after the cache has been flushed.
     *
     * @param  TInput  $input
     * @param  mixed  $key
     */
    protected function after($input, $key): void {}
}
