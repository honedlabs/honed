<?php

use Illuminate\Console\Command;

arch()->preset()->php();

arch()->preset()->security();

arch('it will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();

arch('strict types')
    ->expect('Honed\Refine')
    ->not->toUseStrictTypes();

arch('concerns')
    ->expect('Honed\Refine\Concerns')
    ->toBeTraits();

arch('contracts')
    ->expect('Honed\Refine\Contracts')
    ->toBeInterfaces();

arch('commands')
    ->expect('Honed\Refine\Console\Commands')
    ->toBeClasses()
    ->toExtend(Command::class);

arch('enums')
    ->expect('Honed\Refine\Enums')
    ->toBeEnums();

arch('pipelines')
    ->expect('Honed\Refine\Pipelines')
    ->toBeClasses();
