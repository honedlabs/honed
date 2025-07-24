<?php

declare(strict_types=1);

namespace Honed\Honed\Concerns;

use Illuminate\Support\Str;

use function array_map;

/**
 * @phpstan-require-implements \BackedEnum
 */
trait Resourceful
{
    /**
     * Create a resource from the enum.
     *
     * @return array<int, array{value: mixed, label: string}>
     */
    public static function resource(): array
    {
        return array_map(static fn (self $case) => $case->toResource(),
            array_values(
                array_filter(
                    static::cases(),
                    static fn (self $case) => $case->allow()
                )
            )
        );
    }

    /**
     * Convert the enum to a resource.
     *
     * @return array{value: mixed, label: string}
     */
    public function toResource(): array
    {
        return [
            'value' => $this->value(),
            'label' => $this->label(),
        ];
    }

    /**
     * Whether to allow the enum to be used as a resource.
     */
    public function allow(): bool
    {
        return true;
    }

    /**
     * Get the value of the enum.
     */
    public function value(): mixed
    {
        return $this->value;
    }

    /**
     * Create a label for the enum value.
     */
    public function label(): string
    {
        return Str::of($this->name)
            ->snake()
            ->ucfirst()
            ->replace('_', ' ')
            ->value();
    }
}
