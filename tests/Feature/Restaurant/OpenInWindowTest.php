<?php

namespace Tests\Feature\Restaurant;

use App\Models\Hour;
use App\Models\Period;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class OpenInWindowTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_it_matches_restaurant_with_periods_covering_window(): void
    {
        $restaurant = Restaurant::factory()
            ->has(
                Hour::factory()
                    ->dayOfWeek(2)
                    ->has(Period::factory()->state(['start' => '09:00:00', 'end' => '17:00:00']))
            )
            ->create();

        $results = Restaurant::openInWindow('10:00:00', '14:00:00')->pluck('id');

        $this->assertCount(1, $results);
        $this->assertSame($restaurant->id, $results->first());
    }

    public function test_it_requires_period_to_fully_cover_the_window(): void
    {
        $restaurant = Restaurant::factory()
            ->has(
                Hour::factory()
                    ->dayOfWeek(2)
                    ->has(Period::factory()->state(['start' => '10:00:00', 'end' => '14:00:00']))
            )
            ->create();

        $results = Restaurant::openInWindow('08:00:00', '11:00:00')->pluck('id');

        $this->assertEmpty($results);
    }

    public function test_it_excludes_restaurant_without_any_hours(): void
    {
        Restaurant::factory()->create();

        $results = Restaurant::openInWindow('10:00:00', '14:00:00')->pluck('id');

        $this->assertEmpty($results);
    }

    public function test_it_excludes_restaurant_with_hour_but_no_periods_and_not_24hr(): void
    {
        Restaurant::factory()
            ->has(Hour::factory()->dayOfWeek(3))
            ->create();

        $results = Restaurant::openInWindow('10:00:00', '14:00:00')->pluck('id');

        $this->assertEmpty($results);
    }

    public function test_it_matches_restaurants_regardless_of_weekday(): void
    {
        $monday = Restaurant::factory()->has(Hour::factory()->dayOfWeek(1)->is24hr())->create();
        $friday = Restaurant::factory()->has(Hour::factory()->dayOfWeek(5)->is24hr())->create();

        $results = Restaurant::openInWindow('10:00:00', '14:00:00')->pluck('id');

        $this->assertCount(2, $results);
        $this->assertContains($monday->id, $results->all());
        $this->assertContains($friday->id, $results->all());
    }

    public function test_it_excludes_is_close(): void
    {
        $open = Restaurant::factory()->has(Hour::factory()->is24hr())->create();
        $closed = Restaurant::factory()->has(Hour::factory()->is24hr()->isClose())->create();

        $results = Restaurant::openInWindow('10:00:00', '14:00:00')->pluck('id');

        $this->assertCount(1, $results);
        $this->assertSame($open->id, $results->first());
    }

    public function test_it_excludes_non_zero_weight(): void
    {
        $base = Restaurant::factory()->has(Hour::factory()->is24hr())->create();
        $holiday = Restaurant::factory()->has(Hour::factory()->nonZeroWeight()->is24hr())->create();

        $results = Restaurant::openInWindow('10:00:00', '14:00:00')->pluck('id');

        $this->assertCount(1, $results);
        $this->assertSame($base->id, $results->first());
    }

    public function test_it_excludes_inactive_restaurants(): void
    {
        $inactive = Restaurant::factory()->inactive()->has(Hour::factory()->is24hr())->create();

        $results = Restaurant::withoutGlobalScope('active')
            ->openInWindow('10:00:00', '14:00:00')
            ->pluck('id');

        $this->assertCount(1, $results);
        $this->assertSame($inactive->id, $results->first());

        $resultsWithScope = Restaurant::openInWindow('10:00:00', '14:00:00')->pluck('id');

        $this->assertEmpty($resultsWithScope);
    }

    public function test_it_matches_overnight_period_covering_overnight_query(): void
    {
        $restaurant = Restaurant::factory()
            ->has(
                Hour::factory()
                    ->dayOfWeek(4)
                    ->has(Period::factory()->state(['start' => '22:00:00', 'end' => '02:00:00']))
            )
            ->create();

        $results = Restaurant::openInWindow('23:00:00', '01:00:00')->pluck('id');

        $this->assertCount(1, $results);
        $this->assertSame($restaurant->id, $results->first());
    }

    public function test_it_requires_overnight_period_to_fully_cover_daytime_query(): void
    {
        $restaurant = Restaurant::factory()
            ->has(
                Hour::factory()
                    ->dayOfWeek(4)
                    ->has(Period::factory()->state(['start' => '22:00:00', 'end' => '02:00:00']))
            )
            ->create();

        $results = Restaurant::openInWindow('21:00:00', '23:00:00')->pluck('id');

        $this->assertEmpty($results);
    }

    public function test_it_requires_overnight_period_to_fully_cover_overnight_query(): void
    {
        $restaurant = Restaurant::factory()
            ->has(
                Hour::factory()
                    ->dayOfWeek(4)
                    ->has(Period::factory()->state(['start' => '22:00:00', 'end' => '02:00:00']))
            )
            ->create();

        $results = Restaurant::openInWindow('23:00:00', '05:00:00')->pluck('id');

        $this->assertEmpty($results);
    }

    public function test_it_matches_overnight_period_covering_early_morning_query(): void
    {
        $restaurant = Restaurant::factory()
            ->has(
                Hour::factory()
                    ->dayOfWeek(4)
                    ->has(Period::factory()->state(['start' => '22:00:00', 'end' => '02:00:00']))
            )
            ->create();

        $results = Restaurant::openInWindow('00:00:00', '02:00:00')->pluck('id');

        $this->assertCount(1, $results);
        $this->assertSame($restaurant->id, $results->first());
    }

    public function test_it_does_not_match_overnight_query_against_daytime_period(): void
    {
        $restaurant = Restaurant::factory()
            ->has(
                Hour::factory()
                    ->dayOfWeek(4)
                    ->has(Period::factory()->state(['start' => '09:00:00', 'end' => '18:00:00']))
            )
            ->create();

        $results = Restaurant::openInWindow('22:00:00', '02:00:00')->pluck('id');

        $this->assertEmpty($results);
    }

    public function test_it_excludes_overnight_period_closing_at_query_start_when_query_is_daytime(): void
    {
        $restaurant = Restaurant::factory()
            ->has(
                Hour::factory()
                    ->dayOfWeek(4)
                    ->has(Period::factory()->state(['start' => '18:00:00', 'end' => '03:00:00']))
            )
            ->create();

        $results = Restaurant::openInWindow('03:00:00', '04:00:00')->pluck('id');

        $this->assertEmpty($results);
    }
}
