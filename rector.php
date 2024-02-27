<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/app',
        __DIR__.'/database',
        __DIR__.'/resources',
        __DIR__.'/routes/web.php',
        __DIR__.'/tests',
    ])
    ->withRootFiles()
    ->withSkip([
        __DIR__.'/database/factories/UserFactory.php',
        __DIR__.'/tests/Pest.php',
        __DIR__.'/app/Console',
        __DIR__.'/app/Exceptions',
        __DIR__.'/app/Http/Middleware',
        __DIR__.'/app/Providers',
    ]);
