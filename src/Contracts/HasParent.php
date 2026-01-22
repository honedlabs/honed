<?php

declare(strict_types=1);

namespace Honed\Honed\Contracts;

use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 */
interface HasParent
{
    /**
     * Get the parent model.
     *
     * @return TModel
     */
    public function getParent(): Model;

    /**
     * Set the parent model.
     *
     * @param  TModel  $parent
     * @return $this
     */
    public function parent(Model $parent): static;
}
