<?php

declare(strict_types=1);

namespace Honed\Refine\Concerns;

use Honed\Refine\Sort;
use Illuminate\Support\Arr;

use function array_filter;
use function array_map;
use function array_merge;
use function array_values;

trait HasSorts
{
    /**
     * Whether the sorts should be applied.
     *
     * @var bool
     */
    protected $sort = true;

    /**
     * List of the sorts.
     *
     * @var array<int,Sort>
     */
    protected $sorts = [];

    /**
     * The query parameter to identify the sort to apply.
     *
     * @var string|null
     */
    protected $sortKey;

    /**
     * The default query parameter to identify the sort to apply.
     *
     * @var string
     */
    protected static $useSortKey = 'sort';

    /**
     * Set the default query parameter to identify the sort to apply.
     *
     * @param  string  $sortKey
     * @return void
     */
    public static function useSortKey($sortKey = 'sort')
    {
        static::$useSortKey = $sortKey;
    }

    /**
     * Set whether the sorts should be applied.
     *
     * @return $this
     */
    public function disableSorting()
    {
        $this->sort = false;

        return $this;
    }

    /**
     * Set the sorts to not be applied.
     *
     * @return $this
     */
    public function enableSorting()
    {
        $this->sort = true;

        return $this;
    }

    /**
     * Determine if the sorts should be applied.
     *
     * @return bool
     */
    public function sortingEnabled()
    {
        return $this->sort;
    }

    /**
     * Determine if the sorts should not be applied.
     *
     * @return bool
     */
    public function sortingDisabled()
    {
        return ! $this->sortingEnabled();
    }

    /**
     * Define the sorts for the instance.
     *
     * @return array<int,Sort>
     */
    public function sorts()
    {
        return [];
    }

    /**
     * Merge a set of sorts with the existing sorts.
     *
     * @param  Sort|iterable<int, Sort>  ...$sorts
     * @return $this
     */
    public function withSorts(...$sorts)
    {
        /** @var array<int, Sort> $sorts */
        $sorts = Arr::flatten($sorts);

        $this->sorts = array_merge($this->sorts, $sorts);

        return $this;
    }

    /**
     * Retrieve the sorts.
     *
     * @return array<int,Sort>
     */
    public function getSorts()
    {
        if ($this->sortingDisabled()) {
            return [];
        }

        return once(fn () => array_values(
            array_filter(
                array_merge($this->sorts(), $this->sorts),
                static fn (Sort $sort) => $sort->isAllowed()
            )
        ));
    }

    /**
     * Set the query parameter to identify the sort to apply.
     *
     * @param  string  $sortKey
     * @return $this
     */
    public function sortKey($sortKey)
    {
        $this->sortKey = $sortKey;

        return $this;
    }

    /**
     * Get the query parameter to identify the sort to apply.
     *
     * @return string
     */
    public function getSortKey()
    {
        return $this->sortKey ?? static::$useSortKey;
    }

    /**
     * Determine if there is a sort being applied.
     *
     * @return bool
     */
    public function isSorting()
    {
        return (bool) Arr::first(
            $this->getSorts(),
            static fn (Sort $sort) => $sort->isActive()
        );
    }

    /**
     * Get the default sort.
     *
     * @return Sort|null
     */
    public function getDefaultSort()
    {
        return Arr::first(
            $this->getSorts(),
            static fn (Sort $sort) => $sort->isDefault()
        );
    }

    /**
     * Get the sorts as an array.
     *
     * @return array<int,array<string,mixed>>
     */
    public function sortsToArray()
    {
        return array_map(
            static fn (Sort $sort) => $sort->toArray(),
            $this->getSorts()
        );
    }
}
