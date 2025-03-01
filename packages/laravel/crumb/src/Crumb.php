<?php

declare(strict_types=1);

namespace Honed\Crumb;

use Honed\Core\Concerns\HasIcon;
use Honed\Core\Concerns\HasName;
use Honed\Core\Concerns\HasRequest;
use Honed\Core\Concerns\HasRoute;
use Honed\Core\Contracts\Resolves;
use Honed\Core\Primitive;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;

/**
 * @extends \Honed\Core\Primitive<string, mixed>
 */
class Crumb extends Primitive implements Resolves
{
    use HasIcon;
    use HasName;
    use HasRequest;
    use HasRoute;

    public function __construct(Request $request)
    {
        $this->$request = $request;
    }

    /**
     * Make a new crumb instance.
     *
     * @param string|\Closure $name
     * @param string|\Closure|null $link
     * @param mixed $parameters
     * @return $this
     */
    public static function make($name, $link = null, $parameters = [])
    {
        return resolve(static::class)
            ->name($name)
            ->route($link, $parameters);
    }

    /**
     * {@inheritDoc}
     */
    public function resolve($parameters = [], $typed = [])
    {
        $this->resolveName($parameters, $typed);
        $this->resolveRoute($parameters, $typed);

        return $this;
    }

    /**
     * Determine if the current route corresponds to this crumb.
     * 
     * @return bool
     */
    public function isCurrent()
    {
        $route = $this->resolveRoute();

        return (bool) ($route ? $this->getRequest()->url() === $route : false);
    }

    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        $this->resolve();

        return [
            'name' => $this->getName(),
            'url' => $this->getRoute(),
            'icon' => $this->getIcon(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName($parameterName)
    {
        $request = $this->getRequest();

        $parameters = Arr::mapWithKeys(
            $request->route()?->parameters() ?? [],
            static fn ($value, $key) => [$key => [$value]]
        );

        /** @var array<int, mixed> */
        return match ($parameterName) {
            'request' => [$request],
            'route' => [$request->route()],
            default => Arr::get(
                $parameters,
                $parameterName,
                parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
            ),        
        };
    }

    /**
     * {@inheritDoc}
     */
    protected function resolveDefaultClosureDependencyForEvaluationByType($parameterType)
    {
        $request = $this->getRequest();

        $parameters = Arr::mapWithKeys(
            $request->route()?->parameters() ?? [],
            static fn ($value) => \is_object($value)
                ? [\get_class($value) => [$value]]
                : [],
        );

        /** @var array<int, mixed> */
        return match ($parameterType) {
            Request::class => [$request],
            Route::class => [$request->route()],
            default => Arr::get(
                $parameters, 
                $parameterType, 
                App::make($parameterType)
            ),
        };
    }
}
