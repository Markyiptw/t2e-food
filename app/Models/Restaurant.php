<?php

namespace App\Models;

use Closure;
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

    public function scopeActive(Builder $query): void
    {
        $query
            ->whereRaw("(data->>'status')::int = ?", [10])
            ->whereNull('data->statusText');
    }

    public function scopeHasLocation(Builder $query): void
    {
        $query
            ->whereNotNull('data->mapLatitude')
            ->whereNotNull('data->mapLongitude')
            ->whereRaw("(data->>'mapLatitude')::float != 0")
            ->whereRaw("(data->>'mapLongitude')::float != 0");
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
        $startSec = $start->secondsSinceMidnight();
        $endSec = $startSec + ($durationInMinutes * 60);

        $query
            ->whereHas('hours', fn (Builder $query) => $query
                ->baseWeeklySchedule()
                ->where(fn (Builder $query) => $query
                    ->twentyFourHours()
                    ->orWhereHas('periods', fn (Builder $query) => $query->between($startSec, $endSec))
                ));
    }

    /**
     * Scope to restaurants that are open at any point between the given times.
     */
    public function scopeOpenInAnyWindowBetween(Builder $query, Carbon $start, Carbon $end): void
    {
        $queryStartSeconds = $start->secondsSinceMidnight();
        $queryEndSeconds = $end->secondsSinceMidnight();

        if ($queryStartSeconds > $queryEndSeconds) {
            $queryEndSeconds += 86400;
        }

        $periodEndSeconds = '(EXTRACT(EPOCH FROM "end") + CASE WHEN "start" > "end" THEN 86400 ELSE 0 END)';

        $query
            ->whereHas('hours', fn (Builder $query) => $query
                ->baseWeeklySchedule()
                ->where(fn (Builder $query) => $query
                    ->twentyFourHours()
                    ->orWhereHas('periods', fn (Builder $query) => $query
                        ->whereRaw(
                            'EXTRACT(EPOCH FROM "start") <= ? AND '.$periodEndSeconds.' >= ?',
                            [$queryEndSeconds, $queryStartSeconds],
                        )
                        ->orWhereRaw(
                            '(EXTRACT(EPOCH FROM "start") - 86400) <= ? AND ('.$periodEndSeconds.' - 86400) >= ?',
                            [$queryEndSeconds, $queryStartSeconds],
                        )
                        ->orWhereRaw(
                            '(EXTRACT(EPOCH FROM "start") + 86400) <= ? AND ('.$periodEndSeconds.' + 86400) >= ?',
                            [$queryEndSeconds, $queryStartSeconds],
                        )
                    )
                ));

    }

    private function whereHasBaseOpenHours(Builder $query, Closure $periodConstraint): void
    {
        $query->whereHas('hours', fn (Builder $query) => $query
            ->whereRaw('data @> ?', [
                collect([
                    'weight' => 0,
                    'isClose' => false,
                ])
                    ->toJson(),
            ])
            ->where(fn (Builder $query) => $query
                ->whereRaw('data @> ?', [json_encode(['is24hr' => true])])
                ->orWhereHas('periods', $periodConstraint)
            ));
    }
}
