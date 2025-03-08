<?php

declare(strict_types=1);

namespace Honed\Refine;

use Honed\Core\Concerns\HasLabel;
use Honed\Core\Concerns\HasValue;
use Honed\Core\Concerns\IsActive;
use Honed\Core\Primitive;

/**
 * @extends Primitive<string, mixed>
 */
class Option extends Primitive
{
    use HasLabel;
    use HasValue;
    use IsActive;

    /**
     * Create a new option.
     *
     * @param  mixed  $value
     * @param  string|null  $label
     * @return static
     */
    public static function make($value, $label = null)
    {
        return resolve(static::class)
            ->value($value)
            ->label($label ?? (string) $value); // @phpstan-ignore-line
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return [
            'value' => $this->getValue(),
            'label' => $this->getLabel(),
            'active' => $this->isActive(),
        ];
    }
}
