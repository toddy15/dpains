<?php

declare(strict_types=1);

use function Pest\Laravel\get;

test('a guest cannot access the BD reports page', function () {
    get(route('reports.showbds'))
        ->assertRedirect(route('login'));
});

todo('a user can access the BD reports page');
