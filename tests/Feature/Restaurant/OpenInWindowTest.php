<?php

namespace Tests\Feature\Restaurant;

use App\Models\Restaurant;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class OpenInWindowTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_it_matches_restaurant_open_24_hours_on_specified_weekday(): void
    {
        $restaurantId = $this->insertActiveRestaurant();

        $this->insertHour($restaurantId, [
            'dayOfWeek' => 3,
            'weight' => 0,
            'isClose' => false,
            'is24hr' => true,
        ]);

        $results = Restaurant::openInWindow(3, '10:00:00', '14:00:00')->pluck('id');

        $this->assertCount(1, $results);
        $this->assertSame($restaurantId, $results->first());
    }

    public function test_it_matches_restaurant_with_periods_covering_window_on_specified_weekday(): void
    {
        $restaurantId = $this->insertActiveRestaurant();

        $hourId = $this->insertHour($restaurantId, [
            'dayOfWeek' => 2,
            'weight' => 0,
            'isClose' => false,
            'is24hr' => false,
        ]);

        $this->insertPeriod($hourId, 1, '09:00:00', '17:00:00');

        $results = Restaurant::openInWindow(2, '10:00:00', '14:00:00')->pluck('id');

        $this->assertCount(1, $results);
        $this->assertSame($restaurantId, $results->first());
    }

    public function test_it_requires_period_to_fully_cover_the_window(): void
    {
        $restaurantId = $this->insertActiveRestaurant();

        $hourId = $this->insertHour($restaurantId, [
            'dayOfWeek' => 2,
            'weight' => 0,
            'isClose' => false,
            'is24hr' => false,
        ]);

        $this->insertPeriod($hourId, 1, '10:00:00', '14:00:00');

        $results = Restaurant::openInWindow(2, '08:00:00', '11:00:00')->pluck('id');

        $this->assertEmpty($results);
    }

    public function test_it_excludes_restaurant_with_mismatched_weekday(): void
    {
        $restaurantId = $this->insertActiveRestaurant();

        $this->insertHour($restaurantId, [
            'dayOfWeek' => 5,
            'weight' => 0,
            'isClose' => false,
            'is24hr' => true,
        ]);

        $results = Restaurant::openInWindow(3, '10:00:00', '14:00:00')->pluck('id');

        $this->assertEmpty($results);
    }

    public function test_it_excludes_restaurant_with_is_close_true(): void
    {
        $restaurantId = $this->insertActiveRestaurant();

        $this->insertHour($restaurantId, [
            'dayOfWeek' => 3,
            'weight' => 0,
            'isClose' => true,
            'is24hr' => true,
        ]);

        $results = Restaurant::openInWindow(3, '10:00:00', '14:00:00')->pluck('id');

        $this->assertEmpty($results);
    }

    public function test_it_excludes_restaurant_with_non_zero_weight(): void
    {
        $restaurantId = $this->insertActiveRestaurant();

        $this->insertHour($restaurantId, [
            'dayOfWeek' => 3,
            'weight' => 4,
            'isClose' => false,
            'is24hr' => true,
        ]);

        $results = Restaurant::openInWindow(3, '10:00:00', '14:00:00')->pluck('id');

        $this->assertEmpty($results);
    }

    public function test_it_excludes_restaurant_without_any_hours(): void
    {
        $this->insertActiveRestaurant();

        $results = Restaurant::openInWindow(3, '10:00:00', '14:00:00')->pluck('id');

        $this->assertEmpty($results);
    }

    public function test_it_excludes_restaurant_with_hour_but_no_periods_and_not_24hr(): void
    {
        $restaurantId = $this->insertActiveRestaurant();

        $this->insertHour($restaurantId, [
            'dayOfWeek' => 3,
            'weight' => 0,
            'isClose' => false,
            'is24hr' => false,
        ]);

        $results = Restaurant::openInWindow(3, '10:00:00', '14:00:00')->pluck('id');

        $this->assertEmpty($results);
    }

    public function test_it_matches_any_weekday_when_day_of_week_is_null(): void
    {
        $mondayId = $this->insertActiveRestaurant();
        $this->insertHour($mondayId, [
            'dayOfWeek' => 1,
            'weight' => 0,
            'isClose' => false,
            'is24hr' => true,
        ]);

        $fridayId = $this->insertActiveRestaurant();
        $this->insertHour($fridayId, [
            'dayOfWeek' => 5,
            'weight' => 0,
            'isClose' => false,
            'is24hr' => true,
        ]);

        $results = Restaurant::openInWindow(null, '10:00:00', '14:00:00')->pluck('id');

        $this->assertCount(2, $results);
        $this->assertContains($mondayId, $results->all());
        $this->assertContains($fridayId, $results->all());
    }

    public function test_it_still_excludes_is_close_when_day_of_week_is_null(): void
    {
        $openId = $this->insertActiveRestaurant();
        $this->insertHour($openId, [
            'dayOfWeek' => 1,
            'weight' => 0,
            'isClose' => false,
            'is24hr' => true,
        ]);

        $closedId = $this->insertActiveRestaurant();
        $this->insertHour($closedId, [
            'dayOfWeek' => 1,
            'weight' => 0,
            'isClose' => true,
            'is24hr' => true,
        ]);

        $results = Restaurant::openInWindow(null, '10:00:00', '14:00:00')->pluck('id');

        $this->assertCount(1, $results);
        $this->assertSame($openId, $results->first());
    }

    public function test_it_still_excludes_non_zero_weight_when_day_of_week_is_null(): void
    {
        $baseId = $this->insertActiveRestaurant();
        $this->insertHour($baseId, [
            'dayOfWeek' => 1,
            'weight' => 0,
            'isClose' => false,
            'is24hr' => true,
        ]);

        $holidayId = $this->insertActiveRestaurant();
        $this->insertHour($holidayId, [
            'dayOfWeek' => 0,
            'weight' => 4,
            'isClose' => false,
            'is24hr' => true,
        ]);

        $results = Restaurant::openInWindow(null, '10:00:00', '14:00:00')->pluck('id');

        $this->assertCount(1, $results);
        $this->assertSame($baseId, $results->first());
    }

    public function test_it_excludes_inactive_restaurants(): void
    {
        $restaurantId = DB::table('restaurants')->insertGetId([
            'data' => json_encode(['status' => 5, 'mapLatitude' => 22.3, 'mapLongitude' => 114.2], JSON_THROW_ON_ERROR),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $this->insertHour($restaurantId, [
            'dayOfWeek' => 3,
            'weight' => 0,
            'isClose' => false,
            'is24hr' => true,
        ]);

        $results = Restaurant::withoutGlobalScope('active')
            ->openInWindow(3, '10:00:00', '14:00:00')
            ->pluck('id');

        $this->assertCount(1, $results);
        $this->assertSame($restaurantId, $results->first());

        $resultsWithScope = Restaurant::openInWindow(3, '10:00:00', '14:00:00')->pluck('id');

        $this->assertEmpty($resultsWithScope);
    }

    public function test_it_matches_overnight_period_covering_overnight_query(): void
    {
        $restaurantId = $this->insertActiveRestaurant();

        $hourId = $this->insertHour($restaurantId, [
            'dayOfWeek' => 4,
            'weight' => 0,
            'isClose' => false,
            'is24hr' => false,
        ]);

        $this->insertPeriod($hourId, 1, '22:00:00', '02:00:00');

        $results = Restaurant::openInWindow(4, '23:00:00', '01:00:00')->pluck('id');

        $this->assertCount(1, $results);
        $this->assertSame($restaurantId, $results->first());
    }

    public function test_it_requires_overnight_period_to_fully_cover_daytime_query(): void
    {
        $restaurantId = $this->insertActiveRestaurant();

        $hourId = $this->insertHour($restaurantId, [
            'dayOfWeek' => 4,
            'weight' => 0,
            'isClose' => false,
            'is24hr' => false,
        ]);

        $this->insertPeriod($hourId, 1, '22:00:00', '02:00:00');

        $results = Restaurant::openInWindow(4, '21:00:00', '23:00:00')->pluck('id');

        $this->assertEmpty($results);
    }

    public function test_it_requires_overnight_period_to_fully_cover_overnight_query(): void
    {
        $restaurantId = $this->insertActiveRestaurant();

        $hourId = $this->insertHour($restaurantId, [
            'dayOfWeek' => 4,
            'weight' => 0,
            'isClose' => false,
            'is24hr' => false,
        ]);

        $this->insertPeriod($hourId, 1, '22:00:00', '02:00:00');

        $results = Restaurant::openInWindow(4, '23:00:00', '05:00:00')->pluck('id');

        $this->assertEmpty($results);
    }

    public function test_it_matches_overnight_period_covering_early_morning_query(): void
    {
        $restaurantId = $this->insertActiveRestaurant();

        $hourId = $this->insertHour($restaurantId, [
            'dayOfWeek' => 4,
            'weight' => 0,
            'isClose' => false,
            'is24hr' => false,
        ]);

        $this->insertPeriod($hourId, 1, '22:00:00', '02:00:00');

        $results = Restaurant::openInWindow(4, '00:00:00', '02:00:00')->pluck('id');

        $this->assertCount(1, $results);
        $this->assertSame($restaurantId, $results->first());
    }

    public function test_it_does_not_match_overnight_query_against_daytime_period(): void
    {
        $restaurantId = $this->insertActiveRestaurant();

        $hourId = $this->insertHour($restaurantId, [
            'dayOfWeek' => 4,
            'weight' => 0,
            'isClose' => false,
            'is24hr' => false,
        ]);

        $this->insertPeriod($hourId, 1, '09:00:00', '18:00:00');

        $results = Restaurant::openInWindow(4, '22:00:00', '02:00:00')->pluck('id');

        $this->assertEmpty($results);
    }

    public function test_it_excludes_overnight_period_closing_at_query_start_when_query_is_daytime(): void
    {
        $restaurantId = $this->insertActiveRestaurant();

        $hourId = $this->insertHour($restaurantId, [
            'dayOfWeek' => 4,
            'weight' => 0,
            'isClose' => false,
            'is24hr' => false,
        ]);

        $this->insertPeriod($hourId, 1, '18:00:00', '03:00:00');

        $results = Restaurant::openInWindow(4, '03:00:00', '04:00:00')->pluck('id');

        $this->assertEmpty($results);
    }

    private function insertActiveRestaurant(): int
    {
        return DB::table('restaurants')->insertGetId([
            'data' => json_encode([
                'status' => 10,
                'mapLatitude' => 22.3,
                'mapLongitude' => 114.2,
            ], JSON_THROW_ON_ERROR),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function insertHour(int $restaurantId, array $data): int
    {
        return DB::table('hours')->insertGetId([
            'restaurant_id' => $restaurantId,
            'data' => json_encode($data, JSON_THROW_ON_ERROR),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }

    private function insertPeriod(int $hourId, int $position, string $start, string $end): int
    {
        return DB::table('periods')->insertGetId([
            'position' => $position,
            'start' => $start,
            'end' => $end,
            'hour_id' => $hourId,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
