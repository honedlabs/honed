<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

use Honed\Core\Concerns\Authorizable;
use Honed\Core\Primitive;
use Honed\Core\Concerns\HasLabel;
use Honed\Core\Concerns\HasName;
use Honed\Core\Concerns\HasType;
use Honed\Core\Concerns\IsKey;
use Honed\Core\Concerns\HasMeta;
use Honed\Core\Concerns\HasPlaceholder;
use Honed\Core\Concerns\IsActive;
use Honed\Core\Concerns\IsHidden;
use Honed\Core\Concerns\Transformable;
use Honed\Table\Columns\Concerns\HasBreakpoint;
use Honed\Table\Columns\Concerns\HasFallback;
use Honed\Table\Columns\Concerns\HasTooltip;
use Honed\Table\Columns\Concerns\IsSortable;
use Honed\Table\Columns\Concerns\IsSrOnly;
use Honed\Table\Columns\Concerns\IsToggleable;

abstract class BaseColumn extends Primitive
{
    use HasBreakpoint;
    use HasLabel;
    use HasMeta;
    use HasName;
    use HasPlaceholder;
    use HasTooltip;
    use HasType;
    use IsActive;
    use Authorizable;
    use IsHidden;
    use IsKey;
    use IsSortable;
    use IsSrOnly;
    use IsToggleable;
    use Transformable;
    use HasFallback;

    /**
     * Create a new column instance specifying the related database attribute, and optionally the display label.
     * 
     * @param string|Closure():string $attribute
     * @param string|(Closure():string)|null $label
     */
    final public function __construct(string|\Closure $name, string|\Closure|null $label = null)
    {
        parent::__construct();
        $this->setName($name);
        $this->setLabel($label ?? $this->makeLabel($name));
    }

    /**
     * Make a column specifying the related database attribute, and optionally the display label.
     * 
     * @param string|Closure():string $attribute
     * @param string|(Closure():string)|null $label
     */
    public static function make(string|\Closure $name, string|\Closure|null $label = null): static
    {
        return resolve(static::class, compact('name', 'label'));
    }

    /**
     * Get the column state as an array
     * 
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'label' => $this->getLabel(),
            'type' => $this->getType(),
            'hidden' => $this->isHidden(),
            'tooltip' => $this->getTooltip(),
            'breakpoint' => $this->getBreakpoint(),
            'sr' => $this->isSrOnly(),
            'toggle' => $this->isToggleable(),
            'active' => $this->isToggledOn(),
            'sort' => $this->isSortable(),
            'sorting' => $this->isSorting(),
            'direction' => $this->getSort()?->getDirection(),
            'meta' => $this->getMeta(),
        ];
    }

    /**
     * Modify the record value to align it with the column configuration.
     * 
     * @template T
     * @param T $value
     * @return T|mixed
     */
    public function apply(mixed $value): mixed
    {
        $value = $this->applyTransform($value);

        return $this->formatValue($value);
    }

    /**
     * Format how the records' values are displayed in this column.
     * 
     * @template T
     * @param T $value
     * @return T|mixed
     */
    public function formatValue(mixed $value): mixed
    {
        return $value;
    }
}
