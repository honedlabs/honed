<?php

declare(strict_types=1);

namespace Honed\Core\Formatters;

use Illuminate\Support\Number;

class NumberFormatter implements Contracts\Formatter
{
    use Concerns\HasPrecision;
    use Concerns\HasDivideBy;

    /**
     * Create a new number formatter instance with a precision, divide by, and rounding.
     * 
     * @param int|(\Closure():int)|null $precision
     * @param int|(\Closure():int)|null $divideBy
     * @param string|(\Closure():string)|null $locale
     */
    public function __construct(int|\Closure|null $precision = null, int|\Closure|null $divideBy = null)
    {
        $this->setPrecision($precision);
        $this->setDivideBy($divideBy);
    }
    
    /**
     * Make a number formatter with a precision, divide by, and rounding.
     * 
     * @param int|(\Closure():int)|null $precision
     * @param int|(\Closure():int)|null $divideBy
     * @param string|(\Closure():string)|null $locale
     * @return $this
     */
    public static function make(int|\Closure|null $precision = null, int|\Closure|null $divideBy = null, string|\Closure|null $locale = null): static
    {
        return resolve(static::class, compact('precision', 'divideBy', 'locale'));
    }

    /**
     * Format the value as a number
     * 
     * @param mixed $value
     * @return string|null|false
     */
    public function format(mixed $value): string|null|false
    {
        // If it isn't a number, return null
        if (!\is_numeric($value)) {
            return null;
        }

        $value = $this->hasDivideBy() ? $value / $this->getDivideBy() : $value;
        return Number::format($value, precision: $this->getPrecision(), locale: $this->getLocale());
    }
}
