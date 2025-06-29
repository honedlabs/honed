<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as AuthUser;
use Workbench\App\Models\Product;
use Workbench\App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();

    $this->actingAs($this->user);

    $this->product = Product::factory()->create();
});

it('has author and editor', function () {
    expect($this->product)
        ->author()->toBeInstanceOf(BelongsTo::class)
        ->author
        ->scoped(fn ($author) => $author
            ->toBeInstanceOf(User::class)
            ->id->toBe($this->user->id)
        )
        ->editor()->toBeInstanceOf(BelongsTo::class)
        ->editor
        ->scoped(fn ($editor) => $editor
            ->toBeInstanceOf(AuthUser::class)
            ->id->toBe($this->user->id)
        );
});

it('updates editor', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $this->product->update([
        'name' => 'Updated',
    ]);

    expect($this->product)
        ->author
        ->scoped(fn ($author) => $author
            ->toBeInstanceOf(AuthUser::class)
            ->id->toBe($this->user->id)
        )
        ->editor
        ->scoped(fn ($editor) => $editor
            ->toBeInstanceOf(AuthUser::class)
            ->id->toBe($user->id)
        );
});
