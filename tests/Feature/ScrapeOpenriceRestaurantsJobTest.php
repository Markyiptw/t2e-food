<?php

namespace Tests\Feature;

use App\Jobs\ScrapeOpenriceRestaurants;
use App\Models\Hour;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ScrapeOpenriceRestaurantsJobTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_it_persists_restaurants_with_hours_and_periods(): void
    {
        $this->fakeApi([
            'www.openrice.com/api/v2/search*' => Http::response([
                'paginationResult' => [
                    'results' => [
                        [
                            'poiId' => 101,
                            'name' => 'Test Restaurant',
                            'address' => '1 Test Street',
                            'poiHours' => [
                                [
                                    'dayOfWeek' => 1,
                                    'period1Start' => '09:00:00',
                                    'period1End' => '22:00:00',
                                ],
                                [
                                    'dayOfWeek' => 2,
                                    'isClose' => true,
                                ],
                            ],
                        ],
                        [
                            'poiId' => 202,
                            'name' => 'Another Place',
                            'address' => '2 Another Road',
                            'poiHours' => [],
                        ],
                        [
                            'poiId' => 303,
                            'name' => 'Multi Period',
                            'poiHours' => [
                                [
                                    'dayOfWeek' => 3,
                                    'period1Start' => '08:00:00',
                                    'period1End' => '12:00:00',
                                    'period2Start' => '14:00:00',
                                    'period2End' => '22:00:00',
                                ],
                            ],
                        ],
                    ],
                    'count' => 3,
                ],
            ]),
        ]);

        (new ScrapeOpenriceRestaurants)->handle();

        $this->assertDatabaseCount('restaurants', 3);

        $restaurant1 = Restaurant::withoutGlobalScope('active')
            ->where('data->poiId', '101')
            ->first();
        $this->assertNotNull($restaurant1);
        $this->assertSame('Test Restaurant', $restaurant1->data['name']);
        $this->assertSame('1 Test Street', $restaurant1->data['address']);

        $restaurant2 = Restaurant::withoutGlobalScope('active')
            ->where('data->poiId', '202')
            ->first();
        $this->assertNotNull($restaurant2);
        $this->assertSame('Another Place', $restaurant2->data['name']);

        $this->assertDatabaseCount('hours', 3);

        $hour1 = Hour::where('restaurant_id', $restaurant1->id)
            ->whereJsonContains('data->dayOfWeek', 1)
            ->first();
        $this->assertNotNull($hour1);
        $this->assertDatabaseHas('periods', [
            'hour_id' => $hour1->id,
            'position' => 1,
            'start' => '09:00:00',
            'end' => '22:00:00',
        ]);

        $hour2 = Hour::where('restaurant_id', $restaurant1->id)
            ->whereJsonContains('data->isClose', true)
            ->first();
        $this->assertNotNull($hour2);
        $this->assertTrue($hour2->data['isClose']);

        $restaurant3 = Restaurant::withoutGlobalScope('active')
            ->where('data->poiId', '303')
            ->first();
        $this->assertNotNull($restaurant3);
        $this->assertDatabaseCount('periods', 3);

        $hour3 = Hour::where('restaurant_id', $restaurant3->id)->first();
        $this->assertNotNull($hour3);
        $this->assertDatabaseHas('periods', [
            'hour_id' => $hour3->id,
            'position' => 1,
            'start' => '08:00:00',
            'end' => '12:00:00',
        ]);
        $this->assertDatabaseHas('periods', [
            'hour_id' => $hour3->id,
            'position' => 2,
            'start' => '14:00:00',
            'end' => '22:00:00',
        ]);
    }

    public function test_it_upserts_existing_restaurants_instead_of_duplicating(): void
    {
        $this->fakeApi([
            'www.openrice.com/api/v2/search*' => Http::response([
                'paginationResult' => [
                    'results' => [
                        [
                            'poiId' => 101,
                            'name' => 'Updated Name',
                            'address' => 'New Address',
                            'poiHours' => [],
                        ],
                    ],
                    'count' => 1,
                ],
            ]),
        ]);

        DB::table('restaurants')->insert([
            'data' => json_encode([
                'poiId' => 101,
                'name' => 'Old Name',
                'address' => 'Old Address',
            ], JSON_THROW_ON_ERROR),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        (new ScrapeOpenriceRestaurants)->handle();

        $this->assertDatabaseCount('restaurants', 1);

        $restaurant = Restaurant::withoutGlobalScope('active')
            ->where('data->poiId', '101')
            ->first();
        $this->assertSame('Updated Name', $restaurant->data['name']);
        $this->assertSame('New Address', $restaurant->data['address']);
    }

    public function test_it_replaces_existing_hours_and_periods_on_re_scrape(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'www.openrice.com/api/v2/metadata/region/all' => Http::response([
                'districts' => [
                    ['districtId' => 1001, 'districtGroupId' => 1],
                ],
            ]),
            'www.openrice.com/api/v2/search*' => Http::sequence()
                ->push([
                    'paginationResult' => [
                        'results' => [
                            [
                                'poiId' => 200,
                                'name' => 'Re-scrape Test',
                                'poiHours' => [
                                    [
                                        'dayOfWeek' => 1,
                                        'period1Start' => '08:00:00',
                                        'period1End' => '20:00:00',
                                    ],
                                ],
                            ],
                        ],
                        'count' => 1,
                    ],
                ])
                ->push([
                    'paginationResult' => [
                        'results' => [
                            [
                                'poiId' => 200,
                                'name' => 'Re-scrape Test',
                                'poiHours' => [
                                    [
                                        'dayOfWeek' => 3,
                                        'period1Start' => '10:00:00',
                                        'period1End' => '18:00:00',
                                    ],
                                ],
                            ],
                        ],
                        'count' => 1,
                    ],
                ]),
        ]);

        (new ScrapeOpenriceRestaurants)->handle();

        $this->assertDatabaseCount('hours', 1);
        $this->assertDatabaseCount('periods', 1);

        (new ScrapeOpenriceRestaurants)->handle();

        $this->assertDatabaseCount('hours', 1);
        $this->assertDatabaseCount('periods', 1);

        $hour = Hour::first();
        $this->assertSame(3, $hour->data['dayOfWeek']);

        $this->assertDatabaseHas('periods', [
            'hour_id' => $hour->id,
            'position' => 1,
            'start' => '10:00:00',
            'end' => '18:00:00',
        ]);
    }

    public function test_it_propagates_api_failures(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'www.openrice.com/api/v2/metadata/region/all' => Http::response([
                'districts' => [
                    ['districtId' => 1001, 'districtGroupId' => 1],
                ],
            ]),
            'www.openrice.com/api/v2/search*' => Http::response('', 500),
        ]);

        $this->expectException(RequestException::class);

        (new ScrapeOpenriceRestaurants)->handle();
    }

    /**
     * @param  array<string, Response|Factory>  $searchFake
     */
    private function fakeApi(array $searchFake = []): void
    {
        Http::preventStrayRequests();
        Http::fake(array_merge([
            'www.openrice.com/api/v2/metadata/region/all' => Http::response([
                'districts' => [
                    ['districtId' => 1001, 'districtGroupId' => 1],
                ],
            ]),
        ], $searchFake));
    }
}
