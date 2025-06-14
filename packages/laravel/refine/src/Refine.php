<?php

declare(strict_types=1);

namespace Honed\Refine;

use Closure;
use Honed\Core\Parameters;
use Honed\Core\Primitive;
use Honed\Refine\Concerns\CanRefine;
use Illuminate\Container\Container;
use Illuminate\Contracts\Database\Eloquent\Builder as BuilderContract;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\ForwardsCalls;
use Laravel\Scout\Builder as ScoutBuilder;
use Throwable;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model = \Illuminate\Database\Eloquent\Model
 * @template TBuilder of \Illuminate\Database\Eloquent\Builder<TModel> = \Illuminate\Database\Eloquent\Builder<TModel>
 *
 * @mixin TBuilder
 */
class Refine extends Primitive // implements NullsAsUndefined
{
    use ForwardsCalls;
    use CanRefine;

    /**
     * The default namespace where refiners reside.
     *
     * @var string
     */
    protected static $namespace = 'App\\Refiners\\';

    /**
     * How to resolve the refiner for the given model name.
     *
     * @var (Closure(class-string<\Illuminate\Database\Eloquent\Model>):class-string<Refine>)|null
     */
    protected static $refinerResolver;

    /**
     * Create a new refine instance.
     */
    public function __construct(Request $request)
    {
        parent::__construct();

        $this->request($request);

        $this->definition($this);
    }

    /**
     * {@inheritdoc}
     *
     * @param  array<int, mixed>  $parameters
     */
    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return parent::macroCall($method, $parameters);
        }

        return $this->forwardBuilderCall($method, $parameters);
    }

    /**
     * Create a new refine instance.
     *
     * @param  TModel|class-string<TModel>|TBuilder|null  $resource
     * @return self
     */
    public static function make($resource = null)
    {
        $refine = App::make(static::class);

        if ($resource) {
            return $refine->withResource($resource);
        }

        return $refine;
    }

    /**
     * Get a new refiner instance for the given model name.
     *
     * @template TClass of \Illuminate\Database\Eloquent\Model
     *
     * @param  class-string<TClass>  $modelName
     * @return Refine<TClass>
     */
    public static function refinerForModel($modelName)
    {
        $refiner = static::resolveRefinerName($modelName);

        return $refiner::make()
            ->withResource($modelName);
    }

    /**
     * Get the refiner name for the given model name.
     *
     * @param  class-string<\Illuminate\Database\Eloquent\Model>  $className
     * @return class-string<Refine>
     */
    public static function resolveRefinerName($className)
    {
        $resolver = static::$refinerResolver ?? function (string $className) {
            $appNamespace = static::appNamespace();

            $className = Str::startsWith($className, $appNamespace.'Models\\')
                ? Str::after($className, $appNamespace.'Models\\')
                : Str::after($className, $appNamespace);

            /** @var class-string<Refine> */
            return static::$namespace.$className.'Refiner';
        };

        return $resolver($className);
    }

    /**
     * Specify the default namespace that contains the application's refiners.
     *
     * @param  string  $namespace
     * @return void
     */
    public static function useNamespace($namespace)
    {
        static::$namespace = $namespace;
    }

    /**
     * Specify the callback that should be invoked to guess the name of a refiner for a model.
     *
     * @param  Closure(class-string<\Illuminate\Database\Eloquent\Model>):class-string<Refine>  $callback
     * @return void
     */
    public static function guessRefinersUsing($callback)
    {
        static::$refinerResolver = $callback;
    }

    /**
     * Flush the global configuration state.
     *
     * @return void
     */
    public static function flushState()
    {
        static::$namespace = 'App\\Refine\\';
        static::$refinerResolver = null;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray($named = [], $typed = [])
    {
        return [
            'term' => $this->getTerm(),
            'delimiter' => $this->getDelimiter(),
            'sort' => $this->getSortKey(),
            'search' => $this->getSearchKey(),
            'placeholder' => $this->getSearchPlaceholder(),
            'match' => $this->getMatchKey(),

            'sorts' => $this->sortsToArray(),
            'filters' => $this->filtersToArray(),
            'searches' => $this->searchesToArray(),
        ];
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

    /**
     * Define the refine instance.
     *
     * @param  \Honed\Refine\Refine<TModel, TBuilder>  $refine
     * @return \Honed\Refine\Refine<TModel, TBuilder>|void
     */
    protected function definition(Refine $refine)
    {
        return $refine;
    }

    /**
     * Forward a call to the resource.
     *
     * @param  string  $method
     * @param  array<int, mixed>  $parameters
     * @return mixed
     */
    protected function forwardBuilderCall($method, $parameters)
    {
        return $this->refine()
            ->forwardDecoratedCallTo(
                $this->getResource(),
                $method,
                $parameters
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName($parameterName)
    {
        $resource = $this->getResource();

        $request = $this->getRequest();

        [$_, $singular, $plural] = Parameters::names($resource);

        return match ($parameterName) {
            'request' => [$request],
            'route' => [$request->route()],
            'builder', 'resource', 'query', 'q', $singular, $plural => [$resource],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }

    /**
     * {@inheritdoc}
     */
    protected function resolveDefaultClosureDependencyForEvaluationByType($parameterType)
    {
        $resource = $this->getResource();
        $request = $this->getRequest();

        return match ($parameterType) {
            Request::class => [$request],
            Route::class => [$request->route()],
            $resource::class, Builder::class, ScoutBuilder::class, BuilderContract::class => [$resource],
            default => parent::resolveDefaultClosureDependencyForEvaluationByType($parameterType),
        };
    }
}
