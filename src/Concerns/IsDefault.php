<?php

namespace Conquest\Core\Concerns;

use Closure;

/**
 * Set whether a class is the default
 */
trait IsDefault
{
    protected bool|Closure $default = false;

    /**
     * Set the class as default, chainable
     */
    public function default(bool|Closure $default = true): static
    {
        $this->setDefault($default);

        return $this;
    }

    /**
     * Set the default quietly
     */
    public function setDefault(bool|Closure $default): void
    {
        if (is_null($default)) {
            return;
        }
        $this->default = $default;
    }

    /**
     * Check if the class is default
     */
    public function isDefault(): bool
    {
        return $this->evaluate($this->default);
    }

    /**
     * Check if the class is not default
     */
    public function isNotDefault(): bool
    {
        return ! $this->isDefault();
    }
}
