<?php

namespace App\Services\Openrice;

use Illuminate\Support\Facades\Validator;

final readonly class OpenriceCategoryData
{
    public function __construct(
        public string $name,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromArray(array $payload): self
    {
        Validator::make($payload, [
            'name' => ['required', 'string'],
        ])->stopOnFirstFailure()->validate();

        /** @var array{name: string} $payload */
        return new self(name: $payload['name']);
    }
}
