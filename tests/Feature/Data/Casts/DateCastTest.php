<?php

declare(strict_types=1);

use Illuminate\Support\Carbon;
use Workbench\App\Data\DateData;
use Workbench\App\Models\Product;

beforeEach(function () {
    $this->product = Product::factory()->create();
});

// it('casts to date', function () {
//     $now = Carbon::now()->format('Y-m-d');

//     $data = DateData::validateAndCreate([
//         'created_at' => null,
//         'updated_at' => $now,
//     ]);
// });