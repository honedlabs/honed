<?php

declare(strict_types=1);

namespace Honed\Honed\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class UseData
{
    /**
     * Create a new attribute instance.
     *
     * @param  class-string<\Spatie\LaravelData\Data>  $dataClass
     * @return void
     */
    public function __construct(
        public string $dataClass
    ) {}
}
