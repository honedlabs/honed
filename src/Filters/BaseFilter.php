<?php

declare(strict_types=1);

namespace Honed\Table\Filters;

use Honed\Core\Primitive;
use Honed\Core\Concerns\HasMeta;
use Honed\Core\Concerns\HasType;
use Honed\Core\Concerns\HasLabel;
use Honed\Core\Concerns\HasValue;
use Honed\Core\Concerns\IsActive;
use Honed\Table\Concerns\HasAlias;
use Honed\Table\Contracts\Filters;
use Honed\Core\Concerns\Validatable;
use Honed\Core\Concerns\Authorizable;
use Honed\Core\Concerns\HasAttribute;
use Honed\Core\Concerns\Transformable;

abstract class BaseFilter extends Primitive implements Filters
{
    use HasLabel;
    use HasMeta;
    use HasAttribute;
    use HasType;
    use HasValue;
    use IsActive;
    use Authorizable;
    use Transformable;
    use HasAlias;
    use Validatable;

    /**
     * Create a new filter instance specifying the database column, and optionally the display label.
     * 
     * @param string|(\Closure():string) $attribute
     * @param string|(\Closure():string)|null $label
     */
    final public function __construct(string|\Closure $attribute, string|\Closure|null $label = null)
    {
        parent::__construct();
        $this->setAttribute($attribute);
        $this->setLabel($label ?? $this->makeLabel($this->getAttribute()));
    }

    /**
     * Make a filter specifying the database column, and optionally the display label.
     * 
     * @param string|(\Closure():string) $attribute
     * @param string|(\Closure():string)|null $label
     */
    final public static function make(string|\Closure $attribute, string|\Closure|null $label = null): static
    {
        return resolve(static::class, compact('attribute', 'label'));
    }

    /**
     * Retrieve the value of the filter name from the current request.
     * 
     * @return int|string|array<int,int|string>|null
     */
    public function getValueFromRequest(): mixed
    {
        return request()->input($this->getParameterName(), null);
    }

    /**
     * Determine if the filter should be applied.
     * 
     * @param mixed $value
     * @return bool
     */
    public function filtering(mixed $value): bool
    {
        return ! \is_null($value);
    }

    /**
     * Retrieve the query parameter name of the filter
     * 
     * @internal
     * @return string
     */
    protected function getParameterName(): string
    {
        return $this->getAlias() ?? str($this->getAttribute())->afterLast('.')->toString();
    }

    /**
     * Get the filter state as an array
     * 
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->getParameterName(),
            'label' => $this->getLabel(),
            'type' => $this->getType(),
            'active' => $this->isActive(),
            'value' => $this->getValue(),
            'meta' => $this->getMeta(),
        ];
    }
}
