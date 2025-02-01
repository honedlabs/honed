<?php

declare(strict_types=1);

namespace Honed\Nav\Concerns;

use Symfony\Component\HttpFoundation\Request;

trait HasRoute
{
    const ValidMethods = [
        Request::METHOD_GET,
        Request::METHOD_POST,
        Request::METHOD_PUT,
        Request::METHOD_DELETE,
        Request::METHOD_PATCH,
    ];

    /**
     * @var string|\Closure|null
     */
    protected $route;

    /**
     * @var bool
     */
    protected $external = false;

    /**
     * @var string
     */
    protected $method = Request::METHOD_GET;

    /**
     * @return $this
     */
    public function route(string|\Closure|null $route, mixed $parameters = []): static
    {
        if (! \is_null($route)) {
            $this->route = match (true) {
                \is_string($route) => route($route, $parameters),
                $route instanceof \Closure => $route,
            };
        }


        return $this;
    }

    /**
     * @return $this
     */
    public function url(string|\Closure|null $url): static
    {
        if (! \is_null($url)) {
            $this->route = $url;
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function method(?string $method): static 
    {
        if (\is_null($method)) {
            return $this;
        }

        $method = \mb_strtoupper($method);

        if (! \in_array($method, self::ValidMethods)) {
            throw new \InvalidArgumentException("The provided method [{$method}] is not a valid HTTP method.");
        }

        $this->method = $method;

        return $this;
    }

    /**
     * @return $this
     */
    public function external(bool $external = true): static
    {
        $this->external = $external;

        return $this;
    }

    /**
     * Determine if the route is set.

     */
    public function hasRoute(): bool
    {
        return ! \is_null($this->route);
    }

    /**
     * Determine if the route points to an external link.
     */
    public function isExternal(): bool
    {
        return $this->external;
    }

        /**
     * @param  array<string,mixed>  $parameters
     * @param  array<string,mixed>  $typed
     */
    public function getRoute($parameters = [], $typed = []): ?string
    {
        return $this->evaluate($this->route, $parameters, $typed);
    }

    /**
     * Get the HTTP method for the route.
     * 
     * @default \Symfony\Component\HttpFoundation\Request::METHOD_GET
     */
    public function getMethod(): string
    {
        return $this->method;
    }
}