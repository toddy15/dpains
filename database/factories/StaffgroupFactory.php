<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class StaffgroupFactory extends Factory
{
    public function definition()
    {
        return [
            'staffgroup' => $this->faker->jobTitle(),
            'weight' => $this->faker->numberBetween(1, 100),
        ];
    }
}
