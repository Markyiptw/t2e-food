<?php

namespace App\Jobs;

use App\Models\Hour;
use App\Models\Period;
use App\Models\Restaurant;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ScrapeOpenriceRestaurants implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        $queryParameters = Cache::get(
            'scrape_openrice_restaurants.query_parameters',
            [
                'districtId' => 0,
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
                ->dd()
                ->all(),
            [DB::raw("(data->>'poiId')")],
            ['data', 'updated_at'],
        );

        $restaurants
            ->each(function ($restaurant) {
                $restaurantId = Restaurant::query()
                    ->where('data->poiId', $restaurant['poiId'])
                    ->first('id')
                    ->id;

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
                            ->groupBy(fn ($value, $key) => Str::match('/^period(\d+)(Start|End)$/', $key))
                            ->map(fn (array $period, $key) => [
                                'position' => $key,
                                'start' => $period["period{$key}Start"],
                                'end' => $period["period{$key}End"],
                            ])
                            ->each(fn ($row) => Period::create([
                                ...$row,
                                'hour_id' => $hourId,
                            ]));
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
