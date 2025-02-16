<?php

declare(strict_types=1);

namespace Honed\Crumb;

use Honed\Core\Primitive;
use Honed\Crumb\Exceptions\NonTerminatingCrumbException;
use Inertia\Inertia;

class Trail extends Primitive
{
    use Concerns\IsTerminable;

    /**
     * @var array<int,\Honed\Crumb\Crumb>
     */
    protected $crumbs;

    /**
     * Create a new trail instance.
     */
    public function __construct(Crumb ...$crumbs)
    {
        $this->crumbs = \array_values($crumbs);
    }

    /**
     * Make a new trail instance.
     */
    public static function make(Crumb ...$crumbs): self
    {
        return resolve(static::class, ['crumbs' => $crumbs]);
    }

    /**
     * Get the trail as an array.
     *
     * @return array<int,\Honed\Crumb\Crumb>
     */
    public function toArray(): array
    {
        return $this->crumbs();
    }

    /**
     * Append crumbs to the end of the crumb trail.
     *
     * @param  string|\Honed\Crumb\Crumb|(\Closure(mixed...):string)  $crumb
     * @param  string|(\Closure(mixed...):string)|null  $link
     * @return $this
     */
    public function add(string|\Closure|Crumb $crumb, string|\Closure|null $link = null, ?string $icon = null): static
    {
        if (! $this->isTerminated()) {
            $crumb = $crumb instanceof Crumb ? $crumb : Crumb::make($crumb, $link, $icon);
            $this->crumbs[] = $crumb;
            $this->terminated = $this->isTerminating() && $crumb->isCurrent();
        }

        return $this;
    }

    /**
     * Select and add the first matching crumb to the trail.
     *
     * @return $this
     *
     * @throws NonTerminatingCrumbException
     */
    public function select(Crumb ...$crumbs): static
    {
        if ($this->isTerminated()) {
            return $this;
        }

        if (! $this->isTerminating()) {
            throw new NonTerminatingCrumbException;
        }

        $crumb = collect($crumbs)->first(fn (Crumb $crumb): bool => $crumb->isCurrent());

        if ($crumb) {
            $this->crumbs[] = $crumb;
            $this->terminated = true;
        }

        return $this;
    }

    /**
     * Retrieve the crumbs in the crumb trail.
     *
     * @return array<int,\Honed\Crumb\Crumb>
     */
    public function crumbs(): array
    {
        return $this->crumbs;
    }

    /**
     * Determine if the crumb trail has crumbs.
     */
    public function hasCrumbs(): bool
    {
        return filled($this->crumbs());
    }

    /**
     * Share the crumbs with Inertia.
     *
     * @return $this
     */
    public function share(): static
    {
        Inertia::share('crumbs', $this->crumbs());

        return $this;
    }
}
