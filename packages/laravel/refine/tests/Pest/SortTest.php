<?php

declare(strict_types=1);

use Honed\Refine\Sort;
use Honed\Refine\Tests\Stubs\Product;
use Illuminate\Support\Facades\Request;

beforeEach(function () {
    $this->param = 'name';
    $this->builder = Product::query();
    $this->sort = Sort::make($this->param);
});

it('has next direction', function () {
    expect($this->sort)
        ->getNextDirection()->toBe($this->sort->getAscendingValue())
        ->direction('asc')
        ->getNextDirection()->toBe($this->sort->getDescendingValue())
        ->direction('desc')
        ->getNextDirection()->toBeNull();
});

it('can invert direction', function () {
    expect($this->sort)
        ->isInverted()->toBeFalse()
        ->invert()->toBe($this->sort)
        ->isInverted()->toBeTrue()
        ->getNextDirection()->toBe($this->sort->getDescendingValue());
});

it('can enforce a singular direction', function () {
    $request = Request::create('/', 'GET', [$this->key => $this->param]);

    expect($this->sort)
        ->isSingularDirection()->toBeFalse()
        ->desc()->toBe($this->sort)
        ->isSingularDirection()->toBeTrue();

    expect($this->sort->apply($this->builder, $request, $this->key))
        ->toBeTrue();

    expect($this->builder->getQuery()->orders)->toBeArray()
        ->toHaveCount(1)
        ->{0}->scoped(fn ($order) => $order
            ->{'column'}->toBe($this->builder->qualifyColumn($this->param))
            ->{'direction'}->toBe(Sort::DESCENDING)
        );

    expect($this->sort)
        ->isActive()->toBeTrue()
        ->getDirection()->toBe(Sort::DESCENDING)
        ->getNextDirection()->toBe('-name');
});

it('has direction', function () {
    expect($this->sort)
        ->getAscendingValue()->toBe($this->param)
        ->getDescendingValue()->toBe('-'.$this->param);
});

it('has array representation', function () {
    expect($this->sort->toArray())->toEqual([
        'name' => $this->param,
        'label' => ucfirst($this->param),
        'type' => 'sort',
        'meta' => [],
        'active' => false,
        'direction' => null,
        'next' => $this->sort->getAscendingValue(),
    ]);
});
