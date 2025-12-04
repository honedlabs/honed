<?php

declare(strict_types=1);

namespace Honed\Honed\Concerns;

use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 */
trait HasParent
{
    /**
     * The parent model.
     *
     * @var TModel
     */
    protected $parent;

    /**
     * Set the parent model.
     *
     * @param  TModel  $parent
     * @return $this
     */
    public function parent(Model $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get the parent model.
     *
     * @return TModel
     */
    public function getParent(): Model
    {
        return $this->parent;
    }
}
