<?php

declare(strict_types=1);

namespace Honed\Honed\Data\Normalizers;

use Honed\Honed\Contracts\Resourceful;
use Spatie\LaravelData\Normalizers\Normalized\Normalized;
use Spatie\LaravelData\Normalizers\Normalizer;

class ResourcefulNormalizer implements Normalizer
{
    /**
     * Normalize a resourceful to an array.
     *
     * @return array{label: string, value: mixed}
     */
    public function normalize(mixed $value): null|array|Normalized
    {
        if (! $value instanceof Resourceful) {
            return null;
        }

        return [
            'label' => $value->label(),
            'value' => $value->value(),
        ];
    }
}
