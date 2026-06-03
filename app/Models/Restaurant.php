<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Restaurant extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'data' => 'array',
        ];
    }

    public function hours(): HasMany
    {
        return $this->hasMany(Hour::class);
    }

    protected static function booted(): void
    {
        static::addGlobalScope('active', function (Builder $builder) {
            $builder
                ->where('data->status', 10)
                ->whereNull('data->statusText');
        });

        static::addGlobalScope('hasLocation', function (Builder $builder) {
            $builder
                ->whereNotNull('data->mapLatitude')
                ->whereNotNull('data->mapLongitude')
                ->where('data->mapLatitude', '!=', 0)
                ->where('data->mapLongitude', '!=', 0);
        });
    }

    /**
     * Scope to restaurants that are open for a duration starting at the given time.
     *
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
    public function scopeOpenInWindow(Builder $query, Carbon $start, int $durationInMinutes = 0): void
    {
        $queryStartSeconds = $start->secondsSinceMidnight();
        $queryEndSeconds = $queryStartSeconds + ($durationInMinutes * 60);

        $query
            ->whereHas('hours', fn (Builder $query) => $query
                ->whereRaw('data @> ?', [
                    collect([
                        'weight' => 0,
                        'isClose' => false,
                    ])
                        ->toJson(),
                ])
                ->where(fn (Builder $query) => $query
                    ->whereRaw('data @> ?', [json_encode(['is24hr' => true])])
                    ->orWhereHas('periods', fn (Builder $query) => $query
                        ->where(fn (Builder $query) => $query
                            ->whereRaw(
                                'EXTRACT(EPOCH FROM "start") <= ? AND (EXTRACT(EPOCH FROM "end") + CASE WHEN "start" > "end" THEN 86400 ELSE 0 END) >= ?',
                                [$queryStartSeconds, $queryEndSeconds],
                            )
                            ->orWhereRaw(
                                '"start" > "end" AND (EXTRACT(EPOCH FROM "start") - 86400) <= ? AND EXTRACT(EPOCH FROM "end") >= ?',
                                [$queryStartSeconds, $queryEndSeconds],
                            )
                        ),
                    )
                )
            );
    }
}
