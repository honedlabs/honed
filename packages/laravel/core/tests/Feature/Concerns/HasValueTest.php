<?php

declare(strict_types=1);

use Honed\Core\Concerns\HasValue;

beforeEach(function () {
    $this->test = new class()
    {
        use HasValue;
    };
});

it('sets', function () {
    expect($this->test)
        ->getValue()->toBeNull()
        ->value('test')->toBe($this->test)
        ->getValue()->toBe('test');
});
