<?php

declare(strict_types=1);

use Honed\Action\Support\Constants;
use Honed\Action\Testing\PageRequest;

beforeEach(function () {
    $this->request = new PageRequest;
});

it('has data', function () {
    expect($this->request)
        ->getData()->scoped(fn ($data) => $data
        ->toBeArray()
        ->toHaveKeys(['type', 'id', 'name'])
        ->{'type'}->toBe(Constants::PAGE)
        )
        ->data(['type' => 'test'])->toBe($this->request)
        ->getData()->scoped(fn ($data) => $data
        ->toBeArray()
        ->toHaveKeys(['type', 'id', 'name'])
        ->{'type'}->toBe('test')
        );
});
