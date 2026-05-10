<?php

namespace Database\Factories;

use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Restaurant>
 */
class RestaurantFactory extends Factory
{
    public function definition(): array
    {
        return [
            'data' => [
                'status' => 10,
                'mapLatitude' => fake()->latitude(),
                'mapLongitude' => fake()->longitude(),
            ],
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'data' => array_merge($attributes['data'], ['status' => 5]),
        ]);
    }
}
