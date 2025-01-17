<?php

namespace Honed\Table\Sorts\Concerns;

trait HasDirection
{
    public const Ascending = 'asc';

    public const Descending = 'desc';

    /**
     * @var string|null
     */
    protected $direction = null;

    /**
     * @var string|null
     */
    protected $activeDirection = null;

    /**
     * @var string
     */
    protected static $defaultDirection = self::Ascending;

    /**
     * Set the default direction to ascending.
     */
    public static function sortByAscending(): void
    {
        static::$defaultDirection = self::Ascending;
    }

    /**
     * Set the default direction to descending.
     */
    public static function sortByDescending(): void
    {
        static::$defaultDirection = self::Descending;
    }

    /**
     * Get the default direction.
     */
    public static function getDefaultDirection(): string
    {
        return static::$defaultDirection;
    }

    /**
     * Set the direction, chainable.
     *
     * @return $this
     */
    public function direction(?string $direction): static
    {
        $this->setDirection($direction);

        return $this;
    }

    /**
     * Allow for the query parameters to determine the direction.
     *
     * @return $this
     */
    public function agnostic(): static
    {
        return $this->direction(null);
    }

    /**
     * Set the direction quietly.
     */
    public function setDirection(?string $direction): void
    {
        if (! \in_array($direction, [null, self::Ascending, self::Descending])) {
            return;
        }

        $this->direction = $direction;
    }

    /**
     * Get the direction
     */
    public function getDirection(): ?string
    {
        return $this->direction;
    }

    /**
     * Determine if the direction is set
     */
    public function hasDirection(): bool
    {
        return ! \is_null($this->direction);
    }

    /**
     * Determine if the direction is agnostic (not set).
     */
    public function isAgnostic(): bool
    {
        return ! $this->hasDirection();
    }

    /**
     * Set the direction to be descending
     *
     * @return $this
     */
    public function desc(): static
    {
        return $this->direction(self::Descending);
    }

    /**
     * Set the direction to be ascending
     *
     * @return $this
     */
    public function asc(): static
    {
        return $this->direction(self::Ascending);
    }

    /**
     * Set the active direction.
     */
    public function setActiveDirection(?string $direction): void
    {
        if (! \in_array($direction, [null, self::Ascending, self::Descending])) {
            return;
        }

        $this->activeDirection = $direction;
    }

    /**
     * Get the active direction
     *
     * @return 'asc'|'desc'|null
     */
    public function getActiveDirection(): string|null
    {
        return $this->activeDirection;
    }
}
