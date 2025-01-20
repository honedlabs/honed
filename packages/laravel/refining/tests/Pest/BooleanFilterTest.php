<?php

declare(strict_types=1);

use Honed\Refining\Tests\Stubs\Product;
use Illuminate\Support\Facades\Request;
use Honed\Refining\Filters\BooleanFilter;

beforeEach(function () {
    $this->builder = Product::query();
    $this->param = 'is_active';
    $this->filter = BooleanFilter::make($this->param);
});

it('filters by boolean value', function () {
    $request = Request::create('/', 'GET', [$this->param => 'true']);
    
    $this->filter->apply($this->builder, $request);

    expect($this->builder->getQuery()->wheres)->toBeArray()
        ->toHaveCount(1)
        ->{0}->scoped(fn ($order) => $order
            ->{'column'}->toBe($this->builder->qualifyColumn('is_active'))
            ->{'value'}->toBe(true)
            ->{'operator'}->toBe('=')
            ->{'boolean'}->toBe('and')
        );

    expect($this->filter)
        ->isActive()->toBeTrue()
        ->getValue()->toBe(true);
});

it('does not filter if falsy value', function () {
    $request = Request::create('/', 'GET', [$this->param => '0']);
    
    $this->filter->apply($this->builder, $request);

    expect($this->builder->getQuery()->wheres)->toBeArray()->toBeEmpty();

    expect($this->filter)
        ->isActive()->toBeFalse()
        ->getValue()->toBeFalse();
});
