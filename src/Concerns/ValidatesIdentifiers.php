<?php

declare(strict_types=1);

namespace Honed\Honed\Concerns;

use Closure;
use Illuminate\Support\Arr;

/**
 * @internal
 */
trait ValidatesIdentifiers
{
    /**
     * Validate each item in an array.
     *
     * @param  array<int, mixed>  $value
     */
    public static function each(string $attribute, mixed $value, Closure $fail): void
    {
        foreach ($value as $item) {
            if (! is_scalar($item)) {
                $fail('An invalid record has been selected, please check your selection.');
            }
        }
    }
}
