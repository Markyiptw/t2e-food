<?php

namespace App\Jobs;

use App\Models\Hour;
use App\Models\Period;
use App\Models\Restaurant;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ScrapeOpenriceRestaurants implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        $queryParameters = Cache::get(
            'scrape_openrice_restaurants.query_parameters',
            [
                'districtId' => Http::openrice()
                    ->get('/metadata/region/all')
                    ->collect('districts')
                    ->whereNotNull('districtGroupId')
                    ->pluck('districtId')
                    ->first(),
                'startAt' => 0,
                'rows' => 50, // so if the value changed here, it will be effective from the next run after the current cache get removed, which happens when the scraper finishes scraping all restaurants in the current district and moves on to the next one
            ]
        );

        Log::debug('Scraping OpenRice restaurants with query parameters: '.json_encode($queryParameters));

        $response = Http::openrice()
            ->get('/search', $queryParameters)
            ->throw()
            ->json();

        $restaurants = collect($response['paginationResult']['results']);

        Restaurant::upsert(
            $restaurants
                ->map(fn (array $restaurant) => [
                    'data' => collect($restaurant)
                        ->except(['poiHours'])
                        ->toJson(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
                ->all(),
            [DB::raw("(data->>'poiId')")],
            ['data', 'updated_at'],
        );

        $restaurants
            ->each(function ($restaurant) {
                $restaurantId = Restaurant::withoutGlobalScope('active')
                    ->withoutGlobalScope('hasLocation')
                    ->where('data->poiId', $restaurant['poiId'])
                    ->first('id')
                    ->id;

                Hour::query()
                    ->where('restaurant_id', $restaurantId)
                    ->delete(); // delete existing hours (and their related periods) before inserting new ones to handle the case when the hours data structure changes, e.g. the number of periods changes

                collect($restaurant['poiHours'])
                    ->each(function ($hour) use ($restaurantId) {
                        $hourId = Hour::create([
                            'restaurant_id' => $restaurantId,
                            'data' => collect($hour)
                                ->filter(fn ($value, $key) => ! Str::startsWith($key, 'period'))
                                ->toJson(),
                        ])->id;

                        collect($hour)
                            ->filter(fn ($value, $key) => Str::startsWith($key, 'period') && Str::endsWith($key, ['Start', 'End']))
                            ->groupBy(fn ($value, $key) => Str::match('/^period(\d+)(Start|End)$/', $key), true)
                            ->map(fn (Collection $period, $key) => [
                                'position' => $key,
                                'start' => $period["period{$key}Start"],
                                'end' => $period["period{$key}End"],
                                'hour_id' => $hourId,
                            ])
                            ->each(fn ($row) => Period::create($row));
                    });
            });

        if ($response['paginationResult']['count'] > $queryParameters['startAt'] + $restaurants->count()) {
            Cache::put(
                'scrape_openrice_restaurants.query_parameters',
                [
                    ...$queryParameters,
                    'startAt' => $queryParameters['startAt'] + $queryParameters['rows'],
                ],
            );
        } else {
            $districtIds = Http::openrice()
                ->get('/metadata/region/all')
                ->collect('districts')
                ->whereNotNull('districtGroupId')
                ->pluck('districtId')
                ->values();

            $hasNextDistrict = $districtIds->search($queryParameters['districtId'])
                |> (fn ($key) => $key !== false && $key < $districtIds->count() - 1);

            if ($hasNextDistrict) {
                Cache::put('scrape_openrice_restaurants.query_parameters', [
                    ...$queryParameters,
                    'districtId' => $districtIds->search($queryParameters['districtId'])
                        |> (fn ($key) => $districtIds[$key + 1]),
                    'startAt' => 0,
                ]);
            } else {
                Cache::forget('scrape_openrice_restaurants.query_parameters');
            }
        }
    }
}
