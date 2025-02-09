<?php

declare(strict_types=1);

namespace Honed\Action;

use Honed\Core\Concerns\Allowable;
use Honed\Core\Concerns\HasExtra;
use Honed\Core\Concerns\HasIcon;
use Honed\Core\Concerns\HasLabel;
use Honed\Core\Concerns\HasName;
use Honed\Core\Concerns\HasRoute;
use Honed\Core\Concerns\HasType;
use Honed\Core\Primitive;
use Illuminate\Support\Traits\ForwardsCalls;

/**
 * @extends \Honed\Core\Primitive<string,mixed>
 */
abstract class Action extends Primitive
{
    use Allowable;
    use Concerns\HasAction;
    use Concerns\HasConfirm;
    use ForwardsCalls;
    use HasExtra;
    use HasIcon;
    use HasLabel;
    use HasName;
    use HasRoute;
    use HasType;

    public static function make(string $name, string|\Closure|null $label = null): static
    {
        return resolve(static::class)
            ->name($name)
            ->label($label ?? static::makeLabel($name));
    }

    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'label' => $this->getLabel(),
            'type' => $this->getType(),
            'icon' => $this->getIcon(),
            'extra' => $this->getExtra(),
            'action' => $this->hasAction(),
            'confirm' => $this->getConfirm()?->toArray(),
            ...$this->routeToArray(),
        ];
    }

    /**
     * @return array<string,mixed>
     */
    public function routeToArray(): array
    {
        if (! $this->hasRoute()) {
            return [];
        }

        return [
            'href' => $this->getRoute(),
            'method' => $this->getMethod(),
        ];
    }

    /**
     * Resolve the action's properties.
     *
     * @param  array<string,mixed>  $parameters
     * @param  array<string,mixed>  $typed
     * @return $this
     */
    public function resolve(array $parameters = [], array $typed = []): static
    {
        $this->resolveName($parameters, $typed);
        $this->resolveLabel($parameters, $typed);
        $this->resolveIcon($parameters, $typed);
        $this->resolveRoute($parameters, $typed);

        return $this;
    }

    /**
     * @return array<int,mixed>
     */
    public function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'confirm' => [$this->confirmInstance()],
            default => [],
        };
    }

    /**
     * @return array<int,mixed>
     */
    public function resolveDefaultClosureDependencyForEvaluationByType(string $parameterType): array
    {
        return match ($parameterType) {
            Confirm::class => [$this->confirmInstance()],
            default => [],
        };
    }
}
