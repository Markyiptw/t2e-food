<?php

namespace App\Services\Openrice;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

final readonly class OpenriceSearchPageData
{
    /**
     * @param  Collection<int, OpenriceRestaurantData>  $results
     */
    public function __construct(
        public int $count,
        public Collection $results,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromArray(array $payload): self
    {
        Validator::make($payload, self::rules())
            ->stopOnFirstFailure()
            ->validate();

        /** @var array{paginationResult: array{count: int, results: array<int, array<string, mixed>>}} $payload */
        return new self(
            count: $payload['paginationResult']['count'],
            results: collect($payload['paginationResult']['results'])
                ->map(fn (array $restaurant): OpenriceRestaurantData => OpenriceRestaurantData::fromArray($restaurant)),
        );
    }

    /**
     * @return array<string, list<string>>
     */
    private static function rules(): array
    {
        return [
            'paginationResult' => ['required', 'array'],
            'paginationResult.count' => ['required', 'integer', 'min:0'],
            'paginationResult.results' => ['required', 'array'],
            'paginationResult.results.*' => ['required', 'array'],
        ];
    }
}
