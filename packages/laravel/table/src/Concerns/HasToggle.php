<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Honed\Table\Columns\Column;
use Honed\Table\Concerns\Keys\ColumnsKey;
use Illuminate\Support\Facades\Cookie;
use Honed\Table\Contracts\ShouldRemember;

trait HasToggle
{
    use ColumnsKey;

    /**
     * Whether the table should allow the user to toggle which columns are visible.
     * 
     * @var bool|null
     */
    protected $toggle;

    /**
     * Whether the table should remember the user's preferences for column visibility
     *  via a cookie.
     * 
     * @var bool|null
     */
    protected $remember;

    /**
     * The name of the cookie to use for remembering the user's preferences.
     * 
     * @var string|null
     */
    protected $cookie;

    /**
     * The duration of the cookie to use for remembering the user's preferences.
     * 
     * @var int|null
     */
    protected $duration;
    
    /**
     * Whether the table should allow the user to change the order of the columns.
     * 
     * @var bool|null
     */
    protected $order;

    /**
     * Determine whether this table allows for the user to toggle which
     * columns are visible.
     */
    public function hasToggle(): bool
    {
        if (isset($this->toggle)) {
            return $this->toggle;
        }

        /** @var bool */
        return config('table.toggle.enabled', false);
    }

    /**
     * Determine whether the user's preferences should be remembered.
     */
    public function hasRemember(): bool
    {
        if (isset($this->remember)) {
            return $this->remember;
        }

        if ($this instanceof ShouldRemember) {
            return true;
        }

        /** @var bool */
        return config('table.toggle.remember', false);
    }

    /**
     * Get the cookie name to use for the table toggle.
     */
    public function getCookie(): string
    {
        if (isset($this->cookie)) {
            return $this->cookie;
        }

        return $this->guessCookieName();
    }

    /**
     * Guess the name of the cookie to use for the table toggle.
     */
    public function guessCookieName(): string
    {
        return str(static::class)
            ->classBasename()
            ->append('Table')
            ->kebab()
            ->lower()
            ->toString();
    }

    /**
     * Get the duration of the cookie to use for how long the table should.
     * remember the user's preferences.
     */
    public function getDuration(): int
    {
        if (isset($this->duration)) {
            return $this->duration;
        }

        /** @var int */
        return config('table.toggle.remember.duration', 15768000);
    }

    /**
     * Determine whether the user can order the columns.
     */
    public function hasOrder(): bool
    {
        if (isset($this->order)) {
            return $this->order;
        }

        /** @var bool */
        return config('table.toggle.order', false);
    }

    /**
     * Toggle the columns that are visible.
     * 
     * @param  array<int,\Honed\Table\Columns\Column>  $columns
     * 
     * @return array<int,\Honed\Table\Columns\Column>
     */
    public function toggle(array $columns): array
    {
        if (! $this->hasToggle()) {
            return $columns;
        }

        /** @var \Illuminate\Http\Request */
        $request = $this->getRequest();
        
        $activeColumns = $this->getColumnsFromRequest($request);

        if ($this->hasRemember()) {
            $activeColumns = $this->configureCookie($request, $activeColumns);
        }

        return Arr::where(
            $columns,
            fn (Column $column) => $column
                ->active($column->isDisplayed($activeColumns))
                ->isActive()
        );
    }

    /**
     * Use the columns cookie to determine which columns are active, or set the
     * cookie to the current columns.
     * 
     * @param array<int,string>|null $params
     * 
     * @return array<int,string>|null
     */
    public function configureCookie(Request $request, ?array $params): ?array
    {
        if (\is_null($params)) {
            Cookie::queue($this->getCookie(), $params, $this->getDuration());
            return $params;
        }

        /** @var array<int,string>|null */
        return $request->cookie($this->getCookie());
    }

    /**
     * Retrieve the columns to display from the request.
     * 
     * @return array<int,string>|null
     */
    public function getColumnsFromRequest(Request $request): ?array
    {
        $matches = $request->string($this->getColumnsKey(), null);

        if ($matches->isEmpty()) {
            return null;
        }

        /** @var array<int,string> */
        return $matches
            ->explode(',')
            ->map(fn ($value) => trim($value))
            ->filter()
            ->toArray();
    }
}
