<?php

declare(strict_types=1);

namespace Honed\Action\Concerns;

use Honed\Action\BulkAction;
use Honed\Action\PageAction;
use Honed\Action\InlineAction;

trait HasActions
{
    /**
     * @var array<int,\Honed\Action\Action>
     */
    public $actions;

    /**
     * Get the actions for the instance.
     * 
     * @return \Illuminate\Support\Collection<int,\Honed\Action\Action>
     */
    public function getActions()
    {
        return collect(match(true) {
            \property_exists($this, 'actions') && ! \is_null($this->actions) => $this->actions,
            \method_exists($this, 'actions') => $this->actions(),
            default => [],
        });
    }

    /**
     * Determine if the instance has actions.
     * 
     * @return bool
     */
    public function hasActions()
    {
        return $this->getActions()->isNotEmpty();
    }

    /**
     * Get the inline actions for the instance.
     * 
     * @return \Illuminate\Support\Collection<int,\Honed\Action\InlineAction>
     */
    public function inlineActions()
    {
        return $this->getActions()
            ->filter(static fn ($action) => $action instanceof InlineAction)
            ->values();
    }

    /**
     * Get the bulk actions for the instance.
     * 
     * @return \Illuminate\Support\Collection<int,\Honed\Action\BulkAction>
     */
    public function bulkActions()
    {
        return $this->getActions()
            ->filter(static fn ($action) => $action instanceof BulkAction)
            ->values();
    }

    /**
     * Get the page actions for the instance.
     * 
     * @return \Illuminate\Support\Collection<int,\Honed\Action\PageAction>
     */
    public function pageActions()
    {
        return $this->getActions()
            ->filter(static fn ($action) => $action instanceof PageAction)
            ->values();
    }
}