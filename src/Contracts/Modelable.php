<?php

declare(strict_types=1);

namespace Honed\Honed\Contracts;

use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 */
interface Modelable
{
    /**
     * Get the model.
     *
     * @return TModel
     */
    public function getModel(): Model;

    /**
     * Set the model.
     *
     * @param  TModel  $model
     * @return $this
     */
    public function model(Model $model): static;
}
