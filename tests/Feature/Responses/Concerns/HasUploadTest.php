<?php

declare(strict_types=1);

use Workbench\App\Http\Responses\CreateProduct;
use Workbench\App\Uploads\FileUpload;

beforeEach(function () {
    $this->store = route('products.create');

    $this->response = new CreateProduct($this->store);
});

it('has upload', function () {
    expect($this->response)
        ->getUpload()->toBeNull()
        ->missingUpload()->toBeTrue()
        ->hasUpload()->toBeFalse()
        ->upload(FileUpload::class)->toBe($this->response)
        ->getUpload()->toBeInstanceOf(FileUpload::class)
        ->missingUpload()->toBeFalse()
        ->hasUpload()->toBeTrue();
});

it('has upload props', function () {
    expect($this->response)
        ->hasUploadToProps()
        ->scoped(fn ($upload) => $upload
            ->toBeArray()
            ->toBeEmpty()
        )
        ->upload(FileUpload::make())->toBe($this->response)
        ->hasUploadToProps()
        ->scoped(fn ($upload) => $upload
            ->toBeArray()
            ->toHaveCount(1)
            ->toHaveKey(CreateProduct::UPLOAD_PROP)
            ->{CreateProduct::UPLOAD_PROP}
            ->scoped(fn ($upload) => $upload
                ->toBeArray()
                ->toHaveKeys([
                    'multiple',
                    'message',
                    'extensions',
                    'mimes',
                    'size',
                ])
            )
        );
});
