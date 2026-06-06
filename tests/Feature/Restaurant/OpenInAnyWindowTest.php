<?php

namespace Tests\Feature\Restaurant;

use App\Models\Hour;
use App\Models\Period;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class OpenInAnyWindowTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_matches_periods_that_overlap_range(): void
    {
        $validEndsAtStart = Restaurant::factory()
            ->has(Hour::factory()->has(Period::factory()->state(['start' => '09:00:00', 'end' => '10:00:00'])))
            ->create();
        $validOverlapsStart = Restaurant::factory()
            ->has(Hour::factory()->has(Period::factory()->state(['start' => '09:00:00', 'end' => '10:15:00'])))
            ->create();
        $validOverlapsEnd = Restaurant::factory()
            ->has(Hour::factory()->has(Period::factory()->state(['start' => '10:30:00', 'end' => '12:00:00'])))
            ->create();
        $validStartsAtEnd = Restaurant::factory()
            ->has(Hour::factory()->has(Period::factory()->state(['start' => '11:00:00', 'end' => '12:00:00'])))
            ->create();

        collect([
            ['start' => '08:00:00', 'end' => '09:59:00'],
            ['start' => '11:01:00', 'end' => '12:00:00'],
        ])->map(fn ($period) => Restaurant::factory()
            ->has(Hour::factory()->has(Period::factory()->state($period)))
            ->create()
        );

        $results = Restaurant::openInAnyWindowBetween(
            Carbon::createFromFormat('!H:i', '10:00'),
            Carbon::createFromFormat('!H:i', '11:00'),
        )
            ->orderBy('id')
            ->pluck('id')
            ->all();

        $this->assertEquals([
            $validEndsAtStart->id,
            $validOverlapsStart->id,
            $validOverlapsEnd->id,
            $validStartsAtEnd->id,
        ], $results);
    }

    public function test_matches_overnight_period_after_midnight(): void
    {
        $valid = Restaurant::factory()
            ->has(Hour::factory()->has(Period::factory()->state(['start' => '18:00:00', 'end' => '04:00:00'])))
            ->create();

        Restaurant::factory()
            ->has(Hour::factory()->has(Period::factory()->state(['start' => '18:00:00', 'end' => '01:59:00'])))
            ->create();

        $results = Restaurant::openInAnyWindowBetween(
            Carbon::createFromFormat('!H:i', '02:00'),
            Carbon::createFromFormat('!H:i', '02:30'),
        )->pluck('id')->all();

        $this->assertEquals([$valid->id], $results);
    }

    public function test_uses_base_open_hour_filters(): void
    {
        $valid = Restaurant::factory()->has(Hour::factory()->is24hr())->create();

        Restaurant::factory()->has(Hour::factory()->is24hr()->isClose())->create();
        Restaurant::factory()->has(Hour::factory()->nonZeroWeight()->is24hr())->create();
        Restaurant::factory()->inactive()->has(Hour::factory()->is24hr())->create();
        Restaurant::factory()->create();
        Restaurant::factory()->has(Hour::factory()->dayOfWeek(3))->create();

        $results = Restaurant::active()->openInAnyWindowBetween(
            Carbon::createFromFormat('!H:i', '10:00'),
            Carbon::createFromFormat('!H:i', '11:00'),
        )->pluck('id')->all();

        $this->assertEquals([$valid->id], $results);
    }
}
