<?php

namespace Tests\Unit\Openrice;

use App\Services\Openrice\OpenriceLocationData;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class OpenriceLocationDataTest extends TestCase
{
    public function test_it_builds_from_valid_coordinates(): void
    {
        $data = OpenriceLocationData::fromCoordinates(22.2819, 114.1589);

        $this->assertSame(22.2819, $data?->latitude);
        $this->assertSame(114.1589, $data?->longitude);
    }

    /**
     * @param  mixed  $latitude
     * @param  mixed  $longitude
     */
    #[DataProvider('emptyCoordinates')]
    public function test_it_normalizes_empty_or_zero_coordinates_to_no_location(mixed $latitude, mixed $longitude): void
    {
        $this->assertNull(OpenriceLocationData::fromCoordinates($latitude, $longitude));
    }

    /**
     * @param  mixed  $latitude
     * @param  mixed  $longitude
     */
    #[DataProvider('invalidCoordinates')]
    public function test_it_rejects_invalid_coordinates(mixed $latitude, mixed $longitude): void
    {
        $this->expectException(ValidationException::class);

        OpenriceLocationData::fromCoordinates($latitude, $longitude);
    }

    /**
     * @return array<string, array{mixed, mixed}>
     */
    public static function emptyCoordinates(): array
    {
        return [
            'missing latitude' => [null, 114.1589],
            'missing longitude' => [22.2819, null],
            'zero latitude' => [0, 114.1589],
            'zero longitude' => [22.2819, 0],
        ];
    }

    /**
     * @return array<string, array{mixed, mixed}>
     */
    public static function invalidCoordinates(): array
    {
        return [
            'invalid latitude' => ['not numeric', 114.1589],
            'invalid longitude' => [22.2819, 'not numeric'],
        ];
    }
}
