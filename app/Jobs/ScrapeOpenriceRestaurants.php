<?php

namespace App\Jobs;

use App\Models\Category;
use App\Models\District;
use App\Models\Hour;
use App\Models\Location;
use App\Models\Period;
use App\Models\Restaurant;
use App\Models\Status;
use App\Services\Openrice\OpenriceSearchPageData;
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
                'rows' => 50,
            ]
        );

        Log::debug('Scraping OpenRice restaurants with query parameters: '.json_encode($queryParameters));

        $response = Http::openrice()
            ->get('/search', $queryParameters)
            ->throw()
            ->json();

        $searchPage = OpenriceSearchPageData::fromArray($response);

        $results = $searchPage->results->map->toArray();

        DB::transaction(function () use ($results) {
            $results
                ->each(function (array $restaurant) {
                    $status = Status::firstOrCreate([
                        'code' => $restaurant['status'],
                        'text' => $restaurant['statusText'] ?? null,
                    ]);

                    $district = isset($restaurant['district'])
                        ? District::firstOrCreate(
                            ['external_id' => $restaurant['district']['districtId']],
                            ['name' => $restaurant['district']['name']],
                        )
                        : null;

                    $model = Restaurant::withoutGlobalScope('active')->updateOrCreate(
                        ['poi_id' => $restaurant['poiId']],
                        [
                            'name' => $restaurant['name'],
                            'url' => $restaurant['shortenUrl'] ?? null,
                            'status_id' => $status->id,
                            'status_text' => $restaurant['statusText'] ?? null,
                            'address' => $restaurant['address'] ?? null,
                            'district_id' => $district?->id,
                        ],
                    );

                    $latitude = $restaurant['mapLatitude'] ?? null;
                    $longitude = $restaurant['mapLongitude'] ?? null;

                    if ($latitude && $longitude && $latitude != 0 && $longitude != 0) {
                        Location::updateOrCreate(
                            ['restaurant_id' => $model->id],
                            ['latitude' => $latitude, 'longitude' => $longitude],
                        );
                    } else {
                        $model->location()->delete();
                    }

                    $categoryIds = collect($restaurant['categories'] ?? [])
                        ->map(fn (array $category) => Category::firstOrCreate(
                            ['name' => $category['name']],
                        )->id)
                        ->all();

                    $model->categories()->sync($categoryIds);

                    Hour::query()
                        ->where('restaurant_id', $model->id)
                        ->delete();

                    collect($restaurant['poiHours'] ?? [])
                        ->filter(fn ($hour) => ($hour['weight'] ?? 0) === 0)
                        ->each(function ($hour) use ($model) {
                            $hourId = Hour::create([
                                'restaurant_id' => $model->id,
                                'day_of_week' => $hour['dayOfWeek'] ?? 0,
                                'is_close' => $hour['isClose'] ?? false,
                                'is_24hr' => $hour['is24hr'] ?? false,
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
        });

        if ($searchPage->count > $queryParameters['startAt'] + $results->count()) {
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
