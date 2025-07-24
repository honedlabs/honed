<?php

declare(strict_types=1);

use Workbench\App\Enums\Status;

beforeEach(function () {});

it('creates label for enum', function () {
    expect(Status::ComingSoon)
        ->label()->toBe('Coming soon');
});

it('creates resource for enum', function () {
    expect(Status::resource())
        ->toEqual([
            [
                'value' => 'available',
                'label' => 'Available',
            ],
            [
                'value' => 'unavailable',
                'label' => 'Unavailable',
            ],
            [
                'value' => 'coming-soon',
                'label' => 'Coming soon',
            ],
        ]);
});
