<?php

declare(strict_types=1);

namespace Honed\Action\Tests\Stubs;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];

    protected $casts = [
        'status' => Status::class,
    ];
}