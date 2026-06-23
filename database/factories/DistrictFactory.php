<?php

namespace Database\Factories;

use App\Models\District;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<District>
 */
class DistrictFactory extends Factory
{
    public function definition(): array
    {
        return [
            'external_id' => fake()->unique()->randomNumber(4),
            'name' => fake()->city(),
        ];
    }
}
