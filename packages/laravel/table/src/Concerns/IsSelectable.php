<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Honed\Table\Columns\Column;
use Honed\Table\Contracts\ShouldSelect;
use Illuminate\Support\Arr;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TBuilder of \Illuminate\Database\Eloquent\Builder<TModel>
 */
trait IsSelectable
{
    /**
     * Whether to do column selection.
     *
     * @var bool|null
     */
    protected $select;

    /**
     * The columns to always be selected.
     *
     * @var array<int,string>|null
     */
    protected $selects;

    /**
     * Set whether to do column selection.
     *
     * @param  bool  $select
     * @return $this
     */
    public function selectable($select = true)
    {
        $this->select = $select;

        return $this;
    }

    /**
     * Determine whether to do column selection.
     *
     * @return bool
     */
    public function isSelectable()
    {
        if (isset($this->select)) {
            return $this->select;
        }

        if (\method_exists($this, 'select')) {
            return (bool) $this->select();
        }

        if ($this instanceof ShouldSelect) {
            return true;
        }

        return static::fallbackSelectable();
    }

    /**
     * Whether to do column selection from the config.
     *
     * @return bool
     */
    public function fallbackSelectable()
    {
        return (bool) config('table.select', false);
    }

    /**
     * Set the columns to always have selected.
     *
     * @param  array<int,string>  $selects
     * @return $this
     */
    public function withSelects(...$selects)
    {
        $selects = Arr::flatten($selects);

        $this->selects = \array_merge($this->selects ?? [], $selects);

        return $this;
    }

    /**
     * Get the columns to always have selected.
     *
     * @return array<int,string>|null
     */
    public function getSelects()
    {
        $selects = \method_exists($this, 'selects') ? $this->selects() : [];

        $selects = \array_merge($selects, $this->selects ?? []);

        return $selects;
    }
}