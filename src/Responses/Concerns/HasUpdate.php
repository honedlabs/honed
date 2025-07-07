<?php

declare(strict_types=1);

namespace Honed\Honed\Responses\Concerns;

trait HasUpdate
{
    public const UPDATE_PROP = 'update';

    /**
     * The route to update the model.
     *
     * @var string
     */
    protected $update;

    /**
     * Set the route to update the model.
     *
     * @return $this
     */
    public function update(string $value): static
    {
        $this->update = $value;

        return $this;
    }

    /**
     * Get the route to update the model.
     */
    public function getUpdate(): string
    {
        return $this->update;
    }

    /**
     * Convert the table to an array of props.
     *
     * @return array<string, mixed>
     */
    public function hasUpdateToProps(): array
    {
        return [
            self::UPDATE_PROP => $this->getUpdate(),
        ];
    }
}
