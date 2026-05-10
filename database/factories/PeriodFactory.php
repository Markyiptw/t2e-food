<?php

namespace Database\Factories;

use App\Models\Period;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Period>
 */
class PeriodFactory extends Factory
{
    public function definition(): array
    {
        return [
            'position' => 1,
            'start' => '09:00:00',
            'end' => '17:00:00',
        ];
    }
}
