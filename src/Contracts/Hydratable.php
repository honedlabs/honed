<?php

declare(strict_types=1);

namespace Honed\Honed\Contracts;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 */
interface Hydratable
{
    /**
     * The model to hydrate the data from.
     *
     * @return class-string<TModel>
     */
    public function hydrateFrom(): string;
}
