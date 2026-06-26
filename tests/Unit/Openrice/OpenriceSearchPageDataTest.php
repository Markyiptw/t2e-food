<?php

namespace Tests\Unit\Openrice;

use App\Services\Openrice\OpenriceSearchPageData;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class OpenriceSearchPageDataTest extends TestCase
{
    public function test_it_builds_from_a_valid_search_response(): void
    {
        $data = OpenriceSearchPageData::fromArray([
            'paginationResult' => [
                'count' => 12,
                'results' => [
                    ['poiId' => 101, 'name' => 'First Restaurant', 'status' => 10],
                    ['poiId' => 202, 'name' => 'Second Restaurant', 'status' => 10],
                ],
            ],
        ]);

        $this->assertSame(12, $data->count);
        $this->assertCount(2, $data->results);
        $this->assertSame('First Restaurant', $data->results->first()->name);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    #[DataProvider('invalidPayloads')]
    public function test_it_rejects_invalid_search_responses(array $payload): void
    {
        $this->expectException(ValidationException::class);

        OpenriceSearchPageData::fromArray($payload);
    }

    /**
     * @return array<string, array{array<string, mixed>}>
     */
    public static function invalidPayloads(): array
    {
        return [
            'missing pagination result' => [[]],
            'missing count' => [[
                'paginationResult' => [
                    'results' => [],
                ],
            ]],
            'non integer count' => [[
                'paginationResult' => [
                    'count' => 'abc',
                    'results' => [],
                ],
            ]],
            'negative count' => [[
                'paginationResult' => [
                    'count' => -1,
                    'results' => [],
                ],
            ]],
            'missing results' => [[
                'paginationResult' => [
                    'count' => 0,
                ],
            ]],
            'non array result row' => [[
                'paginationResult' => [
                    'count' => 1,
                    'results' => ['not a restaurant row'],
                ],
            ]],
        ];
    }
}
