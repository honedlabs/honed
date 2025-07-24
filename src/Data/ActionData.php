<?php

declare(strict_types=1);

namespace Honed\Honed\Data;

use Honed\Honed\Concerns\ValidatesIdentifiers;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

/**
 * @property array<int, int|string>|Optional $only
 * @property array<int, int|string>|Optional $except
 */
class ActionData extends Data
{
    use ValidatesIdentifiers;

    /**
     * @param  array<int, int|string>|Optional  $only
     * @param  array<int, int|string>|Optional  $except
     */
    public function __construct(
        public bool|Optional $all,
        public array|Optional $only,
        public array|Optional $except,
        public int|string|Optional $id,
    ) {}

    /**
     * Get the validation rules for the data.
     *
     * @return array<string, array<int,mixed>>
     */
    public static function rules(): array
    {
        return [
            'all' => ['sometimes', 'boolean'],
            'only' => ['bail', 'exclude_without:all', 'present', 'array', 'list', static::each(...)],
            'except' => ['bail', 'exclude_without:all', 'present', 'array', 'list', static::each(...)],
            'id' => ['bail', 'required_without:all', 'alpha_num'],
        ];
    }

    /**
     * Get the IDs of the selected records.
     *
     * @return array<int, int|string>
     */
    public function ids(): array
    {
        return match (true) {
            ! $this->id instanceof Optional => [$this->id],
            ! $this->only instanceof Optional => $this->only,
            default => [],
        };
    }
}
