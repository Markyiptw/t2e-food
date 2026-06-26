<?php

namespace App\Jobs;

use App\Services\Openrice\OpenriceRestaurantData;
use App\Services\Openrice\OpenriceSearchPageData;
use App\Services\Openrice\SyncOpenriceRestaurant;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ScrapeOpenriceRestaurants implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public function handle(SyncOpenriceRestaurant $syncRestaurant): void
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

        DB::transaction(function () use ($searchPage, $syncRestaurant): void {
            $searchPage->results->each(
                fn (OpenriceRestaurantData $restaurant) => $syncRestaurant->handle($restaurant)
            );
        });

        if ($searchPage->count > $queryParameters['startAt'] + $searchPage->results->count()) {
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
