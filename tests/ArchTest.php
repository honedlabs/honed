<?php

declare(strict_types=1);

use Illuminate\Console\Command;

arch()->preset()->php();

arch()->preset()->security();

arch('it will not use debugging functions')
    ->expect(['dd', 'dump', 'ray', 'sleep'])
    ->each->not->toBeUsed();

arch('strict types')
    ->expect('Honed\Honed')
    ->toUseStrictTypes();

arch('attributes')
    ->expect('Honed\Honed\Attributes')
    ->toBeClasses();

arch('concerns')
    ->expect('Honed\Honed\Concerns')
    ->toBeTraits();

arch('contracts')
    ->expect('Honed\Honed\Contracts')
    ->toBeInterfaces();

arch('commands')
    ->expect('Honed\Honed\Commands')
    ->classes()
    ->toExtend(Command::class);

arch('exceptions')
    ->expect('Honed\Honed\Exceptions')
    ->toExtend(Exception::class);
