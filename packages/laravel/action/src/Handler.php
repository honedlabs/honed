<?php

declare(strict_types=1);

namespace Honed\Action;

use Honed\Action\Http\Data\ActionData;
use Honed\Action\Http\Data\BulkData;
use Honed\Action\Http\Data\InlineData;
use Honed\Core\Concerns\HasBuilderInstance;
use Honed\Core\Parameters;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TBuilder of \Illuminate\Database\Eloquent\Builder<TModel>
 */
class Handler
{
    /**
     * @use \Honed\Core\Concerns\HasBuilderInstance<TModel, TBuilder>
     */
    use HasBuilderInstance;

    /**
     * List of the available actions.
     *
     * @var array<int,\Honed\Action\Action>
     */
    protected $actions = [];

    /**
     * The key to use for selecting records.
     *
     * @var string|null
     */
    protected $key;

    /**
     * Create a new handler instance.
     *
     * @param  TBuilder  $builder
     * @param  array<int,\Honed\Action\Action>  $actions
     * @param  string|null  $key
     */
    public function __construct($builder, $actions, $key = null)
    {
        $this->builder = $builder;
        $this->actions = $actions;
        $this->key = $key;
    }

    /**
     * Make a new handler instance.
     *
     * @param  TBuilder  $builder
     * @param  array<int,\Honed\Action\Action>  $actions
     * @param  string|null  $key
     * @return static
     */
    public static function make($builder, $actions, $key = null)
    {
        return resolve(static::class, \compact('builder', 'actions', 'key'));
    }

    /**
     * Get the actions for the handler.
     *
     * @return array<int,\Honed\Action\Action>
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * Get the key to use for selecting records.
     *
     * @param  TBuilder  $builder
     * @return string
     */
    public function getKey($builder)
    {
        return $builder->qualifyColumn(
            $this->key ??= $builder->getModel()->getKeyName()
        );
    }

    /**
     * Handle the incoming action request using the actions from the source, and the resource provided.
     *
     * @param  \Honed\Action\Http\Requests\ActionRequest  $request
     * @return \Illuminate\Contracts\Support\Responsable|\Symfony\Component\HttpFoundation\RedirectResponse|void
     */
    public function handle($request)
    {
        /** @var string */
        $type = $request->validated('type');

        $data = match ($type) {
            ActionFactory::Inline => InlineData::from($request),
            ActionFactory::Bulk => BulkData::from($request),
            ActionFactory::Page => ActionData::from($request),
            default => abort(400),
        };

        [$action, $query] = $this->resolveAction($type, $data);

        abort_unless((bool) $action, 400);

        abort_unless((bool) $query, 404);

        [$named, $typed] = Parameters::builder($query);

        abort_unless($action->isAllowed($named, $typed), 403);

        /** @var TModel|TBuilder $query */
        $result = $action->execute($query);

        if ($result instanceof Responsable || $result instanceof RedirectResponse) {
            return $result;
        }

        return back();
    }

    /**
     * Retrieve the action and query based on the type and data.
     *
     * @param  string  $type
     * @param  \Honed\Action\Http\Data\ActionData  $data
     * @return array{\Honed\Action\Action|null,TModel|TBuilder|null}
     */
    public function resolveAction($type, $data)
    {
        return match ($type) {
            ActionFactory::Inline => $this->resolveInlineAction(type($data)->as(InlineData::class)),
            ActionFactory::Bulk => $this->resolveBulkAction(type($data)->as(BulkData::class)),
            ActionFactory::Page => $this->resolvePageAction($data),
            default => static::throwInvalidActionTypeException($type),
        };
    }

    /**
     * Resolve the inline action.
     *
     * @param  \Honed\Action\Http\Data\InlineData  $data
     * @return array{\Honed\Action\Action|null, TModel|null}
     */
    protected function resolveInlineAction($data)
    {
        return [
            $this->getAction($data->name, InlineAction::class),
            $this->getBuilder()
                ->where($this->getKey($this->getBuilder()), $data->record)
                ->first(),
        ];
    }

    /**
     * Resolve the bulk action.
     *
     * @param  \Honed\Action\Http\Data\BulkData  $data
     * @return array{\Honed\Action\Action|null, TBuilder}
     */
    public function resolveBulkAction($data)
    {
        $builder = $this->getBuilder();
        $key = $this->getKey($builder);

        /** @var TBuilder $builder */
        $builder = $data->all
            ? $builder->whereNotIn($key, $data->except)
            : $builder->whereIn($key, $data->only);

        return [
            $this->getAction($data->name, BulkAction::class),
            $builder,
        ];
    }

    /**
     * Resolve the page action.
     *
     * @param  \Honed\Action\Http\Data\ActionData  $data
     * @return array{\Honed\Action\Action|null, TBuilder}
     */
    public function resolvePageAction($data)
    {
        return [
            $this->getAction($data->name, PageAction::class),
            $this->getBuilder(),
        ];
    }

    /**
     * Find the action by name and type.
     *
     * @param  string  $name
     * @param  class-string<\Honed\Action\Action>  $type
     * @return \Honed\Action\Action|null
     */
    public function getAction($name, $type)
    {
        return Arr::first(
            $this->getActions(),
            static fn (Action $action) => $action instanceof $type
                && $action->getName() === $name
        );
    }

    /**
     * Throw an invalid argument exception.
     *
     * @param  string  $type
     * @return never
     *
     * @throws \InvalidArgumentException
     */
    public static function throwInvalidActionTypeException($type)
    {
        throw new \InvalidArgumentException(\sprintf(
            'Action type [%s] is invalid.', $type
        ));
    }
}
