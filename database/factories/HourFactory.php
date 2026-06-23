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
            'day_of_week' => fake()->numberBetween(1, 7),
            'is_close' => false,
            'is_24hr' => false,
        ];
    }

    public function dayOfWeek(int $day): static
    {
        return $this->state(fn (array $attributes) => [
            'day_of_week' => $day,
        ]);
    }

    public function is24hr(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_24hr' => true,
        ]);
    }

    public function isClose(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_close' => true,
        ]);
    }
}
