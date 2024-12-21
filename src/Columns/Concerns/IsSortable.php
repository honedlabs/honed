<?php

declare(strict_types=1);

namespace Honed\Table\Columns\Concerns;

use Honed\Table\Sorts\Sort;
use Honed\Table\Sorts\BaseSort;

/**
 * @method string|null getName()
 */
trait IsSortable
{
    /**
     * @var bool|(\Closure():bool)
     */
    protected $sortable = false;

    /**
     * @var \Honed\Table\Sorts\BaseSort
     */
    protected $sort;

    /**
     * Set the sortable property, chainable.
     *
     * @param  bool|(\Closure():bool)  $sortable
     * @return $this
     */
    public function sortable(bool|\Closure $sortable = true): static
    {
        $this->setSortable($sortable);

        return $this;
    }

    /**
     * Set the sortable property quietly.
     * 
     * @param  bool|(\Closure():bool)|null  $sortable
     * @return void
     */
    public function setSortable(bool|\Closure|null $sortable): void
    {
        if (\is_null($sortable)) {
            return;
        }

        $this->sort = Sort::make($this->getName())->agnostic();
        $this->sortable = $sortable;
    }

    /**
     * Determine if the column is sortable.
     * 
     * @return bool
     */
    public function isSortable(): bool
    {
        return (bool) value($this->sortable);
    }

    /**
     * Determine if the column is not sortable.
     *
     * @return bool
     */
    public function isNotSortable(): bool
    {
        return ! $this->isSortable();
    }

    /**
     * Get the sort instance.
     * 
     * @return \Honed\Table\Sorts\BaseSort
     */
    public function getSort(): ?BaseSort
    {
        if ($this->isNotSortable()) {
            return null;
        }

        return $this->sort;
    }
}
