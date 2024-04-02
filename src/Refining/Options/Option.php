<?php

namespace Jdw5\Vanguard\Refining\Options;

use Jdw5\Vanguard\Concerns\Configurable;
use Jdw5\Vanguard\Concerns\HasLabel;
use Jdw5\Vanguard\Concerns\HasMetadata;
use Jdw5\Vanguard\Concerns\IsActive;
use Jdw5\Vanguard\Concerns\IsIncludable;
use Jdw5\Vanguard\Primitive;
use Jdw5\Vanguard\Refining\Concerns\HasValue;
use Illuminate\Database\Eloquent\Collection;

class Option extends Primitive
{
    use HasLabel;
    use HasValue;
    use HasMetadata;
    use Configurable;
    use IsActive;
    use IsIncludable;

    public function __construct(mixed $value, ?string $label = null) { 
        dd($value);
        $this->value(str($value)->replace('.', '_'));
        $this->label($label ?? str($this->getValue())->headline()->lower()->ucfirst());
    }
    

    public static function make(string $value, string $label = null): static
    {
        return resolve(static::class, compact('value', 'label'));
    }

    public static function collection(Collection $collection, string|callable $asValue = 'value', string|callable $asLabel = null): array
    {
        return $collection->map(function ($item) use ($asValue, $asLabel) {
            $value = is_callable($asValue) ? $asValue($item) : $item[$asValue];
            $label = is_callable($asLabel) ? $asLabel($item) : (\is_null($asLabel) ? null : $item[$asLabel]);
            return static::make($value, $label);
        })->toArray();
    }

    public static function array(array $array, string|callable $asValue = 'value', string|callable $asLabel = null): array
    {
        return Option::collection(collect($array), $asValue, $asLabel);
    }

    public static function enum(string $enum, string|callable $asLabel = null): array
    {
        return collect($enum::cases())->map(function (\BackedEnum $item) use ($asLabel) {
            $label = is_callable($asLabel) ? $asLabel($item) : (\is_null($asLabel) ? null : $item->{$asLabel}());
            return static::make($item->value, $label);
        })->toArray();
    }


    public function jsonSerialize(): array
    {
        return [
            'label' => $this->getLabel(),
            'value' => $this->getValue(),
            'metadata' => $this->getMetadata(),
            'active' => $this->isActive(),
        ];
    }
}