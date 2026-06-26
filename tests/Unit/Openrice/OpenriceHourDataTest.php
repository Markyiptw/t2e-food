<?php

namespace Tests\Unit\Openrice;

use App\Services\Openrice\OpenriceHourData;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class OpenriceHourDataTest extends TestCase
{
    public function test_it_builds_from_a_valid_hour(): void
    {
        $data = OpenriceHourData::fromArray([
            'dayOfWeek' => 1,
            'isClose' => false,
            'is24hr' => false,
            'period1Start' => '09:00:00',
            'period1End' => '12:00:00',
            'period2Start' => '14:00:00',
            'period2End' => '22:00:00',
        ]);

        $this->assertSame(1, $data->dayOfWeek);
        $this->assertFalse($data->isClose);
        $this->assertFalse($data->is24Hr);
        $this->assertCount(2, $data->periods);
        $this->assertSame(2, $data->periods->last()->position);
        $this->assertSame('14:00:00', $data->periods->last()->start);
        $this->assertSame('22:00:00', $data->periods->last()->end);
    }

    public function test_it_defaults_missing_optional_values(): void
    {
        $data = OpenriceHourData::fromArray([]);

        $this->assertSame(0, $data->dayOfWeek);
        $this->assertFalse($data->isClose);
        $this->assertFalse($data->is24Hr);
        $this->assertCount(0, $data->periods);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    #[DataProvider('invalidPayloads')]
    public function test_it_rejects_invalid_hours(array $payload): void
    {
        $this->expectException(ValidationException::class);

        OpenriceHourData::fromArray($payload);
    }

    /**
     * @return array<string, array{array<string, mixed>}>
     */
    public static function invalidPayloads(): array
    {
        return [
            'out of range day of week' => [['dayOfWeek' => 8]],
            'zero day of week' => [['dayOfWeek' => 0]],
            'non boolean is close' => [['isClose' => 'not boolean']],
            'non boolean is 24hr' => [['is24hr' => 'not boolean']],
            'incomplete period' => [['period1Start' => '09:00:00']],
            'invalid period start' => [[
                'period1Start' => 'not a time',
                'period1End' => '22:00:00',
            ]],
            'invalid period end' => [[
                'period1Start' => '09:00:00',
                'period1End' => 'not a time',
            ]],
        ];
    }
}
