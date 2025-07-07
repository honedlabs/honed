<?php

declare(strict_types=1);

namespace Honed\Honed\Responses\Concerns;

trait HasDestroy
{
    public const DESTROY_PROP = 'destroy';

    /**
     * The route to destroy the model.
     *
     * @var string
     */
    protected $destroy;

    /**
     * Set the route to destroy the model.
     *
     * @return $this
     */
    public function destroy(string $value): static
    {
        $this->destroy = $value;

        return $this;
    }

    /**
     * Get the route to destroy the model.
     */
    public function getDestroy(): string
    {
        return $this->destroy;
    }

    /**
     * Convert the table to an array of props.
     *
     * @return array<string, mixed>
     */
    public function hasDestroyToProps(): array
    {
        return [
            self::DESTROY_PROP => $this->getDestroy(),
        ];
    }
}
