<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

use Honed\Core\Concerns\Allowable;
use Honed\Core\Concerns\HasExtra;
use Honed\Core\Concerns\HasFormatter;
use Honed\Core\Concerns\HasIcon;
use Honed\Core\Concerns\HasLabel;
use Honed\Core\Concerns\HasMeta;
use Honed\Core\Concerns\HasName;
use Honed\Core\Concerns\HasPlaceholder;
use Honed\Core\Concerns\HasType;
use Honed\Core\Concerns\IsActive;
use Honed\Core\Concerns\IsHidden;
use Honed\Core\Concerns\IsKey;
use Honed\Core\Concerns\Transformable;
use Honed\Core\Primitive;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @extends Primitive<string, mixed>
 */
class Column extends Primitive
{
    use Allowable;
    use Concerns\HasClass;
    use Concerns\IsSearchable;
    use Concerns\IsSortable;
    use Concerns\IsToggleable;
    use HasExtra;
    use HasFormatter;
    use HasIcon;
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
     * Create a new column instance.
     *
     * @param  string  $name
     * @param  string|null  $label
     * @return static
     */
    public static function make($name, $label = null)
    {
        return resolve(static::class)
            ->name($name)
            ->label($label ?? static::makeLabel($name));
    }

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->active(true);
        $this->type('default');
    }

    /**
     * Get the serialized name of the column.
     *
     * @return string
     */
    public function serializeName()
    {
        return Str::replace('.', '_', type($this->getName())->asString());
    }

    /**
     * Get the value of the column to form a record.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return array<string,mixed>
     */
    public function forRecord($model)
    {
        $value = Arr::get($model, $this->getName());

        return [
            $this->serializeName() => $this->apply($value),
        ];
    }

    /**
     * Apply the column's transform and format value.
     *
     * @param  mixed  $value
     * @return mixed
     */
    public function apply($value)
    {
        $value = $this->transform($value);

        return $this->formatValue($value);
    }

    /**
     * Format the value of the column.
     *
     * @param  mixed  $value
     * @return mixed
     */
    public function formatValue($value)
    {
        return $this->format($value) ?? $this->getPlaceholder();
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return [
            'name' => $this->serializeName(),
            'label' => $this->getLabel(),
            'type' => $this->getType(),
            'hidden' => $this->isHidden(),
            'active' => $this->isActive(),
            'toggleable' => $this->isToggleable(),
            'icon' => $this->getIcon(),
            'class' => $this->getClass(),
            'meta' => $this->getMeta(),
            'sort' => $this->isSortable() ? $this->sortToArray() : null,
        ];
    }
}
