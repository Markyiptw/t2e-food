<?php

namespace Tests\Feature\Openrice;

use App\Models\Hour;
use App\Models\Location;
use App\Models\Period;
use App\Models\Restaurant;
use App\Models\Status;
use App\Services\Openrice\OpenriceCategoryData;
use App\Services\Openrice\OpenriceDistrictData;
use App\Services\Openrice\OpenriceHourData;
use App\Services\Openrice\OpenriceLocationData;
use App\Services\Openrice\OpenricePeriodData;
use App\Services\Openrice\OpenriceRestaurantData;
use App\Services\Openrice\OpenriceStatusData;
use App\Services\Openrice\SyncOpenriceRestaurant;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class SyncOpenriceRestaurantTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_it_persists_a_new_restaurant_with_all_relations(): void
    {
        $data = $this->fullRestaurantData();

        (new SyncOpenriceRestaurant)->handle($data);

        $restaurant = Restaurant::withoutGlobalScope('active')->where('poi_id', 101)->first();

        $this->assertNotNull($restaurant);
        $this->assertSame('Test Restaurant', $restaurant->name);
        $this->assertSame('https://example.test/restaurant', $restaurant->url);
        $this->assertSame('1 Test Street', $restaurant->address);

        $this->assertDatabaseHas('statuses', [
            'code' => 10,
            'text' => 'Closed for renovation',
        ]);

        $this->assertDatabaseHas('districts', [
            'external_id' => 1001,
            'name' => 'Central',
        ]);

        $this->assertDatabaseHas('locations', [
            'restaurant_id' => $restaurant->id,
            'latitude' => 22.2819,
            'longitude' => 114.1589,
        ]);

        $this->assertCount(2, $restaurant->categories);
        $this->assertTrue($restaurant->categories->contains('name', 'Japanese'));
        $this->assertTrue($restaurant->categories->contains('name', 'Sushi'));

        $this->assertDatabaseCount('hours', 1);
        $hour = Hour::where('restaurant_id', $restaurant->id)->first();
        $this->assertDatabaseHas('hours', [
            'restaurant_id' => $restaurant->id,
            'day_of_week' => 1,
            'is_close' => false,
            'is_24hr' => false,
        ]);

        $this->assertDatabaseCount('periods', 2);
        $this->assertDatabaseHas('periods', [
            'hour_id' => $hour->id,
            'position' => 1,
            'start' => '09:00:00',
            'end' => '12:00:00',
        ]);
        $this->assertDatabaseHas('periods', [
            'hour_id' => $hour->id,
            'position' => 2,
            'start' => '14:00:00',
            'end' => '22:00:00',
        ]);
    }

    public function test_it_updates_an_existing_restaurant(): void
    {
        $existing = $this->createRestaurantWithStatus(poiId: 101, name: 'Old Name');

        $data = new OpenriceRestaurantData(
            poiId: 101,
            name: 'New Name',
            url: 'https://example.test/new',
            status: new OpenriceStatusData(10, null),
            address: 'New Address',
            district: null,
            location: null,
            categories: collect(),
            hours: collect(),
        );

        (new SyncOpenriceRestaurant)->handle($data);

        $existing->refresh();
        $this->assertSame('New Name', $existing->name);
        $this->assertSame('New Address', $existing->address);
        $this->assertDatabaseCount('restaurants', 1);
    }

    public function test_it_creates_district_when_present(): void
    {
        $data = new OpenriceRestaurantData(
            poiId: 101,
            name: 'Test Restaurant',
            url: null,
            status: new OpenriceStatusData(10, null),
            address: null,
            district: new OpenriceDistrictData(externalId: 500, name: 'Mong Kok'),
            location: null,
            categories: collect(),
            hours: collect(),
        );

        (new SyncOpenriceRestaurant)->handle($data);

        $this->assertDatabaseHas('districts', ['external_id' => 500, 'name' => 'Mong Kok']);
    }

    public function test_it_skips_district_when_absent(): void
    {
        $data = $this->minimalRestaurantData();

        (new SyncOpenriceRestaurant)->handle($data);

        $this->assertDatabaseCount('districts', 0);
    }

    public function test_it_creates_location_when_coordinates_present(): void
    {
        $data = $this->minimalRestaurantData(location: new OpenriceLocationData(22.3193, 114.1694));

        (new SyncOpenriceRestaurant)->handle($data);

        $restaurant = Restaurant::withoutGlobalScope('active')->where('poi_id', 101)->first();

        $this->assertDatabaseHas('locations', [
            'restaurant_id' => $restaurant->id,
            'latitude' => 22.3193,
            'longitude' => 114.1694,
        ]);
    }

    public function test_it_deletes_location_when_coordinates_absent(): void
    {
        $existing = $this->createRestaurantWithStatus(poiId: 101);
        Location::create([
            'restaurant_id' => $existing->id,
            'latitude' => 22.0,
            'longitude' => 114.0,
        ]);

        $data = $this->minimalRestaurantData(location: null);

        (new SyncOpenriceRestaurant)->handle($data);

        $this->assertDatabaseCount('locations', 0);
    }

    public function test_it_syncs_categories(): void
    {
        $data = new OpenriceRestaurantData(
            poiId: 101,
            name: 'Test Restaurant',
            url: null,
            status: new OpenriceStatusData(10, null),
            address: null,
            district: null,
            location: null,
            categories: collect([
                new OpenriceCategoryData('Italian'),
                new OpenriceCategoryData('Pizza'),
            ]),
            hours: collect(),
        );

        (new SyncOpenriceRestaurant)->handle($data);

        $restaurant = Restaurant::withoutGlobalScope('active')->where('poi_id', 101)->first();

        $this->assertCount(2, $restaurant->categories);
        $this->assertTrue($restaurant->categories->contains('name', 'Italian'));
        $this->assertTrue($restaurant->categories->contains('name', 'Pizza'));
    }

    public function test_it_replaces_hours_and_periods_on_re_scrape(): void
    {
        $existing = $this->createRestaurantWithStatus(poiId: 101);
        $oldHour = Hour::create([
            'restaurant_id' => $existing->id,
            'day_of_week' => 3,
            'is_close' => false,
            'is_24hr' => false,
        ]);
        Period::create([
            'hour_id' => $oldHour->id,
            'position' => 1,
            'start' => '08:00:00',
            'end' => '20:00:00',
        ]);

        $data = new OpenriceRestaurantData(
            poiId: 101,
            name: 'Test Restaurant',
            url: null,
            status: new OpenriceStatusData(10, null),
            address: null,
            district: null,
            location: null,
            categories: collect(),
            hours: collect([
                new OpenriceHourData(
                    dayOfWeek: 5,
                    isClose: false,
                    is24Hr: false,
                    periods: collect([
                        new OpenricePeriodData(1, '10:00:00', '18:00:00'),
                    ]),
                ),
            ]),
        );

        (new SyncOpenriceRestaurant)->handle($data);

        $this->assertDatabaseCount('hours', 1);
        $newHour = Hour::where('restaurant_id', $existing->id)->first();
        $this->assertDatabaseHas('hours', [
            'restaurant_id' => $existing->id,
            'day_of_week' => 5,
        ]);

        $this->assertDatabaseCount('periods', 1);
        $this->assertDatabaseHas('periods', [
            'hour_id' => $newHour->id,
            'position' => 1,
            'start' => '10:00:00',
            'end' => '18:00:00',
        ]);
    }

    public function test_it_persists_closed_day_hour(): void
    {
        $data = new OpenriceRestaurantData(
            poiId: 101,
            name: 'Test Restaurant',
            url: null,
            status: new OpenriceStatusData(10, null),
            address: null,
            district: null,
            location: null,
            categories: collect(),
            hours: collect([
                new OpenriceHourData(
                    dayOfWeek: 2,
                    isClose: true,
                    is24Hr: false,
                    periods: collect(),
                ),
            ]),
        );

        (new SyncOpenriceRestaurant)->handle($data);

        $hour = Hour::first();
        $this->assertTrue($hour->is_close);
        $this->assertDatabaseHas('hours', [
            'day_of_week' => 2,
            'is_close' => true,
        ]);
        $this->assertDatabaseCount('periods', 0);
    }

    private function fullRestaurantData(): OpenriceRestaurantData
    {
        return new OpenriceRestaurantData(
            poiId: 101,
            name: 'Test Restaurant',
            url: 'https://example.test/restaurant',
            status: new OpenriceStatusData(10, 'Closed for renovation'),
            address: '1 Test Street',
            district: new OpenriceDistrictData(externalId: 1001, name: 'Central'),
            location: new OpenriceLocationData(22.2819, 114.1589),
            categories: collect([
                new OpenriceCategoryData('Japanese'),
                new OpenriceCategoryData('Sushi'),
            ]),
            hours: collect([
                new OpenriceHourData(
                    dayOfWeek: 1,
                    isClose: false,
                    is24Hr: false,
                    periods: collect([
                        new OpenricePeriodData(1, '09:00:00', '12:00:00'),
                        new OpenricePeriodData(2, '14:00:00', '22:00:00'),
                    ]),
                ),
            ]),
        );
    }

    private function minimalRestaurantData(?OpenriceLocationData $location = null): OpenriceRestaurantData
    {
        return new OpenriceRestaurantData(
            poiId: 101,
            name: 'Test Restaurant',
            url: null,
            status: new OpenriceStatusData(10, null),
            address: null,
            district: null,
            location: $location,
            categories: collect(),
            hours: collect(),
        );
    }

    private function createRestaurantWithStatus(int $poiId, string $name = 'Old Name'): Restaurant
    {
        $status = Status::firstOrCreate(['code' => 10, 'text' => null]);

        return Restaurant::withoutGlobalScope('active')->create([
            'poi_id' => $poiId,
            'name' => $name,
            'status_id' => $status->id,
        ]);
    }
}
