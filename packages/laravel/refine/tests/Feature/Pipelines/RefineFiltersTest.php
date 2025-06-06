<?php

declare(strict_types=1);

use Honed\Refine\Filter;
use Honed\Refine\Pipelines\RefineFilters;
use Honed\Refine\Refine;
use Illuminate\Support\Facades\Request;
use Workbench\App\Models\User;

beforeEach(function () {
    $this->builder = User::query();
    $this->pipe = new RefineFilters();
    $this->closure = fn ($refine) => $refine;

    $filters = [
        Filter::make('price')->int(),
    ];

    $this->refine = Refine::make($this->builder)
        ->withFilters($filters);

});

it('does not refine', function () {
    $request = Request::create('/', 'GET', [
        'invalid' => 'test',
    ]);

    $this->refine->request($request);

    $this->pipe->__invoke($this->refine, $this->closure);

    expect($this->refine->getResource()->getQuery()->wheres)
        ->toBeEmpty();
});

it('refines', function () {
    $request = Request::create('/', 'GET', [
        'price' => 100,
    ]);

    $this->refine->request($request);

    $this->pipe->__invoke($this->refine, $this->closure);

    $builder = $this->refine->getResource();

    expect($builder->getQuery()->wheres)
        ->toBeOnlyWhere('price', 100);
});

it('disables', function () {
    $request = Request::create('/', 'GET', [
        'price' => 100,
    ]);

    $this->refine->request($request)->disableFiltering();

    $this->pipe->__invoke($this->refine, $this->closure);

    $builder = $this->refine->getResource();

    expect($builder->getQuery()->wheres)
        ->toBeEmpty();
});

describe('scope', function () {
    beforeEach(function () {
        $this->refine = $this->refine->scope('scope');
    });

    it('does not refine', function () {
        $request = Request::create('/', 'GET', [
            'price' => 100,
        ]);

        $this->refine->request($request);

        $this->pipe->__invoke($this->refine, $this->closure);

        $builder = $this->refine->getResource();

        expect($builder->getQuery()->wheres)
            ->toBeEmpty();
    });

    it('refines', function () {
        $request = Request::create('/', 'GET', [
            $this->refine->formatScope('price') => 100,
        ]);

        $this->refine->request($request);

        $this->pipe->__invoke($this->refine, $this->closure);

        $builder = $this->refine->getResource();

        expect($builder->getQuery()->wheres)
            ->toBeOnlyWhere('price', 100);
    });
});
