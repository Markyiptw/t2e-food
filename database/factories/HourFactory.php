<?php

namespace Database\Factories;

use App\Models\Hour;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Hour>
 */
class HourFactory extends Factory
{
    public function definition(): array
    {
        return [
            'data' => [
                'dayOfWeek' => fake()->numberBetween(1, 7),
                'weight' => 0,
                'isClose' => false,
                'is24hr' => false,
            ],
        ];
    }

    public function dayOfWeek(int $day): static
    {
        return $this->state(fn (array $attributes) => [
            'data' => array_merge($attributes['data'], ['dayOfWeek' => $day]),
        ]);
    }

    public function is24hr(): static
    {
        return $this->state(fn (array $attributes) => [
            'data' => array_merge($attributes['data'], ['is24hr' => true]),
        ]);
    }

    public function isClose(): static
    {
        return $this->state(fn (array $attributes) => [
            'data' => array_merge($attributes['data'], ['isClose' => true]),
        ]);
    }

    public function nonZeroWeight(int $weight = 4): static
    {
        return $this->state(fn (array $attributes) => [
            'data' => array_merge($attributes['data'], ['weight' => $weight]),
        ]);
    }
}
