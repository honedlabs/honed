<?php

declare(strict_types=1);

namespace Honed\Honed\Data;

use Honed\Honed\Concerns\ValidatesIdentifiers;
use Spatie\LaravelData\Data;

/**
 * @property array<int, int|string> $only
 * @property array<int, int|string> $except
 */
class BulkData extends Data
{
    use ValidatesIdentifiers;

    /**
     * @param  array<int, int|string>  $only
     * @param  array<int, int|string>  $except
     */
    public function __construct(
        public bool $all,
        public array $only,
        public array $except,
    ) {}

    /**
     * Get the validation rules for the data.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<int,mixed>|string>
     */
    public static function rules(): array
    {
        return [
            'all' => ['required', 'boolean'],
            'only' => ['bail', 'required', 'list', static::each(...)],
            'except' => ['bail', 'required', 'list', static::each(...)],
        ];
    }
}
