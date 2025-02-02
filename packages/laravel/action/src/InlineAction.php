<?php

declare(strict_types=1);

namespace Honed\Action;

use Honed\Action\Contracts\HasHandler;
use Honed\Core\Concerns\HasDestination;
use Honed\Core\Concerns\IsDefault;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Traits\ForwardsCalls;

class InlineAction extends Action
{
    use Concerns\HasConfirm;
    use IsDefault;

    protected $type = Creator::Inline;

    public function toArray(): array
    {
        return \array_merge(parent::toArray(), [
            'action' => $this->hasAction(),
            'default' => $this->isDefault(),
            'confirm' => $this->getConfirm(),
        ]);
    }

    /**
     * @param  array<string,mixed>|\Illuminate\Database\Eloquent\Model  $parameters
     * @param  array<string,mixed>  $typed
     */
    public function resolve($parameters = [], $typed = []): static
    {
        $this->getDestination($parameters, $typed);
        // $this->resolveRoute

        return parent::resolve($parameters, $typed);
    }

    /**
     * Execute the action handler using the provided data.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $record
     * @return \Illuminate\Contracts\Support\Responsable|\Illuminate\Http\RedirectResponse|void
     */
    public function execute($record)
    {
        if (! $this->hasAction()) {
            return;
        }

        [$model, $singular] = $this->getActionParameterNames($record);

        return $this instanceof HasHandler
            ? \call_user_func([$this, 'handle'], $record)
            : $this->evaluate($this->getAction(), [
                'model' => $model,
                'record' => $record,
                $singular => $record,
            ], [
                Model::class => $record,
                $model::class => $record,
            ]);
    }
}
