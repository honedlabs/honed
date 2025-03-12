<?php

declare(strict_types=1);

namespace Honed\Action\Tests\Stubs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $guarded = [];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            $product->public_id = Str::uuid();
        });
    }

    protected $casts = [
        'status' => Status::class,
    ];

    /**
     * Make the product free.
     */
    public function makeFree(): void
    {
        $this->price = 0;
        $this->save();
    }
}
