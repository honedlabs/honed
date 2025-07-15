<?php

declare(strict_types=1);

namespace Honed\Honed\Tests\Feature\Actions;

use Illuminate\Support\Facades\Cache;
use Workbench\App\Actions\FlushUserCache;
use Workbench\App\Caches\UserCache;
use Workbench\App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();

    $this->cache = new UserCache();

    $this->action = new FlushUserCache();
})->skip();

// it('sets value in cache', function () {
//     Cache::put($this->user, [
//         'name' => $this->user->name,
//     ]);

//     expect($this->cache->get($this->user))->toBe([
//         'name' => $this->user->name,
//     ]);

//     $this->action->handle($this->user);

//     expect(Cache::get($this->user->getRouteKey()))->toBeNull();
// });
