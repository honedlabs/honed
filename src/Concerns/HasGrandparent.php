<?php

declare(strict_types=1);

namespace Honed\Honed\Concerns;

use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 */
trait HasGrandparent
{
    /**
     * The grandparent model.
     * 
     * @var TModel
     */
    protected $grandparent;

    /**
     * Set the grandparent model.
     * 
     * @param TModel $grandparent
     * @return $this
     */
    public function grandparent(Model $grandparent): static
    {
        $this->grandparent = $grandparent;

        return $this;
    }

    /**
     * Get the grandparent model.
     * 
     * @return TModel
     */
    public function getGrandparent(): Model
    {
        return $this->grandparent;
    }
}