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
     * Scope to restaurants that are open during a given time window on a given day of the week.
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
     * When ``$dayOfWeek`` is null, the day-of-week filter is omitted, returning
     * all restaurants open in the given time window regardless of weekday.
     */
    public function scopeOpenInWindow(Builder $query, ?Carbon $start, ?Carbon $end): void
    {
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
                        ->when(
                            $start !== null,
                            fn (Builder $query) => $query->where(
                                /**
                                 * A period like 22:00→02:00 means "open from 10 PM tonight until 2 AM tomorrow".
                                 * It spans two calendar days. The question here is: "has the restaurant already
                                 * opened by the time our query starts?"
                                 *
                                 * Two ways that can be true:
                                 *   (A) Day-1 side: the query starts at or after 22:00 on the same day.
                                 *       → "start" <= $startSeconds   (e.g. 22:00 <= 23:00 ✓)
                                 *   (B) Day-2 side: the restaurant opened yesterday and we're querying
                                 *       early in the morning before it closes. Since the period hasn't
                                 *       ended yet, any query_start before the stored "end" is still inside
                                 *       the active open window.
                                 *       → start > $startSeconds (inferred by condition a failing) AND "start" > "end"  AND  $startSeconds <= "end"
                                 *         (e.g. 22:00 > 00:00 AND 22:00 > 02:00  AND  00:00 <= 02:00 ✓)
                                 */
                                fn (Builder $query) => $query
                                    ->where('start', '<=', $start->format('H:i'))
                                    ->orWhere(fn (Builder $query) => $query
                                        ->whereColumn('start', '>', 'end')
                                        ->where('end', '>=', $start->format('H:i'))
                                    )
                            )
                        )
                        ->when(
                            $end !== null,
                            fn (Builder $query) => $query->where(fn (Builder $query) => $query
                                ->when(
                                    ! isset($start) || $start->lte($end), // normal query
                                    fn (Builder $query) => $query
                                        // think of this as the basic of set of record
                                        ->whereColumn('start', '<', 'end')
                                        ->where('end', '>=', $end->format('H:i'))
                                        // and later you include more records for each edge case
                                        ->when(
                                            isset($start),
                                            fn (Builder $query) => $query
                                                ->orWhere(fn (Builder $query) => $query
                                                    ->whereColumn('start', '>=', 'end') // equal should be be considered overnight, such case should have been convered to 24hrs
                                                    ->where('end', '<', $start->format('H:i')) // similar logic to the day-2 side of the start time check?
                                                ),
                                        ),
                                    /**
                                     * Query spans two calendar days (e.g. 21:00→03:00).
                                     * Add 86400 seconds to the period-end to account for the
                                     * midnight-crossing offset, then compare against the query
                                     * end (which is already on day-2).
                                     */
                                    fn (Builder $query) => $query
                                        ->whereRaw(
                                            '(EXTRACT(EPOCH FROM "end") + CASE WHEN "start" > "end" THEN 86400 ELSE 0 END) >= ?',
                                            [$end->secondsSinceMidnight() + 86400],
                                        )
                                ),
                            )
                        ),
                    )
                )
            );
    }
}
