<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

use Honed\Core\Concerns\Authorizable;
use Honed\Core\Concerns\HasLabel;
use Honed\Core\Concerns\HasMeta;
use Honed\Core\Concerns\HasName;
use Honed\Core\Concerns\HasPlaceholder;
use Honed\Core\Concerns\HasType;
use Honed\Core\Concerns\IsActive;
use Honed\Core\Concerns\IsHidden;
use Honed\Core\Concerns\IsKey;
use Honed\Core\Concerns\Transformable;
use Honed\Core\Formatters\Concerns\Formattable;
use Honed\Core\Primitive;
use Honed\Table\Columns\Contracts\Column;

abstract class BaseColumn extends Primitive implements Column
{
    use Authorizable;
    use Concerns\HasBreakpoint;
    use Concerns\HasTooltip;
    use Concerns\IsSearchable;
    use Concerns\IsSortable;
    use Concerns\IsSrOnly;
    use Concerns\IsToggleable;
    use Formattable;
    use HasLabel;
    use HasMeta;
    use HasName;
    use HasPlaceholder;
    use HasType;
    use IsActive;
    use IsHidden;
    use IsKey;
    use Transformable;

    /**
     * Create a new column instance specifying the related database attribute, and optionally the display label.
     */
    final public function __construct(string $name, string $label = null)
    {
        parent::__construct();
        $this->setName($name);
        $this->setLabel($label ?? $this->makeLabel($name));
    }

    /**
     * Ensure all columns are active by default.
     */
    public function setUp(): void
    {
        $this->setActive(true);
    }

    /**
     * Make a column specifying the related database attribute, and optionally the display label.
     */
    public static function make(string $name, string $label = null): static
    {
        return resolve(static::class, compact('name', 'label'));
    }

    public function apply(mixed $value): mixed
    {
        $value = $this->transform($value);

        return $this->formatValue($value);
    }

    public function formatValue(mixed $value): mixed
    {
        return $this->format($value) ?? $this->getPlaceholder();
    }

    // Proxy methods to formatter, exporter

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
            'breakpoint' => $this->getBreakpoint(),
            'hidden' => $this->isHidden(),
            'sr_only' => $this->isSrOnly(),
            'toggleable' => $this->isToggleable(),
            'active' => $this->isActive(),
            'sortable' => $this->isSortable(),
            // 'sorting' => $this->isSorting(),
            'direction' => $this->getSort()?->getDirection(),
            'meta' => $this->getMeta(),
        ];
    }
}
