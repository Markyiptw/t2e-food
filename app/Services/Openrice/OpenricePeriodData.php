<?php

namespace App\Services\Openrice;

final readonly class OpenricePeriodData
{
    public function __construct(
        public int $position,
        public string $start,
        public string $end,
    ) {}
}
