<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php81\Rector\FuncCall\NullToStrictStringFuncCallArgRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__.'/app',
        __DIR__.'/database',
        __DIR__.'/resources',
        __DIR__.'/routes/web.php',
        __DIR__.'/tests',
    ]);

    $rectorConfig->skip([
        __DIR__.'/database/factories/UserFactory.php',
        __DIR__.'/tests/Pest.php',
        __DIR__.'/app/Console',
        __DIR__.'/app/Exceptions',
        __DIR__.'/app/Http/Middleware',
        __DIR__.'/app/Providers',
    ]);

    // register a single rule
    $rectorConfig->rule(NullToStrictStringFuncCallArgRector::class);

    // define sets of rules
    $rectorConfig->sets([
        //        LevelSetList::UP_TO_PHP_82,
        //        LaravelSetList::LARAVEL_90,
    ]);
};
