<?php

test('a guest can view the homepage', function (): void {
    $this->get('/')
        ->assertOk()
        ->assertViewIs('homepage');
});
