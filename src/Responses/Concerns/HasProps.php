<?php

declare(strict_types=1);

namespace Honed\Honed\Responses\Concerns;

trait HasProps
{
    /**
     * The props for the view.
     *
     * @var array<string, mixed>
     */
    protected $props = [];

    /**
     * The array of trait initializers that will be used to create props.
     *
     * @var array<class-string, array<int, string>>
     */
    protected static $traitInitializers = [];

    /**
     * Set the props for the view.
     *
     * @param  string|array<string, mixed>  $props
     * @return $this
     */
    public function props(string|array $props, mixed $value = null): static
    {
        if (is_array($props)) {
            $this->props = [...$this->props, ...$props];
        } else {
            $this->props[$props] = $value;
        }

        return $this;
    }

    /**
     * Set a prop for the view.
     *
     * @param  string  $prop
     * @param  mixed  $value
     * @return $this
     */
    public function prop(string $prop, mixed $value): static
    {
        return $this->props($prop, $value);
    }

    /**
     * Set the props for the view.
     *
     * @param  string|array<string, mixed>  $props
     * @return $this
     */
    public function with(string|array $props, mixed $value = null): static
    {
        return $this->props($props, $value);
    }

    /**
     * Get the props for the view.
     *
     * @return array<string, mixed>
     */
    public function getProps(): array
    {
        return $this->props;
    }

    /**
     * Generate the props for the view.
     *
     * @return array<string, mixed>
     */
    public function toProps(): array
    {
        $this->bootProps();

        return $this->getProps();
    }

    /**
     * Boot the traits being used by the class.
     */
    protected function bootTraits(): void
    {
        $class = static::class;

        static::$traitInitializers[$class] = [];

        foreach (class_uses_recursive($class) as $trait) {
            $method = lcfirst(class_basename($trait)).'ToProps';

            if (method_exists($class, $method)) {
                static::$traitInitializers[$class][] = $method;
            }
        }

        static::$traitInitializers[$class] = array_unique(
            static::$traitInitializers[$class]
        );
    }

    /**
     * Get the booted props for the view.
     */
    protected function bootProps(): void
    {
        $this->bootTraits();

        foreach (static::$traitInitializers[static::class] as $method) {
            $this->props($this->$method());
        }
    }
}
