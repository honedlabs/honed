<?php

declare(strict_types=1);

namespace Honed\Action;

use Honed\Core\Concerns\IsDefault;
use Honed\Core\Contracts\HigherOrder;
use Honed\Core\Contracts\ProxiesHigherOrder;
use Honed\Core\Link\Concerns\Linkable;
use Honed\Core\Link\Proxies\HigherOrderLink;

class InlineAction extends Action implements ProxiesHigherOrder
{
    // use Confirmable;
    use IsDefault;
    use Concerns\MorphsAction;
    use Linkable;
    use Concerns\HasAction;

    public function setUp(): void
    {
        $this->type(Creator::Inline);
    }

    public function __get(string $property): HigherOrder
    {
        return match ($property) {
            // 'confirm' => new HigherOrderConfirm($this),
            'link' => new HigherOrderLink($this),
            default => parent::__get($property),
        };
    }

    public function toArray(): array
    {
        return \array_merge(parent::toArray(), [
            'action' => $this->hasAction(),
            // 'confirm' => $this->confirm(),
            'link' => $this->link(),
        ]);
    }

    /**
     * Morph this action to accomodate for bulk requests.
     * 
     * @return $this
     */
    public function isAlsoBulk()
    {
        return $this->morph();
    }

    public function handle()
    {
        //
    }
}