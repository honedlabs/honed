<?php

declare(strict_types=1);

namespace Honed\Core\Formatters;

use Honed\Core\Contracts\Formats;
use Illuminate\Support\Number;

class NumericFormatter implements Formats
{
    /**
     * @var int
     */
    protected $precision;

    /**
     * @var int
     */
    protected $divideBy;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var string
     */
    protected $currency;

    public function __construct(
        int $precision = null,
        int $divideBy = null,
        string $locale = null,
        string $currency = null
    ) {
        $this->precision($precision);
        $this->divideBy($divideBy);
        $this->locale($locale);
        $this->currency($currency);
    }

    /**
     * Make a new numeric formatter.
     */
    public static function make(
        int $precision = null,
        int $divideBy = null,
        string $locale = null,
        string $currency = null
    ): static {
        return resolve(static::class, compact('precision', 'divideBy', 'locale', 'currency'));
    }

    /**
     * Set the precision for the instance.
     * 
     * @return $this
     */
    public function precision(int $precision = null): static
    {
        if (! \is_null($precision)) {
            $this->precision = $precision;
        }

        return $this;
    }

    /**
     * Get the precision for the instance.
     */
    public function getPrecision(): ?int
    {
        return $this->precision;
    }

    /**
     * Determine if the instance has a precision.
     */
    public function hasPrecision(): bool
    {
        return isset($this->precision);
    }

    /**
     * Set the divide by amount for the instance.
     * 
     * @return $this
     */
    public function divideBy(int $divideBy = null): static
    {
        if (! \is_null($divideBy)) {
            $this->divideBy = $divideBy;
        }

        return $this;
    }

    /**
     * Get the divide by amount for the instance.
     */
    public function getDivideBy(): ?int
    {
        return $this->divideBy;
    }

    /**
     * Set the divide by amount to 100, to indicate the value is stored in cents.
     * 
     * @return $this
     */
    public function cents(): static
    {
        return $this->divideBy(100);
    }

    /**
     * Determine if the instance has a divide by amount set.
     */
    public function hasDivideBy(): bool
    {
        return isset($this->divideBy);
    }

    /**
     * Get or set the locale for the instance.
     * 
     * @return $this
     */
    public function locale(string $locale = null): static
    {
        if (! \is_null($locale)) {
            $this->locale = $locale;
        }

        return $this;   
    }

    /**
     * Get the locale for the instance.
     */
    public function getLocale(): ?string
    {
        return $this->locale;
    }

    /**
     * Determine if the instance has a locale set.
     */
    public function hasLocale(): bool
    {
        return isset($this->locale);
    }

    /**
     * Set the currency for the instance.
     * 
     * @return $this
     */
    public function currency(string $currency = null): static
    {
        if (! \is_null($currency)) {
            $this->currency = $currency;
        }

        return $this;
    }

    /**
     * Get the currency for the instance.
     */
    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    /**
     * Determine if the instance has a currency set.
     */
    public function hasCurrency(): bool
    {
        return isset($this->currency);
    }

    /**
     * Format the value as a number.
     */
    public function format(mixed $value): mixed
    {
        if (\is_null($value) || ! \is_numeric($value)) {
            return null;
        }

        if ($this->hasDivideBy()) {
            $value = $value / $this->getDivideBy();
        }

        return match (true) {
            $this->hasCurrency() => Number::currency($value, $this->getCurrency(), $this->getLocale()),
            $this->hasLocale() => Number::format($value, precision: $this->getPrecision(), locale: $this->getLocale()),
            default => $value
        };
    }
}
