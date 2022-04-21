<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Staffgroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class DueShiftFactory extends Factory
{
    public function definition()
    {
        return [
            'year' => $this->faker->numberBetween(2016, 2058),
            'staffgroup_id' => Staffgroup::factory()->create(),
            'nights' => $this->faker->numberBetween(40, 60),
            'nefs' => $this->faker->numberBetween(15, 35),
        ];
    }
}
