<?php

declare(strict_types=1);

namespace Honed\Honed\Attributes\Validation;

use Attribute;
use Spatie\LaravelData\Attributes\Validation\StringValidationAttribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class HexColor extends StringValidationAttribute
{
    public function __construct() {}

    /**
     * Get the keyword for the validation rule.
     */
    public static function keyword(): string
    {
        return 'hex_color';
    }

    /**
     * Get the parameters to pass to the validation rule.
     *
     * @return array<int, string>
     */
    public function parameters(): array
    {
        return [];
    }
}
