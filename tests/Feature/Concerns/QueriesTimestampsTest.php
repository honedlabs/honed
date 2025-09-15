<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Date;
use Workbench\App\Models\Product;

beforeEach(function () {
    $this->freezeTime();

    $this->builder = Product::query();
});

it('scopes the query to be after custom number of days and column', function () {
    expect($this->builder->after(3, 'updated_at'))
        ->toBeInstanceOf(Builder::class)
        ->getQuery()
        ->scoped(fn ($query) => $query
            ->wheres
            ->scoped(fn ($wheres) => $wheres
                ->toBeArray()
                ->toHaveCount(1)
                ->{0}
                ->scoped(fn ($where) => $where
                    ->toBeArray()
                    ->toHaveCount(5)
                    ->toHaveKeys(['column', 'type', 'boolean', 'value', 'operator'])
                    ->{'column'}->toBe('updated_at')
                    ->{'type'}->toBe('Date')
                    ->{'boolean'}->toBe('and')
                    ->{'value'}->toBe(Date::now()->subDays(3)->toDateString())
                    ->{'operator'}->toBe('>=')
                )
            )
        );
});

it('scopes the query to be before custom number of days and column', function () {
    expect($this->builder->before(3, 'updated_at'))
        ->toBeInstanceOf(Builder::class)
        ->getQuery()
        ->scoped(fn ($query) => $query
            ->wheres
            ->scoped(fn ($wheres) => $wheres
                ->toBeArray()
                ->toHaveCount(1)
                ->{0}
                ->scoped(fn ($where) => $where
                    ->toBeArray()
                    ->toHaveCount(5)
                    ->toHaveKeys(['column', 'type', 'boolean', 'value', 'operator'])
                    ->{'column'}->toBe('updated_at')
                    ->{'type'}->toBe('Date')
                    ->{'boolean'}->toBe('and')
                    ->{'value'}->toBe(Date::now()->subDays(3)->toDateString())
                    ->{'operator'}->toBe('<=')
                )
            )
        );
});

it('scopes the query to days', function (string $method, int $days) {
    expect($this->builder->{$method}())
        ->toBeInstanceOf(Builder::class)
        ->getQuery()
        ->scoped(fn ($query) => $query
            ->wheres
            ->scoped(fn ($wheres) => $wheres
                ->toBeArray()
                ->toHaveCount(1)
                ->{0}
                ->scoped(fn ($where) => $where
                    ->toBeArray()
                    ->toHaveCount(5)
                    ->toHaveKeys(['column', 'type', 'boolean', 'value', 'operator'])
                    ->{'column'}->toBe('created_at')
                    ->{'type'}->toBe('Date')
                    ->{'boolean'}->toBe('and')
                    ->{'value'}->toBe(Date::now()->subDays($days)->toDateString())
                    ->{'operator'}->toBe('>=')
                )
            )
        );
})->with([
    'today' => ['today', 1],
    'pastWeek' => ['pastWeek', 7],
    'pastMonth' => ['pastMonth', 30],
    'pastQuarter' => ['pastQuarter', 90],
    'pastYear' => ['pastYear', 365],
]);
