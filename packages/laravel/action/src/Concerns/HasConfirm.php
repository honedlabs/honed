<?php

declare(strict_types=1);

namespace Honed\Action\Concerns;

use Honed\Action\Confirm;

trait HasConfirm
{
    /**
     * @var \Honed\Action\Confirm|null
     */
    protected $confirm;

    /**
     * Set the confirm for the instance.
     *
     * @param  \Honed\Action\Confirm|\Closure|string|null  $confirm
     * @return $this
     */
    public function confirm($confirm, ?string $description = null): static
    {
        match (true) {
            \is_null($confirm) => null,
            $confirm instanceof Confirm => $this->confirm = $confirm,
            $this->callsConfirm($confirm) => \call_user_func($confirm, $this->newConfirm()), // @phpstan-ignore-line
            default => $this->newConfirm()->title($confirm)->description($description)
        };

        return $this;
    }

    /**
     * Get the confirm for this instance.
     */
    public function getConfirm(): ?Confirm
    {
        return $this->confirm;
    }

    /**
     * Resolve the confirm for this instance.
     *
     * @param  array<string,mixed>|\Illuminate\Database\Eloquent\Model|null  $parameters
     * @param  array<string,mixed>|null  $typed
     * @return $this
     */
    public function resolveConfirm($parameters = null, $typed = null): static
    {
        $this->getConfirm()?->resolve($parameters, $typed);

        return $this;
    }

    /**
     * Determine if the instance has a confirm.
     */
    public function hasConfirm(): bool
    {
        return ! \is_null($this->confirm);
    }

    /**
     * Access the confirm for this instance.
     */
    private function newConfirm(): Confirm
    {
        return $this->confirm ??= Confirm::make();
    }

    /**
     * Determine if the confirm is a closure that modifies the confirm on the instance.
     */
    private function callsConfirm(mixed $confirm): bool
    {
        if (! $confirm instanceof \Closure) {
            return false;
        }

        $parameter = collect((new \ReflectionFunction($confirm))->getParameters())->first();
        
        $type = $parameter?->getType();

        return ($type instanceof \ReflectionNamedType && $type->getName() === Confirm::class)
            || ($parameter?->getName() === 'confirm');
    }
}
