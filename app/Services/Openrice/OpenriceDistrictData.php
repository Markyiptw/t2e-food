<?php

namespace App\Services\Openrice;

use Illuminate\Support\Facades\Validator;

final readonly class OpenriceDistrictData
{
    public function __construct(
        public int $externalId,
        public string $name,
    ) {}

    /**
     * @param  array<string, mixed>|null  $payload
     */
    public static function fromArray(?array $payload): ?self
    {
        if ($payload === null) {
            return null;
        }

        Validator::make($payload, [
            'districtId' => ['required', 'integer'],
            'name' => ['required', 'string'],
        ])->stopOnFirstFailure()->validate();

        /** @var array{districtId: int, name: string} $payload */
        return new self(
            externalId: $payload['districtId'],
            name: $payload['name'],
        );
    }
}
