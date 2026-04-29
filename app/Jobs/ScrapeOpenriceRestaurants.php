<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class ScrapeOpenriceRestaurants implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 120;

    /**
     * @var array<int, int>
     */
    public array $backoff = [1, 5, 10];

    private const string ENDPOINT = 'https://www.openrice.com/api/v2/search';

    private const int REGION_ID = 0;

    private const int ROWS_PER_PAGE = 100;

    private const string PAGE_TOKEN = 'CONST_DUMMY_TOKEN';

    /**
     * @var array<int, int>
     */
    private const array REGION_BUCKET_IDS = [1999, 2999, 3999];

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->districtIds() as $districtId) {
            $this->scrapeDistrict($districtId);
        }
    }

    public function failed(?Throwable $exception): void
    {
        if ($exception === null) {
            return;
        }

        Log::error('Failed to scrape OpenRice restaurants.', [
            'exception' => $exception::class,
            'message' => $exception->getMessage(),
        ]);
    }

    /**
     * @return array<int, int>
     */
    private function districtIds(): array
    {
        $payload = $this->requestPage([
            'regionId' => self::REGION_ID,
            'startAt' => 0,
        ]);

        // https://www.openrice.com/api/v2/metadata/region/all?uiLang=zh&uiCity=hongkong
        // status = 10

        $districtIds = collect(data_get($payload, 'refineSearchFilter.districts', []))
            ->filter(fn (array $district): bool => $this->shouldScrapeDistrict($district))
            ->pluck('id')
            ->map(fn (mixed $districtId): int => (int) $districtId)
            ->unique()
            ->values()
            ->all();

        if ($districtIds === []) {
            throw new RuntimeException('Unable to determine OpenRice district filters.');
        }

        return $districtIds;
    }

    private function shouldScrapeDistrict(array $district): bool
    {
        $districtId = (int) ($district['id'] ?? 0);

        return $districtId > 0 && ! in_array($districtId, self::REGION_BUCKET_IDS, true);
    }

    private function scrapeDistrict(int $districtId): void
    {
        $startAt = 0;
        $totalCount = null;

        do {
            $payload = $this->requestPage([
                'regionId' => self::REGION_ID,
                'districtId' => $districtId,
                'startAt' => $startAt,
            ]);

            $restaurants = $this->restaurantsFromPayload($payload, $districtId, $startAt);

            if ($restaurants->isEmpty()) {
                return;
            }

            $totalCount = $this->countFromPayload($payload, $districtId);
            $this->persistRestaurants($restaurants);

            $startAt += $restaurants->count();
        } while ($startAt < $totalCount);
    }

    /**
     * @param  array<string, int|string>  $parameters
     * @return array<string, mixed>
     */
    private function requestPage(array $parameters): array
    {
        $payload = Http::acceptJson()
            ->connectTimeout(5)
            ->timeout(15)
            ->retry([100, 500, 1000])
            ->get(self::ENDPOINT, [
                ...$parameters,
                'rows' => self::ROWS_PER_PAGE,
                'pageToken' => self::PAGE_TOKEN,
                'uiLang' => 'zh',
                'uiCity' => 'hongkong',
            ])
            ->throw()
            ->json();

        if (! is_array($payload)) {
            throw new RuntimeException('OpenRice returned an invalid response payload.');
        }

        return $payload;
    }

    private function countFromPayload(array $payload, int $districtId): int
    {
        $count = data_get($payload, 'paginationResult.count');

        if (! is_numeric($count)) {
            throw new RuntimeException("OpenRice did not return a total count for district [{$districtId}].");
        }

        return (int) $count;
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function restaurantsFromPayload(array $payload, int $districtId, int $startAt): Collection
    {
        $restaurants = collect(data_get($payload, 'paginationResult.results', []));

        if (! $restaurants->every(fn (mixed $restaurant): bool => is_array($restaurant))) {
            throw new RuntimeException("OpenRice returned invalid restaurants for district [{$districtId}] at offset [{$startAt}].");
        }

        return $restaurants;
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $restaurants
     */
    private function persistRestaurants(Collection $restaurants): void
    {
        $timestamp = now();

        $records = $restaurants
            ->map(function (array $restaurant) use ($timestamp): array {
                if (! isset($restaurant['poiId'])) {
                    throw new RuntimeException('OpenRice returned a restaurant without a poiId.');
                }

                return [
                    'openrice_poi_id' => (string) $restaurant['poiId'],
                    'data' => json_encode($restaurant, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ];
            })
            ->all();

        DB::table('restaurants')->upsert(
            $records,
            ['openrice_poi_id'],
            ['data', 'updated_at'],
        );
    }
}
