<?php

namespace Database\Factories;

use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Status>
 */
class StatusFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => 10,
            'text' => null,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'code' => 4,
            'text' => '已搬遷',
        ]);
    }
}
