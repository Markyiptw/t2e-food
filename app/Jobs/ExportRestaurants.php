<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\LazyCollection;
use RuntimeException;
use Spatie\SimpleExcel\SimpleExcelWriter;
use stdClass;
use Throwable;

class ExportRestaurants implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    private const string EXPORT_DIRECTORY = 'exports';

    private const string EXPORT_PATH = 'exports/restaurants.xlsx';

    private const string TEMP_EXPORT_PATH = 'exports/restaurants.tmp.xlsx';

    /**
     * @var array<int, string>
     */
    private const array HEADERS = [
        'restaurantName',
        'address',
        'dayOfWeek',
        'start',
        'end',
    ];

    /**
     * @var array<int, string>
     */
    private const array DAY_OF_WEEK_LABELS = [
        1 => 'Sun',
        2 => 'Mon',
        3 => 'Tue',
        4 => 'Wed',
        5 => 'Thu',
        6 => 'Fri',
        7 => 'Sat',
    ];

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $disk = Storage::disk('local');

        $disk->makeDirectory(self::EXPORT_DIRECTORY);
        $disk->delete(self::TEMP_EXPORT_PATH);

        $writer = SimpleExcelWriter::create($disk->path(self::TEMP_EXPORT_PATH))
            ->addHeader(self::HEADERS);

        foreach ($this->restaurants() as $restaurant) {
            foreach ($this->exportRowsForRestaurant($restaurant) as $row) {
                $writer->addRow($row);
            }
        }

        $writer->close();

        $disk->delete(self::EXPORT_PATH);
        $disk->move(self::TEMP_EXPORT_PATH, self::EXPORT_PATH);
    }

    public function failed(?Throwable $exception): void
    {
        Storage::disk('local')->delete(self::TEMP_EXPORT_PATH);

        if ($exception === null) {
            return;
        }

        Log::error('Failed to export restaurants.', [
            'exception' => $exception::class,
            'message' => $exception->getMessage(),
        ]);
    }

    public function uniqueId(): string
    {
        return self::EXPORT_PATH;
    }

    /**
     * @return LazyCollection<int, stdClass>
     */
    private function restaurants()
    {
        return DB::table('restaurants')
            ->select(['openrice_poi_id', 'data'])
            ->orderBy('id')
            ->cursor();
    }

    /**
     * @return array<int, array<int, string>>
     */
    private function exportRowsForRestaurant(stdClass $restaurant): array
    {
        $data = json_decode((string) $restaurant->data, true, flags: JSON_THROW_ON_ERROR);

        if (! is_array($data)) {
            throw new RuntimeException("Restaurant [{$restaurant->openrice_poi_id}] has invalid export data.");
        }

        $restaurantName = $this->stringValue($data['name'] ?? null, 'name', (string) $restaurant->openrice_poi_id);
        $address = $this->optionalStringValue($data['address'] ?? null, 'address', (string) $restaurant->openrice_poi_id);
        $poiHours = $data['poiHours'] ?? [];

        if (! is_array($poiHours)) {
            throw new RuntimeException("Restaurant [{$restaurant->openrice_poi_id}] has invalid poiHours data.");
        }

        return collect($poiHours)
            ->map(function (mixed $hours) use ($restaurant): array {
                if (! is_array($hours)) {
                    throw new RuntimeException("Restaurant [{$restaurant->openrice_poi_id}] has an invalid poiHours entry.");
                }

                return $hours;
            })
            ->flatMap(fn (array $hours): array => $this->exportRowsForPoiHours($restaurantName, $address, $hours))
            ->values()
            ->all();
    }

    /**
     * @return array<int, array<int, string>>
     */
    private function exportRowsForPoiHours(string $restaurantName, string $address, array $hours): array
    {
        $dayOfWeek = $hours['dayOfWeek'] ?? null;

        if (! is_numeric($dayOfWeek)) {
            throw new RuntimeException('Encountered poiHours data without a numeric dayOfWeek value.');
        }

        $dayOfWeek = (int) $dayOfWeek;

        if ($dayOfWeek === 0) {
            return [];
        }

        $dayLabel = self::DAY_OF_WEEK_LABELS[$dayOfWeek] ?? throw new RuntimeException(
            "Encountered unsupported dayOfWeek [{$dayOfWeek}] while exporting restaurants.",
        );

        return collect(range(1, 3))
            ->map(fn (int $period): ?array => $this->exportRowForPeriod($restaurantName, $address, $dayLabel, $hours, $period))
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @return array<int, string>|null
     */
    private function exportRowForPeriod(
        string $restaurantName,
        string $address,
        string $dayLabel,
        array $hours,
        int $period,
    ): ?array {
        $start = $hours["period{$period}Start"] ?? null;
        $end = $hours["period{$period}End"] ?? null;

        if (! is_string($start) || $start === '' || ! is_string($end) || $end === '') {
            return null;
        }

        return [
            $restaurantName,
            $address,
            $dayLabel,
            $start,
            $end,
        ];
    }

    private function stringValue(mixed $value, string $key, string $openricePoiId): string
    {
        if (! is_string($value) || $value === '') {
            throw new RuntimeException("Restaurant [{$openricePoiId}] is missing a valid [{$key}] value.");
        }

        return $value;
    }

    private function optionalStringValue(mixed $value, string $key, string $openricePoiId): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        if (! is_string($value)) {
            throw new RuntimeException("Restaurant [{$openricePoiId}] has an invalid [{$key}] value.");
        }

        return $value;
    }
}
