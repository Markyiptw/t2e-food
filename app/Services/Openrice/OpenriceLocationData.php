<?php

namespace App\Services\Openrice;

use Illuminate\Support\Facades\Validator;

final readonly class OpenriceLocationData
{
    public function __construct(
        public float $latitude,
        public float $longitude,
    ) {}

    public static function fromCoordinates(mixed $latitude, mixed $longitude): ?self
    {
        if (! $latitude || ! $longitude || $latitude == 0 || $longitude == 0) {
            return null;
        }

        Validator::make([
            'latitude' => $latitude,
            'longitude' => $longitude,
        ], [
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
        ])->stopOnFirstFailure()->validate();

        return new self(
            latitude: (float) $latitude,
            longitude: (float) $longitude,
        );
    }
}
