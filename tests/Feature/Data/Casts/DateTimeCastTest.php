<?php

declare(strict_types=1);

use Illuminate\Support\Carbon;
use Workbench\App\Data\DateData;
use Workbench\App\Models\Product;

beforeEach(function () {
    $this->product = Product::factory()->create();
});

// it('casts to datetime', function () {
//     expect(DateData::from($this->product))
//         ->created_at->toBe(Carbon::parse($this->product->created_at)->format('Y-m-d H:i:s'));
// });
