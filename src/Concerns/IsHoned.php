<?php

declare(strict_types=1);

namespace Honed\Honed\Concerns;

use Honed\Action\Concerns\HasBatch;
use Honed\Table\Concerns\HasTable;

/**
 * @template TTable of \Honed\Table\Table = \Honed\Table\Table
 * @template TBatch of \Honed\Action\Batch = \Honed\Action\Batch
 */
trait IsHoned
{
    /** @use HasBatch<TActions> */
    use HasBatch;

    /** @use HasTable<TTable> */
    use HasTable;
}
