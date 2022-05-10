<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EmployeeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'email' => $this->faker->safeEmail(),
            'hash' => Str::random(),
            'bu_start' => $this->faker->randomElement(['even', 'odd']),
        ];
    }
}
