<?php

use function Pest\Laravel\get;

test('a guest can access the homepage', function () {
    get('/')->assertOk();
});
