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
        public OpenriceStatusData $status,
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
            status: OpenriceStatusData::fromArray($payload),
            address: $payload['address'] ?? null,
            district: OpenriceDistrictData::fromArray($payload['district'] ?? null),
            location: OpenriceLocationData::fromCoordinates($payload['mapLatitude'] ?? null, $payload['mapLongitude'] ?? null),
            categories: collect($payload['categories'] ?? [])->map(fn (array $category): OpenriceCategoryData => OpenriceCategoryData::fromArray($category)),
            hours: collect($payload['poiHours'] ?? [])
                ->filter(function (array $hour): bool {
                    /**
                     * Hours ``weight`` semantics (higher weight overrides lower):
                     * - 0: base weekly schedule (dayOfWeek 1-7)
                     * - 1: lunar-day schedule (dayOfWeek 0, ``lunarDay`` field)
                     * - 2: week-of-month exception (dayOfWeek 1-7, ``weekOfMonth`` field)
                     * - 4: public-holiday schedule (dayOfWeek 0, ``isHoliday`` or ``isHolidayEve`` flag)
                     * - 5: date-range schedule (dayOfWeek 0, ``dateFrom``/``dateTo`` fields)
                     *
                     * Filtering to ``weight => 0`` restricts to the base weekly schedule only,
                     * deliberately ignoring week-of-month and special-occasion overrides.
                     */
                    return ($hour['weight'] ?? 0) === 0;
                })
                ->map(fn (array $hour): OpenriceHourData => OpenriceHourData::fromArray($hour)),
        );
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
