<?php

declare(strict_types=1);

namespace Workbench\App\Tables;

use Honed\Honed\Concerns\CanDeferLoading;
use Honed\Table\Table;
use Workbench\App\Models\Product;

/**
 * @template TModel of \Workbench\App\Models\Product
 * @template TBuilder of \Illuminate\Database\Eloquent\Builder<TModel>
 *
 * @extends \Honed\Table\Table<TModel, TBuilder>
 */
class ProductTable extends Table
{
    use CanDeferLoading;

    /**
     * Define the table.
     *
     * @return $this
     */
    protected function definition(): static
    {
        return $this
            ->for(Product::class)
            ->key('id');
    }
}
