<?php

namespace Tests\Feature\Restaurant;

use App\Models\Hour;
use App\Models\Period;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class OpenInWindowTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_daytime_period_daytime_query(): void
    {
        $valid = Restaurant::factory()
            ->has(
                Hour::factory()
                    ->has(Period::factory()->state(['start' => '09:00:00', 'end' => '17:00:00']))
            )
            ->create();

        $invalids = collect([
            ['start' => '10:00:00', 'end' => '14:00:00'],
            ['start' => '09:00:00', 'end' => '11:00:00'],
            ['start' => '08:30:00', 'end' => '11:30:00'],
        ])->map(fn ($period) => Restaurant::factory()
            ->has(Hour::factory()->has(Period::factory()->state($period)))
            ->create()
        );

        $this->assertEquals(
            Restaurant::openInWindow(Carbon::createFromFormat('!H:i', '10:00'), 360)->pluck('id')->all(),
            [$valid->id],
        );
    }

    public function test_exclusions(): void
    {
        $close = Restaurant::factory()->has(Hour::factory()->is24hr()->isClose())->create();
        $nonZeroWeight = Restaurant::factory()->has(Hour::factory()->nonZeroWeight()->is24hr())->create();
        $inactive = Restaurant::factory()->inactive()->has(Hour::factory()->is24hr())->create();
        $noHours = Restaurant::factory()->create();
        $noPeriodsNot24hr = Restaurant::factory()->has(Hour::factory()->dayOfWeek(3))->create();

        $results = Restaurant::openInWindow(Carbon::createFromFormat('!H:i', '10:00'), 240)->pluck('id');

        $this->assertEmpty($results);
    }

    public function test_default_duration_matches_closing_boundary(): void
    {
        $valid = Restaurant::factory()
            ->has(Hour::factory()->has(Period::factory()->state(['start' => '18:00:00', 'end' => '04:00:00'])))
            ->create();

        $results = Restaurant::openInWindow(Carbon::createFromFormat('!H:i', '04:00'))->pluck('id')->all();

        $this->assertEquals([$valid->id], $results);
    }

    public function test_overnight_period_overnight_query(): void
    {
        $valid = Restaurant::factory()
            ->has(
                Hour::factory()
                    ->has(Period::factory()->state(['start' => '22:00:00', 'end' => '02:00:00']))
            )
            ->create();

        $invalids = collect([
            ['start' => '23:30:00', 'end' => '02:00:00'], // period starts after query start
            ['start' => '22:00:00', 'end' => '00:30:00'], // period ends before query end
            ['start' => '23:30:00', 'end' => '00:30:00'], // both start after and end before
        ])->map(fn ($period) => Restaurant::factory()
            ->has(Hour::factory()->has(Period::factory()->state($period)))
            ->create()
        );

        $this->assertEquals(
            Restaurant::openInWindow(Carbon::createFromFormat('!H:i', '23:00'), 120)->pluck('id')->all(),
            [$valid->id],
        );
    }

    public function test_overnight_period_daytime_query(): void
    {
        $valid = Restaurant::factory()
            ->has(
                Hour::factory()
                    ->has(Period::factory()->state(['start' => '20:00:00', 'end' => '04:00:00']))
            )
            ->create();

        $invalids = collect([
            ['start' => '21:30:00', 'end' => '04:00:00'], // period starts after query start
            ['start' => '20:00:00', 'end' => '22:00:00'], // period ends before query end
            ['start' => '21:30:00', 'end' => '22:00:00'], // both start after and end before
        ])->map(fn ($period) => Restaurant::factory()
            ->has(Hour::factory()->has(Period::factory()->state($period)))
            ->create()
        );

        $this->assertEquals(
            Restaurant::openInWindow(Carbon::createFromFormat('!H:i', '21:00'), 120)->pluck('id')->all(),
            [$valid->id],
        );
    }

    public function test_daytime_period_overnight_query(): void
    {
        Restaurant::factory()
            ->has(Hour::factory()->has(Period::factory()->state(['start' => '00:00:00', 'end' => '23:00:00'])))
            ->create();

        // there should be no overlap

        $results = Restaurant::openInWindow(Carbon::createFromFormat('!H:i', '23:00'), 60)->pluck('id');

        $this->assertEmpty($results);
    }

    public function test_not_sure_what_case_this_is_but_it_is_failing(): void
    {
        Restaurant::factory()
            ->has(Hour::factory()->has(Period::factory()->state(['start' => '18:00:00', 'end' => '04:00:00'])))
            ->create();

        $results = Restaurant::openInWindow(Carbon::createFromFormat('!H:i', '04:00'), 60)->pluck('id');

        $this->assertEmpty($results);
    }
}
