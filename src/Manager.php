<?php

declare(strict_types=1);

namespace Honed\Crumb;

use Illuminate\Routing\Router;
use Honed\Crumb\Exceptions\CrumbsNotFoundException;
use Honed\Crumb\Exceptions\DuplicateCrumbsException;
use Illuminate\Support\Collection;

class Manager
{
    /**
     * Trails paired to a key.
     * 
     * @var array<string,\Honed\Crumb\Trail>
     */
    protected $trails = [];

    /**
     * Crumbs to be added before the trail.
     * Useful for adding a home crumb to all trails.
     * 
     * @var (\Closure(\Honed\Crumb\Trail $trail):void)|null
     */
    protected $before = null;

    /**
     * Crumbs to be added after the trail.
     * 
     * @var (\Closure(\Honed\Crumb\Trail $trail):void)|null
     */
    protected $after = null;

    /**
     * The resolved breadcrumbs to use.
     * 
     * @var array<string,\Honed\Crumb\Crumb>
     */
    protected $crumbs = [];

    public function __construct()
    {
        //
    }

    /**
     * Set crumbs to be added globally, before all other crumbs.
     * 
     * @param (\Closure(\Honed\Crumb\Trail $trail):void) $trail
     */
    public function before(\Closure $trail)
    {
        $this->before = $trail;
    }

    /**
     * Set crumbs to be added globally, after all other crumbs.
     * 
     * @param (\Closure(\Honed\Crumb\Trail $trail):void) $trail
     */
    public function after(\Closure $trail)
    {
        $this->after = $trail;
    }

    /**
     * Set a crumb trail for a given name.
     * 
     * @param string $name
     * @param (\Closure(\Honed\Crumb\Trail $trail):void) $trail
     */
    public function for(string $name, \Closure $trail)
    {
        if ($this->exists($name)) {
            throw new DuplicateCrumbsException($name);
        }

        $this->trails[$name] = $trail;
    }

    /**
     * Determine if a crumb with the given name exists. 
     */
    public function exists(string $name): bool
    {
        return isset($this->trails[$name]);
    }

    /**
     * Retrieve a crumb trail by name.
     * 
     * @param string $name
     * @return \Honed\Crumb\Trail
     * 
     * @throws \Honed\Crumb\Exceptions\CrumbsNotFoundException
     */
    public function get(string $name): Trail
    {
        if (!$this->exists($name)) {
            throw new CrumbsNotFoundException($name);
        }

        $trail = Trail::make();

        if ($this->before) {
            ($this->before)($trail);
        }

        ($this->trails[$name])($trail);

        if ($this->after) {
            ($this->after)($trail);
        }

        return $trail;
    }
}
