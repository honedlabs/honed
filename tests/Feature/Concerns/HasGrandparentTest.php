<?php

declare(strict_types=1);

use Honed\Honed\Concerns\HasGrandparent;
use Honed\Honed\Contracts\HasGrandparent as HasGrandparentContract;
use Workbench\App\Models\User;

beforeEach(function () {
    $this->class = new class implements HasGrandparentContract
    {
        use HasGrandparent;
    };
});

it('has parent', function () {
    $user = User::factory()->create();
    expect($this->class)
        ->grandparent($user)->toBe($this->class)
        ->getGrandparent()->toBe($user);
});
