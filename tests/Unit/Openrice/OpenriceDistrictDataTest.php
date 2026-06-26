<?php

namespace Tests\Unit\Openrice;

use App\Services\Openrice\OpenriceDistrictData;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class OpenriceDistrictDataTest extends TestCase
{
    public function test_it_builds_from_a_valid_district(): void
    {
        $data = OpenriceDistrictData::fromArray([
            'districtId' => 1001,
            'name' => 'Central',
        ]);

        $this->assertSame(1001, $data?->externalId);
        $this->assertSame('Central', $data?->name);
    }

    public function test_it_normalizes_null_to_no_district(): void
    {
        $this->assertNull(OpenriceDistrictData::fromArray(null));
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    #[DataProvider('invalidPayloads')]
    public function test_it_rejects_invalid_districts(array $payload): void
    {
        $this->expectException(ValidationException::class);

        OpenriceDistrictData::fromArray($payload);
    }

    /**
     * @return array<string, array{array<string, mixed>}>
     */
    public static function invalidPayloads(): array
    {
        return [
            'missing district id' => [['name' => 'Central']],
            'missing name' => [['districtId' => 1001]],
            'non integer district id' => [['districtId' => '1001', 'name' => 'Central']],
            'non string name' => [['districtId' => 1001, 'name' => 123]],
        ];
    }
}
