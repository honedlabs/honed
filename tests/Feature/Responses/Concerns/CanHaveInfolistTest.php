<?php

declare(strict_types=1);

use Workbench\App\Http\Responses\ShowProduct;
use Workbench\App\Infolists\ProductInfolist;
use Workbench\App\Models\Product;

beforeEach(function () {
    $product = Product::factory()->create();

    $this->response = new ShowProduct($product);
});

it('has no infolist by default', function () {
    expect($this->response)
        ->getInfolist()->toBeNull();
});

it('has infolist from class string', function () {
    expect($this->response)
        ->infolist(ProductInfolist::class)->toBe($this->response)
        ->getInfolist()->toBeInstanceOf(ProductInfolist::class);
});

it('has infolist from instance', function () {
    expect($this->response)
        ->infolist(ProductInfolist::make())->toBe($this->response)
        ->getInfolist()->toBeInstanceOf(ProductInfolist::class);
});

it('has infolist from model', function () {
    expect($this->response)
        ->infolist()->toBe($this->response)
        ->getInfolist()->toBeInstanceOf(ProductInfolist::class);
});

it('has infolist props', function () {
    expect($this->response)
        ->canHaveInfolistToProps()->toBe([])
        ->infolist(ProductInfolist::make())->toBe($this->response)
        ->canHaveInfolistToProps()
        ->scoped(fn ($infolist) => $infolist
            ->toBeArray()
            ->toHaveCount(1)
            ->toHaveKey(ShowProduct::INFOLIST_PROP)
            ->{ShowProduct::INFOLIST_PROP}->toBeArray()
        );
});
