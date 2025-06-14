<?php

declare(strict_types=1);

namespace Honed\Action;

use Closure;
use Throwable;
use function array_merge;
use Honed\Core\Primitive;
use Illuminate\Support\Str;
use Honed\Action\Contracts\Handler;
use Honed\Action\Contracts\Handles;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\App;
use Honed\Core\Concerns\HasResource;
use Honed\Action\Concerns\HasEncoder;
use Honed\Action\Concerns\HasHandler;
use Honed\Action\Concerns\HasEndpoint;
use Honed\Action\Concerns\HandlesActions;

use Honed\Action\Concerns\HasTypedActions;
use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Contracts\Foundation\Application;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model = \Illuminate\Database\Eloquent\Model
 * @template TBuilder of \Illuminate\Database\Eloquent\Builder<TModel> = \Illuminate\Database\Eloquent\Builder<TModel>
 */
class ActionGroup extends Primitive implements Handler
{
    /**
     * @use \Honed\Action\Concerns\HandlesActions<TModel, TBuilder>
     */
    use HandlesActions;

    use HasHandler;

    /**
     * @use \Honed\Core\Concerns\HasResource<TModel, TBuilder>
     */
    use HasResource;

    /**
     * The model to be used to resolve inline actions.
     *
     * @var TModel|null
     */
    protected $model;

    /**
     * The default namespace where action groups reside.
     *
     * @var string
     */
    public static $namespace = 'App\\ActionGroups\\';

    /**
     * How to resolve the action group for the given model name.
     *
     * @var (Closure(class-string):class-string<ActionGroup>)|null
     */
    protected static $actionGroupNameResolver;

    /**
     * Create a new action group instance.
     *
     * @param  Action|iterable<int, Action>  ...$actions
     * @return static
     */
    public static function make(...$actions)
    {
        return resolve(static::class)
            ->withActions($actions);
    }

    /**
     * The root parent class, indicating an anonymous class.
     * 
     * @return class-string<\Honed\Action\Contracts\HandlesActions>
     */
    public static function anonymous()
    {
        return ActionGroup::class;
    }

    /**
     * Get a new table instance for the given model name.
     *
     * @template TClass of \Illuminate\Database\Eloquent\Model
     *
     * @param  class-string<TClass>  $modelName
     * @return ActionGroup<TClass>
     */
    public static function actionGroupForModel($modelName)
    {
        $table = static::resolveActionGroupName($modelName);

        return $table::make();
    }

    /**
     * Get the table name for the given model name.
     *
     * @param  class-string  $className
     * @return class-string<ActionGroup>
     */
    public static function resolveActionGroupName($className)
    {
        $resolver = static::$actionGroupNameResolver ?? function (string $className) {
            $appNamespace = static::appNamespace();

            $className = Str::startsWith($className, $appNamespace.'Models\\')
                ? Str::after($className, $appNamespace.'Models\\')
                : Str::after($className, $appNamespace);

            /** @var class-string<ActionGroup> */
            return static::$namespace.$className.'Actions';
        };

        return $resolver($className);
    }

    /**
     * Specify the default namespace that contains the application's model action groups.
     *
     * @param  string  $namespace
     * @return void
     */
    public static function useNamespace($namespace)
    {
        static::$namespace = $namespace;
    }

    /**
     * Specify the callback that should be invoked to guess the name of a model action group.
     *
     * @param  Closure(class-string):class-string<ActionGroup>  $callback
     * @return void
     */
    public static function guessActionGroupNamesUsing($callback)
    {
        static::$actionGroupNameResolver = $callback;
    }

    /**
     * Flush the action group's global configuration state.
     *
     * @return void
     */
    public static function flushState()
    {
        static::$actionGroupNameResolver = null;
        static::$namespace = 'App\\ActionGroups\\';
    }

    /**
     * Set the model to be used to resolve inline actions.
     *
     * @param  TModel|null  $model
     * @return $this
     */
    public function for($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Get the model to be used to resolve inline actions.
     *
     * @return TModel|null
     */
    public function getModel()
    {
        return $this->model;
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
    public function handle($request)
    {
        if ($this->isntExecutable()) {
            abort(404);
        }

        $resource = $this->getResource();

        return App::make(Handler::class)
            ->handle($this, $request);

        // return Handler::make(
        //     $resource,
        //     $this->getActions()
        // )->handle($request);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray($named = [], $typed = [])
    {
        $actions = [
            'inline' => $this->inlineActionsToArray($this->getModel()),
            'bulk' => $this->bulkActionsToArray(),
            'page' => $this->pageActionsToArray(),
        ];

        if ($this->isExecutable(self::class)) {
            return array_merge($actions, [
                'id' => $this->getRouteKey(),
                'endpoint' => $this->getEndpoint(),
            ]);
        }

        return $actions;
    }

    /**
     * Get the application namespace for the application.
     *
     * @return string
     */
    protected static function appNamespace()
    {
        try {
            return Container::getInstance()
                ->make(Application::class)
                ->getNamespace();
        } catch (Throwable) {
            return 'App\\';
        }
    }
}
