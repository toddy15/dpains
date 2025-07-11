<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Staffgroup;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Staffgroup> */
class StaffgroupFactory extends Factory
{
    public function definition(): array
    {
        return [
            'staffgroup' => $this->faker->jobTitle(),
            'weight' => $this->faker->numberBetween(1, 100),
        ];
    }
}
