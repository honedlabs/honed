<?php

declare(strict_types=1);

namespace Honed\Honed\Concerns;

/**
 * @template T
 */
trait Instantiable
{
    /**
     * The instance of the class.
     *
     * @var T|null
     */
    protected $instance;

    /**
     * The class to instantiate.
     *
     * @return class-string<T>
     */
    abstract public function instantiable(): string;

    /**
     * Create a new instance of the class.
     *
     * @return T
     */
    public function instance(mixed ...$arguments): mixed
    {
        return $this->instance ??= new ($this->instantiable())(...$arguments);
    }
}
