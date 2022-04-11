<?php

use function Pest\Laravel\get;

test('a guest can view the homepage', function () {
    get('/')
        ->assertOk()
        ->assertViewIs('homepage');
});
