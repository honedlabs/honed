<?php

declare(strict_types=1);

namespace Honed\Honed\Responses\Concerns;

trait HasStore
{
    public const STORE_PROP = 'store';

    /**
     * The route to store the model.
     *
     * @var string
     */
    protected $store;

    /**
     * Set the route to store the model.
     *
     * @return $this
     */
    public function store(string $value): static
    {
        $this->store = $value;

        return $this;
    }

    /**
     * Get the route to store the model.
     */
    public function getStore(): string
    {
        return $this->store;
    }

    /**
     * Convert the table to an array of props.
     *
     * @return array<string, mixed>
     */
    public function hasStoreToProps(): array
    {
        return [
            self::STORE_PROP => $this->getStore(),
        ];
    }
}
