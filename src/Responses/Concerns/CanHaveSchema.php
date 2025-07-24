<?php

declare(strict_types=1);

namespace Honed\Honed\Responses\Concerns;

trait CanHaveSchema
{
    /**
     * The schema to be used to template form data.
     *
     * @var class-string<\Spatie\LaravelData\Data>|null
     */
    protected $schema;

    /**
     * The defaults to use in the schema.
     *
     * @var array<string, mixed>
     */
    protected $defaults = [];

    /**
     * Set the schema to be used to template form data.
     *
     * @param  class-string<\Spatie\LaravelData\Data>|null  $value
     * @param  array<string, mixed>  $defaults
     * @return $this
     */
    public function schema(?string $value, array $defaults = []): static
    {
        $this->schema = $value;
        $this->defaults = $defaults;

        return $this;
    }

    /**
     * Set the defaults to use in the schema.
     *
     * @param  array<string, mixed>  $value
     * @return $this
     */
    public function defaults(array $value): static
    {
        $this->defaults = [...$this->defaults, ...$value];

        return $this;
    }

    /**
     * Get the schema to be used to template form data.
     *
     * @return class-string<\Spatie\LaravelData\Data>|null
     */
    public function getSchema(): ?string
    {
        return $this->schema;
    }

    /**
     * Get the defaults to use in the schema.
     *
     * @return array<string, mixed>
     */
    public function getDefaults(): array
    {
        return $this->defaults;
    }

    /**
     * Convert the schema to an array of props.
     *
     * @return array<string, array<string, mixed>>
     */
    public function canHaveSchemaToProps(): array
    {
        $schema = $this->getSchema();

        if ($schema) {
            return [
                'schema' => $schema::empty($this->getDefaults()),
            ];
        }

        return [];
    }
}
