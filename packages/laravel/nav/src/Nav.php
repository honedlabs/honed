<?php

declare(strict_types=1);

namespace Honed\Nav;

use Inertia\Inertia;

class Nav
{
    const ShareProp = 'nav';

    /**
     * @var array<string, array<int,\Honed\Nav\NavItem|\Honed\Nav\NavGroup>>
     */
    protected $items = [];

    /**
     * Configure a new navigation group.
     * 
     * @return $this
     */
    public function make(string $group, NavItem|NavGroup ...$items): static
    {
        $this->items[$group] = \array_is_list($items) 
            ? $items 
            : \array_values($items);

        return $this;
    }

    /**
     * Append a navigation item to the provided group.  
     * 
     * @return $this
     */
    public function add(string $group, NavItem|NavGroup ...$items): static
    {
        $items = \array_is_list($items) 
            ? $items 
            : \array_values($items);

        \array_push($this->items[$group], ...$items);

        return $this;
    }

    /**
     * Retrieve the navigation item and groups associated with the provided group(s).
     * 
     * @return array<string|int,mixed>
     */
    public function get(string ...$groups): array
    {
        return match (\count($groups)) {
            0 => $this->items,
            1 => $this->items[$groups[0]] ?? [],
            default => \array_filter(
                $this->items, 
                fn ($key) => \in_array($key, $groups), 
                \ARRAY_FILTER_USE_KEY
            ),
        };
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

    /**
     * Determine if the provided group(s) have navigation defined.
     * 
     * @return bool
     */
    public function hasGroups(string ...$groups): bool
    {
        return \count(\array_intersect($groups, \array_keys($this->items))) > 0;
    }

    /**
     * Share the navigation items via Inertia.
     */
    public function share(string ...$groups): static
    {
        Inertia::share([
            self::ShareProp => $this->get(...$groups),
        ]);

        return $this;
    }

}
