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
        $districts = $this->districts();
        $resumeCheckpoint = $this->resumeCheckpoint($districts);
        $shouldSkipUntilCheckpoint = $resumeCheckpoint !== null;

        foreach ($districts as $district) {
            if ($shouldSkipUntilCheckpoint) {
                if ($district['id'] !== $resumeCheckpoint['districtId']) {
                    continue;
                }

                $shouldSkipUntilCheckpoint = false;
                $this->scrapeDistrict($district['id'], $resumeCheckpoint['startAt']);

                continue;
            }

            $this->scrapeDistrict($district['id']);
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
     * @return array<int, array{id: int, count: int}>
     */
    private function districts(): array
    {
        $payload = $this->requestPage([
            'regionId' => self::REGION_ID,
            'startAt' => 0,
        ]);

        // https://www.openrice.com/api/v2/metadata/region/all?uiLang=zh&uiCity=hongkong
        // status = 10
        // to-do: make a seperated scrape district job
        // build a relatinship between district and its bucket, e.g. parent_id / districtGroupId

        $districts = collect(data_get($payload, 'refineSearchFilter.districts', []))
            ->filter(fn (array $district): bool => $this->shouldScrapeDistrict($district))
            ->map(fn (array $district): array => $this->districtFromMetadata($district))
            ->unique('id')
            ->values()
            ->all();

        if ($districts === []) {
            throw new RuntimeException('Unable to determine OpenRice district filters.');
        }

        return $districts;
    }

    private function shouldScrapeDistrict(array $district): bool
    {
        $districtId = (int) ($district['id'] ?? 0);

        return $districtId > 0 && ! in_array($districtId, self::REGION_BUCKET_IDS, true);
    }

    /**
     * @param  array{id: int, count: int}  $district
     */
    private function districtFromMetadata(array $district): array
    {
        $districtId = (int) ($district['id'] ?? 0);
        $count = $district['count'] ?? null;

        if (! is_numeric($count)) {
            throw new RuntimeException("OpenRice did not return a total count for district [{$districtId}].");
        }

        return [
            'id' => $districtId,
            'count' => (int) $count,
        ];
    }

    /**
     * @param  array<int, array{id: int, count: int}>  $districts
     * @return array{districtId: int, startAt: int}|null
     */
    private function resumeCheckpoint(array $districts): ?array
    {
        $latestRestaurant = DB::table('restaurants')
            ->select(['id', 'openrice_poi_id', 'query_params'])
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->first();

        if ($latestRestaurant === null) {
            return null;
        }

        $queryParameters = $this->decodedQueryParameters($latestRestaurant->query_params ?? null);

        if ($queryParameters === null) {
            Log::warning('Unable to resume OpenRice scraping because the latest restaurant has invalid query parameters.', [
                'restaurant_id' => $latestRestaurant->id,
                'openrice_poi_id' => $latestRestaurant->openrice_poi_id,
            ]);

            return null;
        }

        $districtId = $queryParameters['districtId'] ?? null;
        $startAt = $queryParameters['startAt'] ?? null;
        $rows = $queryParameters['rows'] ?? self::ROWS_PER_PAGE;

        if (! is_numeric($districtId) || ! is_numeric($startAt) || ! is_numeric($rows)) {
            Log::warning('Unable to resume OpenRice scraping because the latest restaurant query parameters are missing pagination data.', [
                'restaurant_id' => $latestRestaurant->id,
                'openrice_poi_id' => $latestRestaurant->openrice_poi_id,
                'query_params' => $queryParameters,
            ]);

            return null;
        }

        $districtIndex = collect($districts)->search(
            fn (array $district): bool => $district['id'] === (int) $districtId,
        );

        if ($districtIndex === false) {
            Log::warning('Unable to resume OpenRice scraping because the saved district no longer exists.', [
                'restaurant_id' => $latestRestaurant->id,
                'openrice_poi_id' => $latestRestaurant->openrice_poi_id,
                'district_id' => (int) $districtId,
            ]);

            return null;
        }

        $nextStartAt = (int) $startAt + (int) $rows;
        $currentDistrict = $districts[$districtIndex];

        if ($nextStartAt < $currentDistrict['count']) {
            return [
                'districtId' => $currentDistrict['id'],
                'startAt' => $nextStartAt,
            ];
        }

        $nextDistrict = $districts[$districtIndex + 1] ?? null;

        if ($nextDistrict === null) {
            return null;
        }

        return [
            'districtId' => $nextDistrict['id'],
            'startAt' => 0,
        ];
    }

    private function scrapeDistrict(int $districtId, int $startAt = 0): void
    {
        $totalCount = null;

        do {
            $queryParameters = [
                'regionId' => self::REGION_ID,
                'districtId' => $districtId,
                'startAt' => $startAt,
            ];
            $payload = $this->requestPage($queryParameters);

            $restaurants = $this->restaurantsFromPayload($payload, $districtId, $startAt);

            if ($restaurants->isEmpty()) {
                return;
            }

            $totalCount = $this->countFromPayload($payload, $districtId);
            $this->persistRestaurants($restaurants, $this->requestParameters($queryParameters));

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
            ->get(self::ENDPOINT, $this->requestParameters($parameters))
            ->throw()
            ->json();

        if (! is_array($payload)) {
            throw new RuntimeException('OpenRice returned an invalid response payload.');
        }

        return $payload;
    }

    /**
     * @param  array<string, int|string>  $parameters
     * @return array<string, int|string>
     */
    private function requestParameters(array $parameters): array
    {
        return [
            ...$parameters,
            'rows' => self::ROWS_PER_PAGE,
            'pageToken' => self::PAGE_TOKEN,
            'uiLang' => 'zh',
            'uiCity' => 'hongkong',
        ];
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
     * @param  array<string, int|string>  $queryParameters
     */
    private function persistRestaurants(Collection $restaurants, array $queryParameters): void
    {
        $timestamp = now();

        $records = $restaurants
            ->map(function (array $restaurant) use ($queryParameters, $timestamp): array {
                if (! isset($restaurant['poiId'])) {
                    throw new RuntimeException('OpenRice returned a restaurant without a poiId.');
                }

                return [
                    'openrice_poi_id' => (string) $restaurant['poiId'],
                    'data' => json_encode($restaurant, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'query_params' => json_encode($queryParameters, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ];
            })
            ->all();

        DB::table('restaurants')->upsert(
            $records,
            ['openrice_poi_id'],
            ['data', 'query_params', 'updated_at'],
        );
    }

    /**
     * @return array<string, mixed>|null
     */
    private function decodedQueryParameters(mixed $queryParameters): ?array
    {
        if (is_array($queryParameters)) {
            return $queryParameters;
        }

        if (! is_string($queryParameters) || $queryParameters === '') {
            return null;
        }

        $decoded = json_decode($queryParameters, true);

        return is_array($decoded) ? $decoded : null;
    }
}
