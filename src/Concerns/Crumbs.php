<?php

namespace Honed\Crumb\Concerns;

use ReflectionMethod;
use Honed\Crumb\Attributes\Crumb;
use Honed\Crumb\Crumbs as CrumbCrumbs;
use Illuminate\Support\Facades\Route;
use Honed\Crumb\Facades\Crumbs as CrumbsFacade;
use Honed\Crumb\Exceptions\CrumbsNotFoundException;

trait Crumbs
{
    final public function __construct()
    {
        $this->configureCrumbs();
    }

    public function configureCrumbs(): void
    {
        $name = $this->getCrumbName();

        if ($name) {
            CrumbsFacade::get($name)->share();
        }
    }

    public function getCrumbName(): ?string
    {
        return match (true) {
            (bool) ($c = collect((new \ReflectionMethod($this, Route::getCurrentRoute()->getActionMethod()))
            ->getAttributes(Crumb::class)
            )->first()?->newInstance()->getCrumb()) => $c,

            (bool) ($c = collect((new \ReflectionClass($this))
                ->getAttributes(Crumb::class)
            )->first()?->newInstance()->getCrumb()) => $c,

            \property_exists($this, 'crumb') => $this->crumb,

            \method_exists($this, 'crumb') => $this->crumb,
            default => null,
        };
    }
}
