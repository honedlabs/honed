<?php

declare(strict_types=1);

namespace Honed\Honed\Responses\Concerns;

use Honed\Honed\Contracts\ViewsModel;
use Honed\Infolist\Infolist;

trait CanHaveInfolist
{
    public const INFOLIST_PROP = 'infolist';

    /**
     * The infolist to use for the view.
     *
     * @var bool|class-string<Infolist>|Infolist
     */
    protected $infolist = false;

    /**
     * Set the infolist.
     *
     * @param  bool|class-string<Infolist>|Infolist  $value
     * @return $this
     */
    public function infolist(bool|string|Infolist $value = true): static
    {
        $this->infolist = $value;

        return $this;
    }

    /**
     * Get the infolist to use for the view.
     */
    public function getInfolist(): ?Infolist
    {
        if (! $this instanceof ViewsModel) {
            return null;
        }

        return match (true) {
            is_string($this->infolist) => ($this->infolist)::make()->record($this->getModel()),
            $this->infolist instanceof Infolist => $this->infolist->record($this->getModel()),
            $this->infolist === true => $this->getModel()->infolist(), // @phpstan-ignore-line method.notFound
            default => null,
        };
    }

    /**
     * Convert the infolist to props.
     *
     * @return array<string, mixed>
     */
    public function canHaveInfolistToProps(): array
    {
        if ($infolist = $this->getInfolist()) {
            return [
                self::INFOLIST_PROP => $infolist->toArray(),
            ];
        }

        return [];
    }
}
