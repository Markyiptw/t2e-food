<?php

namespace Tests\Unit\Openrice;

use App\Services\Openrice\OpenriceStatusData;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class OpenriceStatusDataTest extends TestCase
{
    public function test_it_builds_from_a_valid_status(): void
    {
        $data = OpenriceStatusData::fromArray([
            'status' => 10,
            'statusText' => 'Closed for renovation',
        ]);

        $this->assertSame(10, $data->code);
        $this->assertSame('Closed for renovation', $data->text);
    }

    public function test_it_defaults_missing_text_to_null(): void
    {
        $data = OpenriceStatusData::fromArray([
            'status' => 10,
        ]);

        $this->assertSame(10, $data->code);
        $this->assertNull($data->text);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    #[DataProvider('invalidPayloads')]
    public function test_it_rejects_invalid_statuses(array $payload): void
    {
        $this->expectException(ValidationException::class);

        OpenriceStatusData::fromArray($payload);
    }

    /**
     * @return array<string, array{array<string, mixed>}>
     */
    public static function invalidPayloads(): array
    {
        return [
            'missing status code' => [['statusText' => 'Closed']],
            'non integer status code' => [['status' => 'abc']],
            'non string status text' => [['status' => 10, 'statusText' => 123]],
        ];
    }
}
