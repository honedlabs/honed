<?php

declare(strict_types=1);

namespace Workbench\App\Actions;

use Honed\Honed\Actions\FlushCache;
use Workbench\App\Caches\UserCache;

/**
 * @template TInput of \Workbench\App\Models\User
 */
class FlushUserCache extends FlushCache
{
    /**
     * {@inheritdoc}
     */
    public function cache(): string
    {
        return UserCache::class;
    }
}
