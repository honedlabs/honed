<?php

declare(strict_types=1);

namespace Honed\Honed\Responses\Concerns;

use Honed\Stats\Overview;

trait CanHaveStats
{
    /**
     * The stats to use for the response.
     *
     * @var bool|Overview|array<int,Stat>
     */
    protected $stats = false;

    /**
     * Set the stats to use for the response.
     *
     * @param  bool|array<int,Stat>|Overview  $stats
     */
    public function stats(bool|array|Overview $stats = true): static
    {
        $this->stats = $stats;

        return $this;
    }
}
