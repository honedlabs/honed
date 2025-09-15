<?php

declare(strict_types=1);

namespace Honed\Honed\Concerns;

use Illuminate\Support\Facades\Date;

/**
 * @phpstan-require-extends \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>
 */
trait QueriesTimestamps
{
    /**
     * Scope the query to only be records after the given number of days.
     *
     * @return $this
     */
    public function after(int $days, ?string $column = null): static
    {
        $this->getQuery()->whereDate($column ?? $this->getTimestampColumn(), '>=', Date::now()->subDays($days));

        return $this;
    }

    /**
     * Scope the query to only be records before the given number of days.
     *
     * @return $this
     */
    public function before(int $days, ?string $column = null): static
    {
        $this->getQuery()->whereDate($column ?? $this->getTimestampColumn(), '<=', Date::now()->subDays($days));

        return $this;
    }

    /**
     * Scope the query to only be records from today.
     *
     * @return $this
     */
    public function today(?string $column = null): static
    {
        return $this->after(1, $column);

    }

    /**
     * Scope the query to only be records from the past week.
     *
     * @return $this
     */
    public function pastWeek(?string $column = null): static
    {
        return $this->after(7, $column);
    }

    /**
     * Scope the query to only be records from the past month.
     *
     * @return $this
     */
    public function pastMonth(?string $column = null): static
    {
        return $this->after(30, $column);
    }

    /**
     * Scope the query to only be records from the past quarter.
     *
     * @return $this
     */
    public function pastQuarter(?string $column = null): static
    {
        return $this->after(90, $column);
    }

    /**
     * Scope the query to only be records from the past year.
     *
     * @return $this
     */
    public function pastYear(?string $column = null): static
    {
        return $this->after(365, $column);
    }

    /**
     * Get the timestamp column.
     */
    protected function getTimestampColumn(): string
    {
        return $this->getModel()->getCreatedAtColumn() ?? 'created_at';
    }
}
