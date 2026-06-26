<?php

namespace Tests\Unit\Openrice;

use App\Services\Openrice\OpenriceRestaurantData;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class OpenriceRestaurantDataTest extends TestCase
{
    public function test_it_builds_from_a_valid_restaurant_result(): void
    {
        $data = OpenriceRestaurantData::fromArray([
            'poiId' => 101,
            'name' => 'Test Restaurant',
            'shortenUrl' => 'https://example.test/restaurant',
            'status' => 10,
            'statusText' => null,
            'address' => '1 Test Street',
            'district' => [
                'districtId' => 1001,
                'name' => 'Central',
            ],
            'mapLatitude' => 22.2819,
            'mapLongitude' => 114.1589,
            'categories' => [
                ['name' => 'Japanese'],
                ['name' => 'Sushi'],
            ],
            'poiHours' => [
                [
                    'dayOfWeek' => 1,
                    'weight' => 0,
                    'isClose' => false,
                    'is24hr' => false,
                    'period1Start' => '09:00:00',
                    'period1End' => '12:00:00',
                    'period2Start' => '14:00:00',
                    'period2End' => '22:00:00',
                ],
            ],
        ]);

        $this->assertSame(101, $data->poiId);
        $this->assertSame('Test Restaurant', $data->name);
        $this->assertSame(10, $data->status->code);
        $this->assertSame('Central', $data->district?->name);
        $this->assertSame(22.2819, $data->location?->latitude);
        $this->assertSame(['Japanese', 'Sushi'], $data->categories->pluck('name')->all());
        $this->assertCount(1, $data->hours);
        $this->assertCount(2, $data->hours->first()->periods);
        $this->assertSame('14:00:00', $data->hours->first()->periods->last()->start);
    }

    public function test_it_normalizes_missing_optional_children(): void
    {
        $data = OpenriceRestaurantData::fromArray([
            'poiId' => 101,
            'name' => 'Test Restaurant',
            'status' => 10,
        ]);

        $this->assertNull($data->url);
        $this->assertNull($data->status->text);
        $this->assertNull($data->address);
        $this->assertNull($data->district);
        $this->assertNull($data->location);
        $this->assertCount(0, $data->categories);
        $this->assertCount(0, $data->hours);
    }

    public function test_it_normalizes_null_district(): void
    {
        $data = OpenriceRestaurantData::fromArray([
            'poiId' => 101,
            'name' => 'Test Restaurant',
            'status' => 10,
            'district' => null,
        ]);

        $this->assertNull($data->district);
    }

    public function test_it_filters_out_non_base_schedule_hours(): void
    {
        $data = OpenriceRestaurantData::fromArray([
            'poiId' => 101,
            'name' => 'Test Restaurant',
            'status' => 10,
            'poiHours' => [
                [
                    'dayOfWeek' => 1,
                    'weight' => 0,
                    'period1Start' => '09:00:00',
                    'period1End' => '22:00:00',
                ],
                [
                    'dayOfWeek' => 0,
                    'weight' => 1,
                ],
                [
                    'dayOfWeek' => 1,
                    'weight' => 2,
                ],
            ],
        ]);

        $this->assertCount(1, $data->hours);
        $this->assertSame(1, $data->hours->first()->dayOfWeek);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    #[DataProvider('invalidPayloads')]
    public function test_it_rejects_invalid_restaurant_results(array $payload): void
    {
        $this->expectException(ValidationException::class);

        OpenriceRestaurantData::fromArray($payload);
    }

    /**
     * @return array<string, array{array<string, mixed>}>
     */
    public static function invalidPayloads(): array
    {
        return [
            'missing poi id' => [[
                'name' => 'Test Restaurant',
                'status' => 10,
            ]],
            'missing name' => [[
                'poiId' => 101,
                'status' => 10,
            ]],
            'missing status' => [[
                'poiId' => 101,
                'name' => 'Test Restaurant',
            ]],
            'invalid district' => [[
                'poiId' => 101,
                'name' => 'Test Restaurant',
                'status' => 10,
                'district' => ['districtId' => 1001],
            ]],
            'invalid category' => [[
                'poiId' => 101,
                'name' => 'Test Restaurant',
                'status' => 10,
                'categories' => [[]],
            ]],
            'incomplete period' => [[
                'poiId' => 101,
                'name' => 'Test Restaurant',
                'status' => 10,
                'poiHours' => [
                    [
                        'dayOfWeek' => 1,
                        'period1Start' => '09:00:00',
                    ],
                ],
            ]],
            'invalid period time' => [[
                'poiId' => 101,
                'name' => 'Test Restaurant',
                'status' => 10,
                'poiHours' => [
                    [
                        'dayOfWeek' => 1,
                        'period1Start' => 'not a time',
                        'period1End' => '22:00:00',
                    ],
                ],
            ]],
        ];
    }
}
