<?php

declare(strict_types=1);

namespace Workbench\App\Caches;

use Honed\Command\CacheManager;

/**
 * @template T of \Workbench\App\Models\User
 * @template U of array{name: string}
 *
 * @extends \Honed\Command\CacheManager<T, U>
 */
class UserCache extends CacheManager
{
    /**
     * {@inheritdoc}
     */
    public function key($parameter)
    {
        return [
            $parameter->getKey(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function value($parameter)
    {
        return [
            'name' => $parameter->name,
        ];
    }
}
