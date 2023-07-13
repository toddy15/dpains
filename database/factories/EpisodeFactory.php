<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Employee;
use App\Models\Staffgroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class EpisodeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory()->create(),
            'name' => $this->faker->name(),
            'start_date' => $this->faker->numberBetween(2016, 2020).
                '-'.
                sprintf('%02d', $this->faker->numberBetween(1, 12)),
            'staffgroup_id' => Staffgroup::factory()->create(),
            'vk' => $this->faker->randomFloat(3, 0, 1),
            'factor_night' => $this->faker->randomFloat(3, 0, 1),
            'factor_nef' => $this->faker->randomFloat(3, 0, 1),
            'comment_id' => Comment::factory()->create(),
        ];
    }
}
