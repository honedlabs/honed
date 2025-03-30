<?php

declare(strict_types=1);

namespace Honed\Action;

use Honed\Action\Concerns\HasActions;
use Honed\Action\Concerns\HasEncoder;
use Honed\Action\Concerns\HasEndpoint;
use Honed\Core\Primitive;
use Illuminate\Contracts\Routing\UrlRoutable;

class ActionGroup extends Primitive implements UrlRoutable
{
    use HasActions;
    use HasEncoder;
    use HasEndpoint;

    /**
     * The model the inline actions should use to resolve.
     *
     * @var \Illuminate\Database\Eloquent\Model|null
     */
    protected $for;

    /**
     * Create a new action group instance.
     *
     * @param  \Honed\Action\Action|iterable<int, \Honed\Action\Action>  ...$actions
     * @return static
     */
    public static function make(...$actions)
    {
        return resolve(static::class)
            ->withActions($actions);
    }

    /**
     * Set the model the inline actions should be bound to.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return $this
     */
    public function for($model)
    {
        $this->for = $model;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @param  string  $value
     * @return \Honed\Action\ActionGroup|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        /** @var \Honed\Action\ActionGroup|null */
        return $this->getPrimitive($value, ActionGroup::class);
    }

    /**
     * {@inheritdoc}
     *
     * @param  string  $value
     * @return \Honed\Action\ActionGroup|null
     */
    public function resolveChildRouteBinding($childType, $value, $field = null)
    {
        return $this->resolveRouteBinding($value, $field);
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteKeyName()
    {
        return 'action';
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteKey()
    {
        return static::encode(static::class);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $actions = [
            'inline' => $this->inlineActionsToArray($this->for),
            'bulk' => $this->bulkActionsToArray(),
            'page' => $this->pageActionsToArray(),
        ];

        if ($this->hasServerActions(ActionGroup::class)) {
            return \array_merge($actions, [
                'id' => $this->getRouteKey(),
                'endpoint' => $this->getEndpoint(),
            ]);
        }

        return $actions;
    }
}
