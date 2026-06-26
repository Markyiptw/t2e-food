<?php

namespace Database\Factories;

use App\Models\Location;
use App\Models\Restaurant;
use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Restaurant>
 */
class RestaurantFactory extends Factory
{
    public function definition(): array
    {
        return [
            'poi_id' => fake()->unique()->randomNumber(6),
            'name' => fake()->name(),
            'url' => fake()->optional()->url(),
            'status_id' => fn () => Status::active()->id,
            'address' => fake()->optional()->streetAddress(),
            'district_id' => null,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_id' => Status::firstOrCreate(['code' => 4, 'text' => '已搬遷'])->id,
        ]);
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Restaurant $restaurant) {
            if (! $restaurant->location()->exists()) {
                Location::factory()->create(['restaurant_id' => $restaurant->id]);
            }
        });
    }
}
