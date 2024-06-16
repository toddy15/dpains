<?php

declare(strict_types=1);

use Tests\Seeders\BDCalculationSeeder;

use function Pest\Laravel\seed;

test('it calculates the maximal number of BDs for an amployee', function () {
    seed(BDCalculationSeeder::class);

})->only();
