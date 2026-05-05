<?php

namespace Tests\Feature;

use App\Jobs\ExportRestaurants;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\SimpleExcel\SimpleExcelReader;
use Tests\TestCase;

class ExportRestaurantsJobTest extends TestCase
{
    use LazilyRefreshDatabase;

    private const string EXPORT_PATH = 'exports/restaurants.xlsx';

    protected function tearDown(): void
    {
        Storage::disk('local')->deleteDirectory('exports');

        parent::tearDown();
    }

    public function test_it_exports_restaurants_to_a_fixed_workbook_with_normalized_period_rows(): void
    {
        $this->insertRestaurant('101', [
            'name' => 'Late Cafe',
            'address' => '1 Night Street',
            'poiHours' => [
                [
                    'dayOfWeek' => 2,
                    'period1Start' => '18:00:00',
                    'period1End' => '23:00:00',
                    'period2Start' => '23:30:00',
                    'period2End' => '02:30:00',
                ],
                [
                    'dayOfWeek' => 0,
                    'displayNameLang1' => '年初一',
                    'period1Start' => '10:00:00',
                    'period1End' => '20:00:00',
                ],
                [
                    'dayOfWeek' => 7,
                    'period3Start' => '09:00:00',
                    'period3End' => '12:00:00',
                ],
            ],
        ]);

        $this->insertRestaurant('202', [
            'name' => 'Breakfast Spot',
            'address' => '2 Day Road',
            'poiHours' => [
                [
                    'dayOfWeek' => 1,
                    'period1Start' => '08:00:00',
                    'period1End' => '14:00:00',
                ],
            ],
        ]);

        (new ExportRestaurants)->handle();

        $disk = Storage::disk('local');

        $this->assertTrue($disk->exists(self::EXPORT_PATH));
        $this->assertSame(
            ['restaurantName', 'address', 'dayOfWeek', 'start', 'end'],
            SimpleExcelReader::create($disk->path(self::EXPORT_PATH))->getHeaders(),
        );
        $this->assertSame([
            [
                'restaurantName' => 'Late Cafe',
                'address' => '1 Night Street',
                'dayOfWeek' => 'Mon',
                'start' => '18:00:00',
                'end' => '23:00:00',
            ],
            [
                'restaurantName' => 'Late Cafe',
                'address' => '1 Night Street',
                'dayOfWeek' => 'Mon',
                'start' => '23:30:00',
                'end' => '02:30:00',
            ],
            [
                'restaurantName' => 'Late Cafe',
                'address' => '1 Night Street',
                'dayOfWeek' => 'Sat',
                'start' => '09:00:00',
                'end' => '12:00:00',
            ],
            [
                'restaurantName' => 'Breakfast Spot',
                'address' => '2 Day Road',
                'dayOfWeek' => 'Sun',
                'start' => '08:00:00',
                'end' => '14:00:00',
            ],
        ], $this->exportedRows());
    }

    public function test_it_replaces_the_existing_export_file_instead_of_appending_rows(): void
    {
        $this->insertRestaurant('101', [
            'name' => 'First Run',
            'address' => '1 Replace Street',
            'poiHours' => [
                [
                    'dayOfWeek' => 2,
                    'period1Start' => '10:00:00',
                    'period1End' => '22:00:00',
                ],
            ],
        ]);

        (new ExportRestaurants)->handle();

        DB::table('restaurants')->delete();

        $this->insertRestaurant('202', [
            'name' => 'Second Run',
            'address' => '2 Replace Street',
            'poiHours' => [
                [
                    'dayOfWeek' => 6,
                    'period1Start' => '11:00:00',
                    'period1End' => '23:30:00',
                ],
            ],
        ]);

        (new ExportRestaurants)->handle();

        $this->assertSame([
            [
                'restaurantName' => 'Second Run',
                'address' => '2 Replace Street',
                'dayOfWeek' => 'Fri',
                'start' => '11:00:00',
                'end' => '23:30:00',
            ],
        ], $this->exportedRows());
    }

    public function test_it_exports_missing_addresses_as_blank_cells(): void
    {
        $this->insertRestaurant('303', [
            'name' => 'Addressless Cafe',
            'poiHours' => [
                [
                    'dayOfWeek' => 3,
                    'period1Start' => '09:00:00',
                    'period1End' => '17:00:00',
                ],
            ],
        ]);

        (new ExportRestaurants)->handle();

        $this->assertSame([
            [
                'restaurantName' => 'Addressless Cafe',
                'address' => '',
                'dayOfWeek' => 'Tue',
                'start' => '09:00:00',
                'end' => '17:00:00',
            ],
        ], $this->exportedRows());
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function insertRestaurant(string $openricePoiId, array $data): void
    {
        DB::table('restaurants')->insert([
            'openrice_poi_id' => $openricePoiId,
            'name' => null,
            'latitude' => null,
            'longitude' => null,
            'data' => json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'query_params' => json_encode(['source' => 'test'], JSON_THROW_ON_ERROR),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function exportedRows(): array
    {
        return SimpleExcelReader::create(Storage::disk('local')->path(self::EXPORT_PATH))
            ->preserveDateTimeFormatting()
            ->getRows()
            ->values()
            ->all();
    }
}
