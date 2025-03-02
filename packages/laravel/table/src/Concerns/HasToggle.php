<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Honed\Table\Columns\Column;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cookie;
use Honed\Table\Contracts\ShouldRemember;
use Honed\Table\Contracts\ShouldToggle;

trait HasToggle
{
    /**
     * Whether the table should allow the user to toggle which columns are 
     * displayed.
     *
     * @var bool|null
     */
    protected $toggleable;

    /**
     * The query parameter for which columns to display.
     *
     * @var string|null
     */
    protected $columnsKey;

    /**
     * Whether the table should remember the columns to display.
     *
     * @var bool|null
     */
    protected $remember;

    /**
     * The name of the cookie to use for remembering the columns to display.
     *
     * @var string|null
     */
    protected $cookieName;

    /**
     * The duration of the cookie to use for remembering the columns to display.
     *
     * @var int|null
     */
    protected $duration;

    /**
     * Determine whether the table should allow the user to toggle which columns
     * are visible.
     * 
     * @return bool
     */
    public function isToggleable()
    {
        if (isset($this->toggle)) {
            return $this->toggle;
        }

        if ($this instanceof ShouldToggle) {
            return true;
        }

        return $this->fallbackToggleable();
    }
    
    /**
     * Determine whether the table should allow the user to toggle which columns
     * are visible.
     *  
     * @return bool
     */
    protected function fallbackToggleable()
    {
        return (bool) config('table.toggle.enabled', false);
    }

    /**
     * Set the query parameter for which columns to display.
     *
     * @return $this
     */
    public function columnsKey(string $columnsKey): static
    {
        $this->columnsKey = $columnsKey;

        return $this;
    }

    /**
     * Get the query parameter for which columns to display.
     * 
     * @return string
     */
    public function getColumnsKey()
    {
        if (isset($this->columnsKey)) {
            return $this->columnsKey;
        }

        return $this->fallbackColumnsKey();
    }

    /**
     * Get the query parameter for which columns to display.
     * 
     * @return string
     */
    protected function fallbackColumnsKey()
    {
        return type(config('table.config.columns', 'columns'))->asString();
    }

    /**
     * Determine whether the table should remember the user preferences.
     * 
     * @return bool
     */
    public function isRememberable()
    {
        if (isset($this->remember)) {
            return $this->remember;
        }

        if ($this instanceof ShouldRemember) {
            return true;
        }

        return $this->fallbackRememberable();
    }

    /**
     * Determine whether the table should remember the user preferences.
     * 
     * @return bool
     */
    protected function fallbackRememberable()
    {
        return (bool) config('table.toggle.remember', false);
    }

    /**
     * Get the cookie name to use for the table toggle.
     * 
     * @return string
     */
    public function getCookieName()
    {
        if (isset($this->cookieName)) {
            return $this->cookieName;
        }

        return $this->guessCookieName();
    }

    /**
     * Guess the name of the cookie to use for remembering the columns to
     * display.
     * 
     * @return string
     */
    public function guessCookieName()
    {
        return str(static::class)
            ->classBasename()
            ->kebab()
            ->lower()
            ->toString();
    }

    /**
     * Get the duration of the cookie to use for remembering the columns to
     * display.
     * 
     * @return int
     */
    public function getDuration()
    {
        if (isset($this->duration)) {
            return $this->duration;
        }

        return $this->fallbackDuration();
    }

    /**
     * Get the duration of the cookie to use for remembering the columns to
     * display.
     * 
     * @return int
     */
    protected function fallbackDuration()
    {
        return type(config('table.toggle.duration', 15768000))->asInt();
    }

    /**
     * Use the columns cookie to determine which columns are active, or set the
     * cookie to the current columns.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array<int,string>|null  $params
     * @return array<int,string>|null
     */
    public function configureCookie($request, $params)
    {
        if (filled($params)) {
            $this->enqueueCookie($params);
            return $params;
        }

        return $this->dequeueCookie($request, $params);
    }

    /**
     * Enqueue a new cookie with preference data.
     * 
     * @param array<int,string> $params
     * @return void
     */
    protected function enqueueCookie($params)
    {
        Cookie::queue(
            $this->getCookieName(),
            \json_encode($params),
            $this->getDuration()
        );
    }

    /**
     * Retrieve the preference data from the cookie if it exists.
     * 
     * @param \Illuminate\Http\Request $request
     * @param array<int,string>|null $params
     * @return array<int,string>|null
     */
    protected function dequeueCookie($request, $params)
    {
        $value = $request->cookie($this->getCookieName(), null);

        if (! \is_string($value)) {
            return $params;
        }

        /** @var array<int,string>|null */
        return \json_decode($value, false);
    }

    /**
     * Toggle the columns that are displayed.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array<int,\Honed\Table\Columns\Column>  $columns
     * @return array<int,\Honed\Table\Columns\Column>
     */
    public function toggleColumns($request, $columns)
    {
        if (! $this->isToggleable()) {
            return $columns;
        }

        $key = $this->formatScope($this->getColumnsKey());
        $params = $request->safeArray($key);

        $params = $params?->isEmpty()
            ? null
            : $params->toArray();

        if ($this->isRememberable()) {
            $params = $this->configureCookie($request, $params);
        }

        return collect($columns)
            ->filter(fn (Column $column) => $column->isDisplayed($params))
            ->values()
            ->all();
    }
}
