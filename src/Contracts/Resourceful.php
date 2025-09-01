<?php

declare(strict_types=1);

namespace Honed\Honed\Contracts;

interface Resourceful
{
    /**
     * Get the value of the enum.
     */
    public function value(): mixed;

    /**
     * Get the label
     */
    public function label(): string;
}
