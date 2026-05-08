<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Restaurant extends Model
{
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

    public function getName(): string
    {
        return $this->name ?? $this->data['name'] ?? 'Unknown';
    }

    public function getAddress(): string
    {
        return $this->data['address'] ?? '';
    }

    public function getLatitude(): ?float
    {
        if ($this->latitude !== null) {
            return (float) $this->latitude;
        }

        $lat = $this->data['mapLatitude'] ?? null;

        return is_numeric($lat) ? (float) $lat : null;
    }

    public function getLongitude(): ?float
    {
        if ($this->longitude !== null) {
            return (float) $this->longitude;
        }

        $lng = $this->data['mapLongitude'] ?? null;

        return is_numeric($lng) ? (float) $lng : null;
    }

    public function getPoiHours(): array
    {
        return $this->data['poiHours'] ?? [];
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
    public function scopeOpenInWindow(Builder $query, ?int $dayOfWeek, ?string $startTime, ?string $endTime): void
    {
        $query
            ->whereHas('hours', fn (Builder $query) => $query
                ->whereRaw('data @> ?', [
                    collect([
                        'weight' => 0,
                        'isClose' => false,
                    ])
                        ->merge($dayOfWeek !== null ? ['dayOfWeek' => $dayOfWeek] : [])
                        ->toJson(),
                ])
                ->where(fn (Builder $query) => $query
                    ->whereRaw('data @> ?', [json_encode(['is24hr' => true])])
                    ->orWhereHas('periods', fn (Builder $query) => $query
                        ->when($startTime !== null, function (Builder $query) use ($startTime) {
                            $startSeconds = Carbon::parse($startTime)->secondsSinceMidnight();

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
                            $query->whereRaw(
                                '(EXTRACT(EPOCH FROM "start") <= ? OR ("start" > "end" AND ? <= EXTRACT(EPOCH FROM "end")))',
                                [$startSeconds, $startSeconds],
                            );
                        })
                        ->when($endTime !== null, function (Builder $query) use ($startTime, $endTime) {
                            $end = Carbon::parse($endTime);
                            $normEnd = $end->secondsSinceMidnight() + (($startTime && Carbon::parse($startTime)->greaterThan($end)) ? 86400 : 0);

                            /**
                             * Mirror of the startTime check: "will the restaurant still be open
                             * when our query ends?" Both the stored "end" and the query's end time
                             * are promoted by 24 h when they cross midnight, so the comparison
                             * works on a single linear timeline.
                             */
                            $query->whereRaw(
                                '(EXTRACT(EPOCH FROM "end") + CASE WHEN "start" > "end" THEN 86400 ELSE 0 END) >= ?',
                                [$normEnd],
                            );
                        })
                    )
                )
            );
    }
}
