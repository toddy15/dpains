<?php

test('a guest can view the homepage', function () {
    $this->get('/')
        ->assertOk()
        ->assertViewIs('homepage');
});
