<?php

declare(strict_types=1);

namespace Honed\Honed\Concerns;

use Honed\Action\Concerns\HasBatch;
use Honed\Infolist\Concerns\HasInfolist;
use Honed\Stats\Concerns\HasOverview;
use Honed\Table\Concerns\HasTable;

/**
 * @template TTable of \Honed\Table\Table = \Honed\Table\Table
 * @template TBatch of \Honed\Action\Batch = \Honed\Action\Batch
 * @template TInfolist of \Honed\Infolist\Infolist = \Honed\Infolist\Infolist
 * @template TStats of \Honed\Stats\Overview = \Honed\Stats\Overview
 */
trait IsHoned
{
    /** @use HasBatch<TActions> */
    use HasBatch;

    /** @use HasInfolist<TInfolist> */
    use HasInfolist;

    /** @use HasOverview<TStats> */
    use HasOverview;

    /** @use HasTable<TTable> */
    use HasTable;

    use Transferable;
}
