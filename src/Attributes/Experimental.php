<?php

declare(strict_types=1);

namespace Honed\Honed\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_ALL)]
class Experimental
{
    /**
     * Create a new attribute instance.
     */
    public function __construct() {}
}
