<?php

declare(strict_types=1);

namespace Honed\Nav;

use Inertia\Inertia;
use Illuminate\Support\Collection;

class Navigation
{
    const ShareProp = 'nav';

    /**
     * @var array<string, array<\Honed\Nav\NavItem|\Honed\Nav\NavGroup>>
     */
    protected $items = [];

    /**
     * Configure a new navigation group.
     * 
     * @param  array<int,\Honed\Nav\NavItem>|\Honed\Nav\NavItem  $items
     * 
     * @return $this
     */
    public function make(string $group, ...$items): static
    {
        $this->items[$group] = $items;

        return $this;
    }

    /**
     * Append a navigation item to the provided group.  
     * 
     * @param  array<int,\Honed\Nav\NavItem>|\Honed\Nav\NavItem  $item
     * 
     * @return $this
     */
    public function add(string $group, ...$item): static
    {
        $this->items[$group][] = $item;

        return $this;
    }

    /**
     * Retrieve the navigation item and groups associated with the provided group(s).
     * 
     * @param  array<int,string>|null  $groups
     * 
     * @return array<string, array<\Honed\Nav\NavItem|\Honed\Nav\NavGroup>>
     */
    public function get(...$groups = null)
    {
        if (\is_null($groups)) {
            return $this->items;
        }

        if (\count($groups) === 1) {
            return $this->items[\reset($groups)] ?? [];
        }

        return \array_filter(
            $this->items, 
            fn (string $key) => \in_array($key, $groups), 
            \ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * Retrieve the navigation items associated with the provided group.
     * 
     * @return array<\Honed\Nav\NavItem|\Honed\Nav\NavGroup>
     */
    public function group(string $group)
    {
        return $this->items[$group] ?? [];
    }

    public function share(...$groups)
    {
        Inertia::share([
            self::ShareProp => $this->get(...$groups),
        ]);
    }

}
