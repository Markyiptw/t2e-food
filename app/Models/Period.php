<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'position',
    'start',
    'end',
    'hour_id',
])]

class Period extends Model
{
    use HasFactory;

    public function scopeBetween(Builder $query, int $startSec, int $endSec): void
    {
        $query
            ->whereRaw(
                '"start" <= "end" AND EXTRACT(EPOCH FROM "start") <= ? AND EXTRACT(EPOCH FROM "end") >= ?',
                [$startSec, $endSec],
            )
            ->orWhereRaw(
                '"start" > "end" AND EXTRACT(EPOCH FROM "start") <= ? AND (EXTRACT(EPOCH FROM "end") + 86400) >= ?',
                [$startSec, $endSec],
            )
            ->orWhereRaw(
                '"start" > "end" AND (EXTRACT(EPOCH FROM "start") - 86400) <= ? AND EXTRACT(EPOCH FROM "end") >= ?',
                [$startSec, $endSec],
            );
    }
}
