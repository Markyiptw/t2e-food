<?php

namespace Tests\Feature;

use App\Jobs\ScrapeOpenriceRestaurants;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ScrapeOpenriceRestaurantsJobTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_it_scrapes_paginated_district_results_and_persists_restaurants(): void
    {
        Http::preventStrayRequests();

        Http::fake(fn (Request $request) => $this->fakeResponseForRequest($request));

        (new ScrapeOpenriceRestaurants)->handle();

        $this->assertDatabaseCount('restaurants', 103);
        $this->assertDatabaseHas('restaurants', ['openrice_poi_id' => '100301']);
        $this->assertDatabaseHas('restaurants', ['openrice_poi_id' => '200801']);
        $this->assertSame('Central 1', json_decode((string) DB::table('restaurants')->where('openrice_poi_id', '100301')->value('data'), true, flags: JSON_THROW_ON_ERROR)['name']);
        $this->assertSame($this->pageQueryParameters(1003, 0), $this->restaurantQueryParameters('100301'));
        $this->assertSame($this->pageQueryParameters(2008, 100), $this->restaurantQueryParameters('200901'));

        Http::assertSentCount(4);
        Http::assertNotSent(function (Request $request): bool {
            $query = $this->queryParameters($request);

            return in_array((int) ($query['districtId'] ?? 0), [1999, -9006], true);
        });
    }

    public function test_it_resumes_from_the_next_page_after_the_latest_saved_restaurant(): void
    {
        DB::table('restaurants')->insert([
            'openrice_poi_id' => '100301',
            'data' => json_encode(['poiId' => 100301, 'name' => 'Saved Page'], JSON_THROW_ON_ERROR),
            'query_params' => json_encode($this->pageQueryParameters(1003, 0), JSON_THROW_ON_ERROR),
            'created_at' => now()->subDay(),
            'updated_at' => now(),
        ]);

        Http::preventStrayRequests();
        Http::fake(fn (Request $request) => $this->fakeResponseForRequest($request, [
            1003 => [
                'count' => 101,
                'pages' => [
                    100 => [
                        $this->restaurant(100399, 'Central Resume', 1003),
                    ],
                ],
            ],
            2008 => [
                'count' => 1,
                'pages' => [
                    0 => [
                        $this->restaurant(200801, 'Tsim Sha Tsui 200801', 2008),
                    ],
                ],
            ],
        ]));

        (new ScrapeOpenriceRestaurants)->handle();

        $this->assertDatabaseCount('restaurants', 3);
        $this->assertSame('Saved Page', json_decode((string) DB::table('restaurants')->where('openrice_poi_id', '100301')->value('data'), true, flags: JSON_THROW_ON_ERROR)['name']);
        $this->assertSame($this->pageQueryParameters(1003, 100), $this->restaurantQueryParameters('100399'));
        $this->assertSame($this->pageQueryParameters(2008, 0), $this->restaurantQueryParameters('200801'));

        Http::assertSentCount(3);
        Http::assertNotSent(function (Request $request): bool {
            $query = $this->queryParameters($request);

            return (int) ($query['districtId'] ?? 0) === 1003 && (int) ($query['startAt'] ?? -1) === 0;
        });
    }

    public function test_it_restarts_from_the_beginning_after_reaching_the_last_district_and_page(): void
    {
        DB::table('restaurants')->insert([
            [
                'openrice_poi_id' => '100301',
                'data' => json_encode(['poiId' => 100301, 'name' => 'Old Name'], JSON_THROW_ON_ERROR),
                'query_params' => json_encode($this->pageQueryParameters(1003, 0), JSON_THROW_ON_ERROR),
                'created_at' => now()->subDay(),
                'updated_at' => now()->subDay(),
            ],
            [
                'openrice_poi_id' => '200801',
                'data' => json_encode(['poiId' => 200801, 'name' => 'Last Page'], JSON_THROW_ON_ERROR),
                'query_params' => json_encode($this->pageQueryParameters(2008, 0), JSON_THROW_ON_ERROR),
                'created_at' => now()->subMinute(),
                'updated_at' => now()->subMinute(),
            ],
        ]);

        Http::preventStrayRequests();
        Http::fake(fn (Request $request) => $this->fakeResponseForRequest($request, [
            1003 => [
                'count' => 1,
                'pages' => [
                    0 => [
                        $this->restaurant(100301, 'Updated Name', 1003),
                    ],
                ],
            ],
            2008 => [
                'count' => 1,
                'pages' => [
                    0 => [
                        $this->restaurant(200801, 'Updated Last Page', 2008),
                    ],
                ],
            ],
        ]));

        (new ScrapeOpenriceRestaurants)->handle();

        $this->assertDatabaseCount('restaurants', 2);
        $this->assertSame('Updated Name', json_decode((string) DB::table('restaurants')->where('openrice_poi_id', '100301')->value('data'), true, flags: JSON_THROW_ON_ERROR)['name']);
        $this->assertSame('Updated Last Page', json_decode((string) DB::table('restaurants')->where('openrice_poi_id', '200801')->value('data'), true, flags: JSON_THROW_ON_ERROR)['name']);

        Http::assertSent(function (Request $request): bool {
            $query = $this->queryParameters($request);

            return (int) ($query['districtId'] ?? 0) === 1003 && (int) ($query['startAt'] ?? -1) === 0;
        });
    }

    public function test_it_propagates_api_failures(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://www.openrice.com/api/v2/search*' => Http::response(['message' => 'fail'], 500),
        ]);

        $this->expectException(RequestException::class);

        (new ScrapeOpenriceRestaurants)->handle();
    }

    /**
     * @param  array<int, array{count: int, pages: array<int, array<int, array<string, mixed>>>}>|null  $districtPages
     */
    private function fakeResponseForRequest(Request $request, ?array $districtPages = null): mixed
    {
        $query = $this->queryParameters($request);
        $districtPages ??= [
            1003 => [
                'count' => 2,
                'pages' => [
                    0 => [
                        $this->restaurant(100301, 'Central 1', 1003),
                        $this->restaurant(100302, 'Central 2', 1003),
                    ],
                ],
            ],
            2008 => [
                'count' => 101,
                'pages' => [
                    0 => array_map(
                        fn (int $poiId): array => $this->restaurant($poiId, "Tsim Sha Tsui {$poiId}", 2008),
                        range(200801, 200900),
                    ),
                    100 => [
                        $this->restaurant(200901, 'Tsim Sha Tsui 200901', 2008),
                    ],
                ],
            ],
        ];

        if (! isset($query['districtId'])) {
            return Http::response([
                'paginationResult' => [
                    'count' => 33022,
                    'results' => [],
                ],
                'refineSearchFilter' => [
                    'districts' => [
                        ['id' => 1999, 'name' => '香港島', 'count' => 8938],
                        ['id' => -9006, 'name' => '蘇豪', 'count' => 241],
                        ['id' => 1003, 'name' => '中環', 'count' => $districtPages[1003]['count']],
                        ['id' => 2008, 'name' => '尖沙咀', 'count' => $districtPages[2008]['count']],
                    ],
                ],
            ]);
        }

        $districtId = (int) $query['districtId'];
        $startAt = (int) ($query['startAt'] ?? 0);

        return Http::response([
            'paginationResult' => [
                'count' => $districtPages[$districtId]['count'],
                'results' => $districtPages[$districtId]['pages'][$startAt] ?? [],
            ],
        ]);
    }

    /**
     * @return array<string, string>
     */
    private function queryParameters(Request $request): array
    {
        parse_str((string) parse_url($request->url(), PHP_URL_QUERY), $query);

        return $query;
    }

    /**
     * @return array<string, mixed>
     */
    private function restaurantQueryParameters(string $poiId): array
    {
        return json_decode((string) DB::table('restaurants')->where('openrice_poi_id', $poiId)->value('query_params'), true, flags: JSON_THROW_ON_ERROR);
    }

    /**
     * @return array<string, int|string>
     */
    private function pageQueryParameters(int $districtId, int $startAt): array
    {
        return [
            'regionId' => 0,
            'districtId' => $districtId,
            'startAt' => $startAt,
            'rows' => 100,
            'pageToken' => 'CONST_DUMMY_TOKEN',
            'uiLang' => 'zh',
            'uiCity' => 'hongkong',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function restaurant(int $poiId, string $name, int $districtId): array
    {
        return [
            'poiId' => $poiId,
            'name' => $name,
            'district' => [
                'districtId' => $districtId,
                'name' => "District {$districtId}",
            ],
        ];
    }
}
