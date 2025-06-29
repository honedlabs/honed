<?php

declare(strict_types=1);

use Workbench\App\Enums\Status;

it('creates label for enum', function () {
    expect(Status::ComingSoon)
        ->label()->toBe('Coming Soon');
});

it('creates resource for enum', function () {
    expect(Status::toResource())
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
                'label' => 'Coming Soon',
            ],
        ]);
});
