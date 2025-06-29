<?php

declare(strict_types=1);

namespace Workbench\App\Tables;

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
    /**
     * Define the table.
     *
     * @param  $this  $table
     * @return $this
     */
    protected function definition(Table $table): Table
    {
        return $table
            ->for(Product::class);
    }
}
