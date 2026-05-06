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
        LazyCollection::make(function () {
            $districts = Http::openrice()
                ->get('/metadata/region/all')
                ->collect('districts');

            FacadesValidator::validate($districts->all(), [
                '*.districtId' => ['required', 'numeric'],
                '*.districtGroupId' => ['nullable', 'numeric'],
            ]);

            $last = Restaurant::latest('updated_at')
                ->first()
                ?->query_params;

            $districtIds = $districts
                ->whereNotNull('districtGroupId')
                ->pluck('districtId')
                ->sort()
                ->sortBy(fn ($districtId) => ! ($districtId >= ($last['districtId'] ?? PHP_INT_MAX)))
                ->values()
                ->dump();

            foreach ($districtIds as $districtId) {
                $queryParameters = [
                    'districtId' => $districtId,
                    'startAt' => $last['startAt'] ?? 0,
                    'rows' => 50,
                ];

                do {
                    $response = Http::openrice()
                        ->get('/search', $queryParameters);

                    $restaurants = $response->collect('paginationResult.results');

                    yield ['restaurants' => $restaurants, 'queryParameters' => $queryParameters];

                    dump($queryParameters);

                    $total = $response->json('paginationResult.count');

                    $queryParameters['startAt'] += $restaurants->count();
                } while ($queryParameters['startAt'] < $total);
            }
        })
            ->each(fn (array $result) => Restaurant::upsert(
                $result['restaurants']
                    ->map(fn (array $restaurant) => [
                        'data' => json_encode($restaurant, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                        'query_params' => json_encode($result['queryParameters'], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ])
                    ->all(),
                [DB::raw("(data->>'poiId')")],
                ['data', 'updated_at'],
            ));
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

            DB::transaction(function () use ($restaurants, $queryParameters) {
                $this->persistRestaurants($restaurants, $this->requestParameters($queryParameters));
            });

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
     * Persist scraped restaurants into the database.
     *
     * The `data` JSON column stores the full OpenRice API response. The `poiHours` sub-key
     * (a JSON array of time-slot objects) encodes the restaurant's opening hours:
     *
     * ── Object fields ──
     * - `pos`:          Category — 1–7 = Mon–Sun, 201–202 = holiday/holiday-eve override,
     *                   211+ = special date override (with dateFrom/dateTo).
     * - `weight`:       Priority — 0 = normal, 4 = holiday-level override, 5 = date-specific.
     * - `dayOfWeek`:    1=Mon..7=Sun, 0 = applies regardless (used by overrides).
     * - `period1Start/End`: First opening block (e.g. "12:00:00"–"22:30:00").
     * - `period2Start/End`: Optional second block (split shifts, e.g. lunch + dinner).
     * - `is24hr`:       24-hour opening (2,158 entries across all restaurants).
     * - `isClose`:      Restaurant closed (7,470 entries across all restaurants).
     * - `isHoliday` / `isHolidayEve`: Flags for pos:201–202 overrides.
     * - `dateFrom` / `dateTo`: Date range for pos:211+ special-date overrides.
     * - `displayNameLang1/2`: Human-readable label (e.g. "年初一至年初三").
     * - `modifyTime`:   Last-modification timestamp.
     * - `isUncertain`:  Whether hours are unconfirmed.
     * - `day`, `lunarDay`, `weekOfMonth`: Always 0 in current data.
     *
     * ── Distribution (32,914 restaurants) ──
     * - 0 entries:  5,873  (no hours data)
     * - 7 entries: 19,344  (weekly schedule only — the standard case)
     * - 8 entries:  3,893  (weekly + 1 override)
     * - 9 entries:  2,832  (weekly + 2 overrides)
     * - 10 entries:   584  (weekly + 3 overrides)
     * - 11 entries:   131  (weekly + 4 overrides)
     * - 12 entries:    31  (weekly + 5 overrides)
     *
     * @param  Collection<int, array<string, mixed>>  $restaurants
     * @param  array<string, int|string>  $queryParameters
     */
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
                    'name' => isset($restaurant['name']) && is_string($restaurant['name']) ? $restaurant['name'] : null,
                    'latitude' => $this->numericOrNull($restaurant['mapLatitude'] ?? null),
                    'longitude' => $this->numericOrNull($restaurant['mapLongitude'] ?? null),
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
            ['name', 'latitude', 'longitude', 'data', 'query_params', 'updated_at'],
        );

        $openricePoiIds = array_column($records, 'openrice_poi_id');

        $restaurantIds = DB::table('restaurants')
            ->whereIn('openrice_poi_id', $openricePoiIds)
            ->pluck('id', 'openrice_poi_id');

        DB::table('restaurant_hours')
            ->whereIn('restaurant_id', $restaurantIds->values())
            ->delete();

        $hoursRows = [];

        foreach ($restaurants as $restaurant) {
            $poiId = (string) $restaurant['poiId'];
            $restaurantId = $restaurantIds[$poiId] ?? null;

            if ($restaurantId === null) {
                continue;
            }

            foreach ($restaurant['poiHours'] ?? [] as $hours) {
                if (! is_array($hours)) {
                    continue;
                }

                if (($hours['weight'] ?? 0) !== 0) {
                    continue;
                }

                if (($hours['isClose'] ?? false) === true) {
                    continue;
                }

                $hoursRows[] = [
                    'restaurant_id' => $restaurantId,
                    'day_of_week' => $hours['dayOfWeek'] ?? 0,
                    'weight' => $hours['weight'] ?? 0,
                    'is_24hr' => $hours['is24hr'] ?? false,
                    'is_close' => $hours['isClose'] ?? false,
                    'period_1_start' => $this->timeOrNull($hours['period1Start'] ?? null),
                    'period_1_end' => $this->timeOrNull($hours['period1End'] ?? null),
                    'period_2_start' => $this->timeOrNull($hours['period2Start'] ?? null),
                    'period_2_end' => $this->timeOrNull($hours['period2End'] ?? null),
                    'period_3_start' => $this->timeOrNull($hours['period3Start'] ?? null),
                    'period_3_end' => $this->timeOrNull($hours['period3End'] ?? null),
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ];
            }
        }

        if ($hoursRows !== []) {
            DB::table('restaurant_hours')->insert($hoursRows);
        }
    }

    private function numericOrNull(mixed $value): ?float
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        return null;
    }

    private function timeOrNull(mixed $value): ?string
    {
        if (is_string($value) && $value !== '') {
            return $value;
        }

        return null;
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
