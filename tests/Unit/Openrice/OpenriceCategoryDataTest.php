<?php

namespace Tests\Unit\Openrice;

use App\Services\Openrice\OpenriceCategoryData;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class OpenriceCategoryDataTest extends TestCase
{
    public function test_it_builds_from_a_valid_category(): void
    {
        $data = OpenriceCategoryData::fromArray(['name' => 'Japanese']);

        $this->assertSame('Japanese', $data->name);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    #[DataProvider('invalidPayloads')]
    public function test_it_rejects_invalid_categories(array $payload): void
    {
        $this->expectException(ValidationException::class);

        OpenriceCategoryData::fromArray($payload);
    }

    /**
     * @return array<string, array{array<string, mixed>}>
     */
    public static function invalidPayloads(): array
    {
        return [
            'missing name' => [[]],
            'non string name' => [['name' => 123]],
        ];
    }
}
