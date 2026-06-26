<?php

namespace App\Services\Openrice;

use Illuminate\Support\Facades\Validator;

final readonly class OpenriceStatusData
{
    public function __construct(
        public int $code,
        public ?string $text,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromArray(array $payload): self
    {
        Validator::make($payload, [
            'status' => ['required', 'integer'],
            'statusText' => ['nullable', 'string'],
        ])->stopOnFirstFailure()->validate();

        /** @var array{status: int, statusText?: string|null} $payload */
        return new self(
            code: $payload['status'],
            text: $payload['statusText'] ?? null,
        );
    }
}
