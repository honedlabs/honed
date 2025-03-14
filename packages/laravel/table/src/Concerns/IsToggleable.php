<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Honed\Core\Concerns\InterpretsRequest;
use Honed\Table\Columns\Column;
use Honed\Table\Contracts\ShouldRemember;
use Honed\Table\Contracts\ShouldToggle;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

trait IsToggleable
{
    /**
     * Whether the table should allow for toggling which columns are visible.
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
    protected $rememberable;

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
     * Whether the displayed columns should not be toggled.
     *
     * @var bool
     */
    protected $withoutToggling = false;

    /**
     * Set whether the table should allow the user to toggle which columns are
     * displayed.
     *
     * @param  bool  $toggleable
     * @return $this
     */
    public function toggleable($toggleable = true)
    {
        $this->toggleable = $toggleable;

        return $this;
    }

    /**
     * Determine whether the table should allow the user to toggle which columns
     * are visible.
     *
     * @return bool
     */
    public function isToggleable()
    {
        if (isset($this->toggleable)) {
            return $this->toggleable;
        }

        if ($this instanceof ShouldToggle) {
            return true;
        }

        return static::fallbackToggleable();
    }

    /**
     * Determine whether the table should allow the user to toggle which columns
     * are visible from the config.
     *
     * @return bool
     */
    public static function fallbackToggleable()
    {
        return (bool) config('table.toggle', false);
    }

    /**
     * Set the query parameter for which columns to display.
     *
     * @param  string  $columnsKey
     * @return $this
     */
    public function columnsKey($columnsKey): static
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
        return $this->columnsKey ?? static::fallbackColumnsKey();
    }

    /**
     * Get the query parameter for which columns to display from the config.
     *
     * @return string
     */
    public static function fallbackColumnsKey()
    {
        return type(config('table.columns_key', 'columns'))->asString();
    }

    /**
     * Set whether the table should remember the user preferences.
     *
     * @param  bool  $rememberable
     * @return $this
     */
    public function rememberable($rememberable = true)
    {
        $this->rememberable = $rememberable;

        return $this;
    }

    /**
     * Determine whether the table should remember the user preferences.
     *
     * @return bool
     */
    public function isRememberable()
    {
        if (isset($this->rememberable)) {
            return (bool) $this->rememberable;
        }

        if ($this instanceof ShouldRemember) {
            return true;
        }

        return static::fallbackRememberable();
    }

    /**
     * Determine whether the table should remember the user preferences from
     * the config.
     *
     * @return bool
     */
    public static function fallbackRememberable()
    {
        return (bool) config('table.remember', false);
    }

    /**
     * Get the cookie name to use for the table toggle.
     *
     * @return string
     */
    public function getCookieName()
    {
        return $this->cookieName ?? static::guessCookieName();
    }

    /**
     * Set the cookie name to use for the table toggle.
     *
     * @param  string  $cookieName
     * @return $this
     */
    public function cookieName($cookieName)
    {
        $this->cookieName = $cookieName;

        return $this;
    }

    /**
     * Guess the name of the cookie to use for remembering the columns to
     * display.
     *
     * @return string
     */
    public static function guessCookieName()
    {
        return Str::of(static::class)
            ->classBasename()
            ->kebab()
            ->lower()
            ->toString();
    }

    /**
     * Set the duration of the cookie to use for remembering the columns to
     * display.
     *
     * @param  int  $seconds
     * @return $this
     */
    public function duration($seconds)
    {
        $this->duration = $seconds;

        return $this;
    }

    /**
     * Get the duration of the cookie to use for remembering the columns to
     * display.
     *
     * @return int
     */
    public function getDuration()
    {
        return $this->duration ?? static::fallbackDuration();
    }

    /**
     * Get the duration of the cookie to use for remembering the columns to
     * display from the config.
     *
     * @return int
     */
    public static function fallbackDuration()
    {
        return type(config('table.duration', 15768000))->asInt();
    }

    /**
     * Set the columns to not be toggled.
     *
     * @return $this
     */
    public function withoutToggling()
    {
        $this->withoutToggling = true;

        return $this;
    }

    /**
     * Determine if the columns should not be toggled.
     *
     * @return bool
     */
    public function isWithoutToggling()
    {
        return $this->withoutToggling;
    }

    /**
     * Get the columns that are displayed.
     *
     * @param  array<int,\Honed\Table\Columns\Column>  $columns
     * @param  array<int,string>|null  $params
     * @return array<int,\Honed\Table\Columns\Column>
     */
    public static function displayedColumns($columns, $params = null)
    {
        return \array_values(
            \array_filter(
                $columns,
                static fn (Column $column) => $column->display($params)
            )
        );
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
            Cookie::queue(
                $this->getCookieName(),
                \json_encode($params),
                $this->getDuration()
            );

            return $params;
        }

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
    public function toggle($request, $columns)
    {
        if (! $this->isToggleable() || $this->isWithoutToggling()) {
            return static::displayedColumns($columns);
        }

        $interpreter = new class { use InterpretsRequest; };

        $key = $this->getColumnsKey();
        $delimiter = $this->getDelimiter();

        /** @var array<int,string>|null */
        $params = $interpreter->interpretArray($request, $key, $delimiter);

        if ($this->isRememberable()) {
            $params = $this->configureCookie($request, $params);
        }

        return static::displayedColumns($columns, $params);
    }
}
