<?php

declare(strict_types=1);

namespace Honed\Nav;

use Honed\Nav\Support\Parameters;
use Illuminate\Support\Arr;
use Inertia\Inertia;

class Nav
{
    /**
     * Keyed navigation groups.
     *
     * @var array<string, array<int,\Honed\Nav\NavBase>>
     */
    protected $items = [];

    /**
     * Set a navigation group under a given name.
     *
     * @param  string  $name
     * @param  array<int,\Honed\Nav\NavBase>  $items
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function for($name, $items)
    {
        if ($this->hasGroup($name)) {
            static::throwDuplicateGroupException($name);
        }

        /** @var array<int,\Honed\Nav\NavBase> $items */
        Arr::set($this->items, $name, $items);

        return $this;
    }

    /**
     * Add navigation items to an existing group.
     *
     * @param  string  $name
     * @param  array<int,\Honed\Nav\NavBase>  $items
     * @return $this
     */
    public function add($name, $items)
    {
        if (! $this->hasGroup($name)) {
            static::throwMissingGroupException($name);
        }

        /** @var array<int,\Honed\Nav\NavBase> $current */
        $current = Arr::get($this->items, $name);

        /** @var array<int,\Honed\Nav\NavBase> $items */
        $updated = \array_merge($current, $items);

        Arr::set($this->items, $name, $updated);

        return $this;
    }

    /**
     * Determine if one or more navigation groups exist.
     *
     * @param  string|array<int,string>  $groups
     * @return bool
     */
    public function hasGroup($groups)
    {
        $groups = Arr::wrap($groups);

        if (empty($groups)) {
            return true;
        }

        return empty(
            \array_diff(
                $groups,
                \array_keys($this->items)
            )
        );
    }

    /**
     * Retrieve navigation groups and their allowed items.
     *
     * @param  string|array<int,string>  $groups
     * @return array<string,array<int,\Honed\Nav\NavBase>>
     */
    public function get(...$groups)
    {
        $groups = Arr::flatten($groups);

        if (! $this->hasGroup($groups)) {
            static::throwMissingGroupException(implode(', ', $groups));
        }

        $keys = empty($groups) ? \array_keys($this->items) : $groups;

        return $this->getGroups($keys);
    }

    /**
     * Retrieve the navigation groups for the given keys.
     *
     * @param  array<int,string>  $keys
     * @return array<string,array<int,\Honed\Nav\NavBase>>
     */
    protected function getGroups($keys)
    {
        return \array_reduce(
            $keys,
            fn (array $acc, string $key) => $acc + [$key => $this->getGroup($key)],
            []
        );
    }

    /**
     * Retrieve the navigation group for the given name.
     *
     * @param  string  $group
     * @return array<int,\Honed\Nav\NavBase>
     */
    public function getGroup($group)
    {
        /** @var array<int,\Honed\Nav\NavBase> */
        $items = Arr::get($this->items, $group);

        return \array_values(
            \array_filter($items,
                fn (NavBase $item) => $item->isAllowed()
            )
        );
    }

    /**
     * Get the navigation items as an array.
     *
     * @param  string|array<int,string>  $groups
     * @return array<string,array<int,array<string,mixed>>>
     */
    public function getToArray(...$groups)
    {
        $groups = $this->get(...$groups);

        return \array_map(
            fn ($group) => \array_map(
                fn (NavBase $item) => $item->toArray(),
                $group
            ),
            $groups
        );
    }

    /**
     * Share the navigation items with Inertia.
     *
     * @param  string|array<int,string>  $groups
     * @return $this
     */
    public function share(...$groups)
    {
        Inertia::share(
            Parameters::Prop,
            $this->getToArray(...$groups)
        );

        return $this;
    }

    /**
     * Throw an exception for a duplicate group.
     *
     * @param  string  $group
     * @return never
     */
    protected static function throwDuplicateGroupException($group)
    {
        throw new \InvalidArgumentException(
            \sprintf('There already exists a group with the name [%s].', $group)
        );
    }

    /**
     * Throw an exception for a missing group.
     *
     * @param  string  $group
     * @return never
     */
    protected static function throwMissingGroupException($group)
    {
        throw new \InvalidArgumentException(
            \sprintf('There is no group with the name [%s].', $group)
        );
    }
}
