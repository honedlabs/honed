<?php

declare(strict_types=1);

namespace Honed\Binding;

use Illuminate\Container\Container;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionMethod;
use Throwable;

use function array_reduce;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 */
abstract class Binder
{
    /**
     * The default namespace where binders reside.
     *
     * @var string
     */
    public static $namespace = 'App\\Binders\\';

    /**
     * The name of the binder's corresponding model.
     *
     * @var class-string<TModel>
     */
    protected $model;

    /**
     * The default model name resolvers.
     *
     * @var array<class-string, callable(self): class-string<TModel>>
     */
    protected static $modelNameResolvers = [];

    /**
     * The binder name resolver.
     *
     * @var callable(class-string<\Illuminate\Database\Eloquent\Model>): class-string<Binder>
     */
    protected static $binderNameResolver;

    /**
     * Retrieve the binder for the model which binds the given field if it exists.
     *
     * @param  class-string<TModel>  $model
     * @param  string|null  $field
     * @return static|null
     */
    public static function for($model, $field)
    {
        if (App::bindersAreCached()
            && $binder = static::getBinderFromCache($model, $field)) {
            return $binder;
        }

        if ($binder = static::guessBinderName($model)) {
            return $binder;
        }

        if (\class_exists($binder) && \method_exists($binder, $field)) {
            return new $binder();
        }

        return null;
    }

    /**
     * Specify the callback that should be invoked to guess model names based on binder names.
     *
     * @param  callable(self): class-string<TModel>  $callback
     * @return void
     */
    public static function guessModelNamesUsing(callable $callback)
    {
        static::$modelNameResolvers[static::class] = $callback;
    }

    /**
     * Specify the default namespace that contains the application's model binders.
     *
     * @return void
     */
    public static function useNamespace(string $namespace)
    {
        static::$namespace = $namespace;
    }

    /**
     * Specify the callback that should be invoked to guess binder names based on dynamic relationship names.
     *
     * @param  callable(class-string<\Illuminate\Database\Eloquent\Model>): class-string<Binder>  $callback
     * @return void
     */
    public static function guessBinderNamesUsing(callable $callback)
    {
        static::$binderNameResolver = $callback;
    }

    /**
     * Flush the binder's global state.
     *
     * @return void
     */
    public static function flushState()
    {
        static::$modelNameResolvers = [];
        static::$binderNameResolver = null;
        static::$namespace = 'App\\Binders\\';
    }

    /**
     * Resolve the binding for the model.
     *
     * @param  \Illuminate\Database\Eloquent\Model|\Illuminate\Contracts\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\Relation  $query
     * @param  string  $field
     * @param  mixed  $value
     * @return TModel|null
     */
    public function resolve($query, $field, $value)
    {
        return $this->{$field}($query, $value)->first();
    }

    /**
     * Get the bindings available on this binder.
     *
     * @return array<int, string>
     */
    public function bindings()
    {
        return array_reduce(
            (new ReflectionClass($this))
                ->getMethods(ReflectionMethod::IS_PUBLIC),

            function (array $bindings, ReflectionMethod $method) {
                if (! $method->isStatic() && $method->class === $this::class) {
                    $bindings[] = $method->getName();
                }

                return $bindings;
            },
            []
        );
    }

    /**
     * Get the name of the model that is generated by the binder.
     *
     * @return class-string<TModel>
     */
    public function modelName()
    {
        if (isset($this->model)) {
            return $this->model;
        }

        $resolver = static::$modelNameResolvers[static::class] ?? static::$modelNameResolvers[self::class] ?? static::$modelNameResolver ?? function (self $binder) {
            $namespacedBinderBasename = Str::replaceLast(
                'Binder', '', Str::replaceFirst(static::$namespace, '', $binder::class)
            );

            $binderBasename = Str::replaceLast('Binder', '', class_basename($binder));

            $appNamespace = static::appNamespace();

            return class_exists($appNamespace.'Models\\'.$namespacedBinderBasename)
                ? $appNamespace.'Models\\'.$namespacedBinderBasename
                : $appNamespace.$binderBasename;
        };

        return $resolver($this);
    }

    /**
     * Retrieve the binder from the cache.
     *
     * @param  class-string<TModel>  $model
     * @param  string  $field
     * @return static|null
     */
    protected static function getBinderFromCache($model, $field)
    {
        $binders = require App::getCachedBindersPath();

        if (isset($binders[$model][$field])) {
            return new $binders[$model][$field]();
        }

        return null;
    }

    /**
     * Guess the binder name for the model and field.
     *
     * @param  class-string<TModel>  $model
     * @param  string  $field
     * @return static|null
     */
    protected static function guessBinderName($model, $field)
    {
        if (static::$binderNameResolver) {
            return static::$binderNameResolver($model);
        }

        if (class_exists($model) && method_exists($model, $field)) {
            return new $Binder();
        }

        return null;
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
