<?php

declare(strict_types=1);

namespace Workbench\App\Builders;

use Honed\Honed\Concerns\QueriesTimestamps;
use Illuminate\Database\Eloquent\Builder;

/**
 * @template TModel of \App\Models\Product
 *
 * @extends \Illuminate\Database\Eloquent\Builder<TModel>
 */
class ProductBuilder extends Builder
{
    use QueriesTimestamps;
}
