<?php

namespace App\Services\Openrice;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

final readonly class OpenriceHourData
{
    /**
     * @param  Collection<int, OpenricePeriodData>  $periods
     */
    public function __construct(
        public int $dayOfWeek,
        public int $weight,
        public bool $isClose,
        public bool $is24Hr,
        public Collection $periods,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromArray(array $payload): self
    {
        Validator::make($payload, [
            'dayOfWeek' => ['nullable', 'integer'],
            'weight' => ['nullable', 'integer'],
            'isClose' => ['nullable', 'boolean'],
            'is24hr' => ['nullable', 'boolean'],
        ])->stopOnFirstFailure()->validate();

        return new self(
            dayOfWeek: $payload['dayOfWeek'] ?? 0,
            weight: $payload['weight'] ?? 0,
            isClose: $payload['isClose'] ?? false,
            is24Hr: $payload['is24hr'] ?? false,
            periods: self::periodsFromArray($payload),
        );
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return Collection<int, OpenricePeriodData>
     */
    private static function periodsFromArray(array $payload): Collection
    {
        $periodKeys = collect($payload)
            ->keys()
            ->filter(fn (string|int $key): bool => is_string($key) && preg_match('/^period\d+(Start|End)$/', $key) === 1);

        Validator::make($payload, $periodKeys
            ->mapWithKeys(fn (string $key): array => [$key => ['required', 'date_format:H:i:s']])
            ->all()
        )->stopOnFirstFailure()->validate();

        return $periodKeys
            ->map(fn (string $key): string => (string) preg_replace('/^period(\d+)(Start|End)$/', '$1', $key))
            ->unique()
            ->sort()
            ->values()
            ->map(function (string $position) use ($payload): OpenricePeriodData {
                $startKey = "period{$position}Start";
                $endKey = "period{$position}End";

                if (! array_key_exists($startKey, $payload) || ! array_key_exists($endKey, $payload)) {
                    throw ValidationException::withMessages([
                        "period{$position}" => "Both {$startKey} and {$endKey} are required.",
                    ]);
                }

                return new OpenricePeriodData(
                    position: (int) $position,
                    start: $payload[$startKey],
                    end: $payload[$endKey],
                );
            });
    }
}
