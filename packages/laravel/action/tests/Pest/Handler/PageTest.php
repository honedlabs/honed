<?php

declare(strict_types=1);

namespace Tests\Pest\Handler;

use Honed\Action\Testing\PageActionRequest;
use Honed\Action\Tests\Stubs\Product;

use function Pest\Laravel\post;

beforeEach(function () {
    PageActionRequest::shouldFill();
});

it('executes the action', function () {
    $data = PageActionRequest::make()
        ->name('create.product.name')
        ->getData();

    $response = post(route('actions'), $data);
    
    $response->assertRedirect();

    $this->assertDatabaseHas('products', [
        'name' => 'name',
        'description' => 'name',
    ]);
});

it('is 404 for no name match', function () {
    $data = PageActionRequest::make()
        ->name('missing')
        ->getData();

    $response = post(route('actions'), $data);

    $response->assertNotFound();
}); 

it('is 403 if the action is not allowed', function () {
    $data = PageActionRequest::make()
        ->name('create.product.description')
        ->getData();

    $response = post(route('actions'), $data);

    $response->assertForbidden();
});

it('does not execute route actions', function () {
    $data = PageActionRequest::make()
        ->name('create')
        ->getData();

    $response = post(route('actions'), $data);

    $response->assertRedirect();
});

it('returns inertia response', function () {
    product();
    $data = PageActionRequest::make()
        ->name('price.10')
        ->getData();
    
    $response = post(route('actions'), $data);

    $response->assertInertia();
    
    expect(Product::all())
        ->toHaveCount(1)
        ->first()->price->toBe(10);
});

