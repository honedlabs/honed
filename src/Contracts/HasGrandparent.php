<?php

declare(strict_types=1);

namespace Honed\Honed\Contracts;

use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 */
interface HasGrandparent
{
    /**
     * Get the grandparent model.
     *
     * @return TModel
     */
    public function getGrandparent(): Model;

    /**
     * Set the grandparent model.
     *
     * @param TModel $grandparent
     * @return $this
     */
    public function grandparent(Model $grandparent): static;
}