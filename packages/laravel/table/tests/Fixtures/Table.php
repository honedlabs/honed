<?php

declare(strict_types=1);

namespace Honed\Table\Tests\Fixtures;

use Honed\Action\Confirm;
use Honed\Action\BulkAction;
use Honed\Action\PageAction;
use Honed\Refine\Sorts\Sort;
use Honed\Action\InlineAction;
use Honed\Table\Columns\Column;
use Honed\Refine\Filters\Filter;
use Honed\Refine\Searches\Search;
use Honed\Refine\Filters\SetFilter;
use Honed\Table\Columns\DateColumn;
use Honed\Table\Columns\TextColumn;
use Honed\Table\Tests\Stubs\Status;
use Honed\Refine\Filters\DateFilter;
use Honed\Table\Table as HonedTable;
use Honed\Table\Tests\Stubs\Product;
use Honed\Refine\Filters\CustomFilter;
use Honed\Table\Columns\BooleanColumn;
use Honed\Table\Columns\NumericColumn;
use Honed\Refine\Filters\BooleanFilter;

class Table extends HonedTable
{
    public $search = ['description'];

    public $toggle = true;

    public $pagination = [10, 25, 50];

    public $defaultPagination = 10;

    public $cookie = 'example-table';

    public function resource()
    {
        return Product::query()
            ->with(['seller', 'categories']);
    }

    public function columns()
    {
        return [
            Column::make('id')->key(),
            TextColumn::make('name')->searchable(),
            TextColumn::make('description')->placeholder('-'),
            BooleanColumn::make('best_seller', 'Favourite')->formatBoolean('Favourite', 'Not favourite'),
            TextColumn::make('seller.name', 'Sold by'),
            Column::make('status')->meta(['badge' => true]),
            NumericColumn::make('price'),
            DateColumn::make('created_at')->sortable(),
        ];
    }

    public function filters()
    {
        return [
            Filter::make('name')->like(),
            SetFilter::make('price', 'Maximum price')->options([10, 20, 50, 100])->lt(),
            SetFilter::make('status')->enum(Status::class)->multiple(),
            SetFilter::make('status', 'Single')->alias('only')->enum(Status::class),
            BooleanFilter::make('best_seller', 'Favourite')->alias('favourite'),
            DateFilter::make('created_at', 'Oldest')->alias('oldest')->gt(),
            DateFilter::make('created_at', 'Newest')->alias('newest')->lt(),
        ];
    }

    public function sorts()
    {
        return [
            Sort::make('name', 'A-Z')->alias('name-desc')->desc()->default(),
            Sort::make('name', 'Z-A')->alias('name-asc')->asc(),
            Sort::make('price'),
            Sort::make('best_seller', 'Favourite')->alias('favourite'),
        ];
    }

    public function searches()
    {
        return [
            Search::make('name'),
            Search::make('description'),
        ];
    }

    public function actions()
    {
        return [
            InlineAction::make('edit')
                ->action(fn (Product $product) => $product->update(['name' => 'Inline'])),
            InlineAction::make('delete')
                ->allow(fn (Product $product) => $product->id % 2 === 0)
                ->action(fn (Product $product) => $product->delete())
                ->confirm(fn (Confirm $confirm) => $confirm->name(fn (Product $product) => 'You are about to delete '.$product->name)->description('Are you sure?')),
            InlineAction::make('show')
                ->route(fn ($product) => route('products.show', $product)),
            BulkAction::make('update')
                ->action(fn (Product $product) => $product->update(['name' => 'Bulk'])),
            BulkAction::make('delete')
                ->action(fn (Product $product) => $product->delete())
                ->allow(false),

            PageAction::make('create')
                ->route('products.create'),

            PageAction::make('factory')
                ->action(fn () => product('test')),
        ];
    }
}
