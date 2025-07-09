<?php

declare(strict_types=1);

namespace Honed\Honed\Responses\Concerns;

use Honed\Honed\Contracts\Modelable;
use Honed\Stats\Overview;

trait CanHaveStats
{
    /**
     * The stats to use for the view.
     *
     * @var bool|class-string<Overview>|Overview
     */
    protected $stats = false;

    /**
     * Set the stats.
     *
     * @param  bool|class-string<Overview>|Overview  $value
     * @return $this
     */
    public function stats(bool|string|Overview $value = true): static
    {
        $this->stats = $value;

        return $this;
    }

    /**
     * Get the stats to use for the view.
     */
    public function getStats(): ?Overview
    {
        return match (true) {
            is_string($this->stats) => ($this->stats)::make(),
            $this->stats instanceof Overview => $this->stats,
            $this->stats === true && $this instanceof Modelable => $this->getModel()->stats(), // @phpstan-ignore-line method.notFound
            default => null,
        };
    }

    /**
     * Convert the stats to props.
     *
     * @return array<string, mixed>
     */
    public function canHaveStatsToProps(): array
    {
        if ($stats = $this->getStats()) {
            return $stats->toArray();
        }

        return [];
    }
}
