<?php

namespace Workbench\App\Tables;

use Conquest\Table\Table;
use Workbench\App\Enums\TestRole;
use Workbench\App\Models\TestUser;
use Conquest\Table\Refining\Sorts\Sort;
use Conquest\Table\Columns\Column;
use Illuminate\Database\Eloquent\Builder;
use Conquest\Table\Refining\Filters\Filter;
use Conquest\Table\Refining\Options\Option;
use Conquest\Table\Actions\BulkAction;
use Conquest\Table\Actions\PageAction;
use Conquest\Table\Actions\InlineAction;
use Conquest\Table\Refining\Filters\QueryFilter;
use Conquest\Table\Refining\Filters\SelectFilter;
use Conquest\Table\Actions\BaseAction;

class BasicTable extends Table
{
    // protected function definePagination()
    // {
    //     return 10;
    // }

    protected $model = TestUser::class;

    protected function defineColumns(): array
    {
        return [
            Column::make('id')->asKey()->hide(),
            Column::make('name')->sort(),
            Column::make('email')->fallback('No email')->sort(),
            Column::make('created_at')->transform(fn ($value) => $value->format('d/m/Y H:i:s')),
            Column::make('role')->label('User Role'),
            Column::make('non_existent')->fallback('Fake')
        ];
    }

    protected function defineRefinements(): array
    {
        return [
            Filter::make('name')->loose(),
            SelectFilter::make('role', 'type')->options(Option::enum(TestRole::class, 'label')),
            QueryFilter::make('id')->query(fn (Builder $builder, $value) => $builder->where('id', '<', $value)),
            
            Sort::make('created_at', 'newest')->desc()->default(),
            Sort::make('created_at', 'oldest')->asc(),
        ];
    }

    protected function defineActions(): array
    {
        return [
            PageAction::make('add')->label('Add User'),

            BulkAction::make('delete')->label('Delete Users'),

            InlineAction::make('view')->label('View User')->default(),
            InlineAction::make('edit')->label('Edit User'),
            InlineAction::make('delete')->label('Delete User')

        ];
    }
}