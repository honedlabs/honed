<?php

namespace Vanguard\Core\Concerns;

use Closure;

trait HasDescription
{
    protected string|Closure $description;

    /**
     * Set the description, chainable.
     * 
     * @param string|Closure $description
     * @return static
     */
    public function description(string|Closure $description): static
    {
        $this->setDescription($description);
        return $this;
    }

    /**
     * Set the description quietly.
     * 
     * @param string|Closure $description
     * @return void
     */
    protected function setDescription(string|Closure $description): void
    {
        $this->description = $description;
    }

    /**
     * Get the description
     * 
     * @return string|Closure
     */
    public function getdescription(): string
    {
        return $this->evaluate($this->description);
    }
}
