<?php

namespace App\Services\Openrice;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

final readonly class OpenriceRestaurantData
{
    /**
     * @param  Collection<int, OpenriceCategoryData>  $categories
     * @param  Collection<int, OpenriceHourData>  $hours
     */
    public function __construct(
        public int $poiId,
        public string $name,
        public ?string $url,
        public int $status,
        public ?string $statusText,
        public ?string $address,
        public ?OpenriceDistrictData $district,
        public ?OpenriceLocationData $location,
        public Collection $categories,
        public Collection $hours,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromArray(array $payload): self
    {
        Validator::make($payload, self::rules())
            ->stopOnFirstFailure()
            ->validate();

        /** @var array{poiId: int, name: string, status: int, shortenUrl?: string|null, statusText?: string|null, address?: string|null, district?: array<string, mixed>|null, mapLatitude?: mixed, mapLongitude?: mixed, categories?: array<int, array<string, mixed>>, poiHours?: array<int, array<string, mixed>>} $payload */
        return new self(
            poiId: $payload['poiId'],
            name: $payload['name'],
            url: $payload['shortenUrl'] ?? null,
            status: $payload['status'],
            statusText: $payload['statusText'] ?? null,
            address: $payload['address'] ?? null,
            district: OpenriceDistrictData::fromArray($payload['district'] ?? null),
            location: OpenriceLocationData::fromCoordinates($payload['mapLatitude'] ?? null, $payload['mapLongitude'] ?? null),
            categories: collect($payload['categories'] ?? [])->map(fn (array $category): OpenriceCategoryData => OpenriceCategoryData::fromArray($category)),
            hours: collect($payload['poiHours'] ?? [])->map(fn (array $hour): OpenriceHourData => OpenriceHourData::fromArray($hour)),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'poiId' => $this->poiId,
            'name' => $this->name,
            'shortenUrl' => $this->url,
            'status' => $this->status,
            'statusText' => $this->statusText,
            'address' => $this->address,
            'district' => $this->district ? [
                'districtId' => $this->district->externalId,
                'name' => $this->district->name,
            ] : null,
            'mapLatitude' => $this->location?->latitude,
            'mapLongitude' => $this->location?->longitude,
            'categories' => $this->categories
                ->map(fn (OpenriceCategoryData $category): array => ['name' => $category->name])
                ->all(),
            'poiHours' => $this->hours
                ->map(fn (OpenriceHourData $hour): array => [
                    'dayOfWeek' => $hour->dayOfWeek,
                    'weight' => $hour->weight,
                    'isClose' => $hour->isClose,
                    'is24hr' => $hour->is24Hr,
                    ...$hour->periods
                        ->flatMap(fn (OpenricePeriodData $period): array => [
                            "period{$period->position}Start" => $period->start,
                            "period{$period->position}End" => $period->end,
                        ])
                        ->all(),
                ])
                ->all(),
        ];
    }

    /**
     * @return array<string, list<string>>
     */
    private static function rules(): array
    {
        return [
            'poiId' => ['required', 'integer'],
            'name' => ['required', 'string'],
            'shortenUrl' => ['nullable', 'string'],
            'status' => ['required', 'integer'],
            'statusText' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'district' => ['nullable', 'array'],
            'mapLatitude' => ['nullable'],
            'mapLongitude' => ['nullable'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['required', 'array'],
            'poiHours' => ['nullable', 'array'],
            'poiHours.*' => ['required', 'array'],
        ];
    }
}
