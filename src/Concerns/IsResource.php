<?php

declare(strict_types=1);

namespace Honed\Honed\Concerns;

use Illuminate\Support\Str;

use function array_map;

trait IsResource
{
    /**
     * Create a resource from the enum.
     *
     * @return array<array-key, array{value: string, label: string|int}>
     */
    public static function toResource()
    {
        $cases = static::cases();

        return array_map(static fn ($case) => [
            'value' => $case->value,
            'label' => $case->label(),
        ], $cases);
    }

    /**
     * Create a label for the enum value.
     *
     * @return string
     */
    public function label()
    {
        return Str::of($this->name)
            ->headline()
            ->value();
    }
}
