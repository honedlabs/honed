<?php

declare(strict_types=1);

namespace Honed\Honed\Concerns;

use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 *
 * @phpstan-require-extends \Spatie\LaravelData\Data
 */
trait Hydratable
{
    /**
     * The model to hydrate the data from.
     *
     * @return class-string<TModel>
     */
    abstract public function hydrateFrom(): string;

    /**
     * Hydrate the data into a model.
     *
     * @param  array<int, string>  $except
     * @return TModel
     */
    public function hydrate(array $except = []): Model
    {
        $model = $this->hydrateFrom();

        $attributes = $this->except(...$except)->toArray();

        return $model::make($attributes);
    }
}
