<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Honed\Table\Columns\BaseColumn;
use Honed\Table\Columns\Column;
use Honed\Table\Contracts\ShouldRemember;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cookie;

trait HasToggle
{
    const Duration = 60 * 24 * 30 * 365; // 1 year
    const ColumnsKey = 'columns';

    /**
     * @var bool|null
     */
    protected $toggle;

    /**
     * @var bool|null
     */
    protected $remember;

    /**
     * @var string|null
     */
    protected $cookie;

    /**
     * @var int|null
     */
    protected $duration;

    /**
     * @var string|null
     */
    protected $columnsKey;
    
    /**
     * Determine whether this table has toggling of the columns enabled.
     */
    public function isToggleable(): bool
    {
        if (\property_exists($this, 'toggle') && ! \is_null($this->toggle)) {
            return $this->toggle;
        }

        return false;
    }

    /**
     * Determine whether this table has toggling of the columns enabled.
     */
    public function isRemembering(): bool
    {
        if (\property_exists($this, 'remember') && ! \is_null($this->remember)) {
            return $this->remember;
        }

        if ($this instanceof ShouldRemember) {
            return true;
        }

        return false;
    }

    /**
     * Get the cookie name to use for the table toggle.
     */
    public function getCookie(): string
    {
        if (\property_exists($this, 'cookie') && ! \is_null($this->cookie)) {
            return $this->cookie;
        }

        return str(static::class)
            ->classBasename()
            ->append('Table')
            ->kebab()
            ->lower()
            ->toString();
    }

    /**
     * Get the default duration of the cookie to use for the table toggle.
     */
    public function getDuration(): int
    {
        if (\property_exists($this, 'duration') && ! \is_null($this->duration)) {
            return $this->duration;
        }

        return self::Duration;
    }

    /**
     * Get the query parameter to use for toggling columns.
     */
    public function getColumnsKey(): string
    {
        if (\property_exists($this, 'columnsKey') && ! \is_null($this->columnsKey)) {
            return $this->columnsKey;
        }

        return self::ColumnsKey;
    }

    /**
     * Update the cookie with the new data.
     * 
     * @param mixed $data
     */
    public function enqueueCookie($data): void
    {
        Cookie::queue(
            $this->getCookieName(), 
            \json_encode($data), 
            $this->getRememberDuration()
        );
    }

    /**
     * Get the data stored in the cookie.
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function retrieveCookie($request = null): mixed
    {
        return \json_decode(
            ($request ?? request())->cookie($this->getCookieName(), '[]'),
            true
        );
    }

    /**
     * Resolve parameters using cookies if applicable.
     *
     * @param array<int,string>|null $params
     * @param \Illuminate\Http\Request|null $request
     * @return array<int,string>|null
     */
    protected function resolveCookieParams($params, $request): ?array
    {
        if (! \is_null($params)) {
            $this->enqueueCookie($params);
            return $params;
        }

        return $this->retrieveCookie($request);
    }

    /**
     * Get the columns to show.
     *
     * @return array<int,string>|null
     */
    public function toggleParameters(Request $request): ?array
    {
        $params = $request->string($this->getRememberName())
            ->trim()
            ->remove(' ')
            ->explode(',')
            ->filter(fn($param) => $param !== '') // Filter out empty strings
            ->toArray();

        return empty($params) ? null : $params;
    }

    /**
     * @param \Illuminate\Support\Collection<\Honed\Table\Columns\Contracts\Column> $columns
     * @return \Illuminate\Support\Collection<\Honed\Table\Columns\Contracts\Column>
     */
    public function toggleColumns(Collection $columns, Request $request = null)
    {
        if (! $this->isToggleable()) {
            // All columns are active by default using `setUp()`
            return $columns;
        }

        
        $params = $this->toggleParameters($request);
        
        if ($this->useCookie()) {
            $params = $this->resolveCookieParams($params, $request);
        }

        return $columns
            ->when(! \is_null($params),
                fn (Collection $columns) => $columns
                    ->filter(static fn (Column $column) => $column
                        ->active(!$column->isToggleable() || \in_array($column->getName(), $params))
                        ->isActive()
                    )->values()
            );
    }

    /**
     * @return array<int,string>|true
     */
    public function getColumnsFromRequest(Request $request): array|true
    {
        $matches = $request->string($this->getColumnsKey(), null);

        if ($matches->isEmpty()) {
            return true;
        }

        /** @var array<int,string> */
        return $matches
            ->explode(',', PHP_INT_MAX)
            ->map(fn ($v) => \trim($v))
            ->toArray();
    }
}
