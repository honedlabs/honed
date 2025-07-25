<?php

declare(strict_types=1);

namespace Honed\Honed\Data;

use Spatie\LaravelData\Data;

class InlineData extends Data
{
    public function __construct(
        public int|string $id,
    ) {}

    /**
     * Get the validation rules for the data.
     *
     * @return array<string, array<int,mixed>>
     */
    public static function rules(): array
    {
        return [
            'id' => ['required', 'alpha_num'],
        ];
    }
}
