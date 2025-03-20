<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Honed\Table\Columns\Column;
use Illuminate\Support\Arr;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TBuilder of \Illuminate\Database\Eloquent\Builder<TModel>
 */
trait HasColumns
{
    /**
     * The columns to be used for the table.
     *
     * @var array<int,\Honed\Table\Columns\Column<TModel, TBuilder>>|null
     */
    protected $columns;

    /**
     * The cached columns to be used for pipelines.
     *
     * @var array<int,\Honed\Table\Columns\Column<TModel, TBuilder>>
     */
    protected $cachedColumns = [];

    /**
     * Whether the columns should be retrievable.
     *
     * @var bool
     */
    protected $withoutColumns = false;

    /**
     * Merge a set of columns with the existing columns.
     *
     * @param  iterable<int,\Honed\Table\Columns\Column<TModel, TBuilder>>  ...$columns
     * @return $this
     */
    public function withColumns(...$columns)
    {
        $columns = Arr::flatten($columns);

        $this->columns = \array_merge($this->columns ?? [], $columns);

        return $this;
    }

    /**
     * Get the columns for the table.
     *
     * @return array<int,\Honed\Table\Columns\Column<TModel, TBuilder>>
     */
    public function getColumns()
    {
        if ($this->isWithoutColumns()) {
            return [];
        }

        return once(function () {

            $columns = \method_exists($this, 'columns') ? $this->columns() : [];

            $columns = \array_merge($columns, $this->columns ?? []);

            return \array_values(
                \array_filter(
                    $columns,
                    static fn (Column $column) => $column->isAllowed()
                )
            );
        });
    }

    /**
     * Determine if the table has columns.
     *
     * @return bool
     */
    public function hasColumns()
    {
        return filled($this->getColumns());
    }

    /**
     * Get the cached columns.
     *
     * @return array<int,\Honed\Table\Columns\Column<TModel, TBuilder>>
     */
    public function getCachedColumns()
    {
        return $this->cachedColumns;
    }

    /**
     * Set the cached columns.
     *
     * @param  array<int,\Honed\Table\Columns\Column<TModel, TBuilder>>  $cachedColumns
     * @return $this
     */
    public function cacheColumns($cachedColumns)
    {
        $this->cachedColumns = $cachedColumns;

        return $this;
    }

    /**
     * Flush the cached columns.
     *
     * @return void
     */
    public function flushCachedColumns()
    {
        $this->cachedColumns = [];
    }

    /**
     * Set the instance to not provide the columns.
     *
     * @param  bool  $withoutColumns
     * @return $this
     */
    public function withoutColumns($withoutColumns = true)
    {
        $this->withoutColumns = $withoutColumns;

        return $this;
    }

    /**
     * Determine if the instance should not provide the columns.
     *
     * @return bool
     */
    public function isWithoutColumns()
    {
        return $this->withoutColumns;
    }

    /**
     * Get the columns as an array.
     *
     * @return array<int,array<string,mixed>>
     */
    public function columnsToArray()
    {
        return \array_map(
            static fn (Column $column) => $column->toArray(),
            $this->getColumns()
        );
    }
}
