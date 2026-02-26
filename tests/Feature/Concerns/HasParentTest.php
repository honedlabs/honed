<?php

declare(strict_types=1);

use Honed\Honed\Concerns\HasParent;
use Honed\Honed\Contracts\HasParent as HasParentContract;
use Workbench\App\Models\User;

beforeEach(function () {
    $this->class = new class() implements HasParentContract
    {
        use HasParent;
    };
});

it('has parent', function () {
    $user = User::factory()->create();
    expect($this->class)
        ->parent($user)->toBe($this->class)
        ->getParent()->toBe($user);
});
