<?php

namespace App\Models;

use Database\Factories\RestaurantFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

class Restaurant extends Model
{
    /** @use HasFactory<RestaurantFactory> */
    use HasFactory;

    protected $fillable = [
        'poi_id',
        'name',
        'url',
        'status_id',
        'address',
        'district_id',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('active', fn (Builder $query) => $query->active());
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function location(): HasOne
    {
        return $this->hasOne(Location::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class)->withTimestamps();
    }

    public function hours(): HasMany
    {
        return $this->hasMany(Hour::class);
    }

    public function scopeActive(Builder $query): void
    {
        $query->whereBelongsTo(Status::firstOrCreate(['code' => 10, 'text' => null]));
    }

    public function scopeHasLocation(Builder $query): void
    {
        $query->whereHas('location');
    }

    /**
     * Scope to restaurants that are open for a duration starting at the given time.
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
        $query
            ->whereHas('hours', fn (Builder $query) => $query
                ->baseWeeklySchedule()
                ->where(fn (Builder $query) => $query
                    ->twentyFourHours()
                    ->orWhereHas(
                        'periods',
                        fn (Builder $query) => $query
                            ->overlap(
                                $start->secondsSinceMidnight(),
                                $end->gte($start) ? $end->secondsSinceMidnight() : $end->secondsSinceMidnight() + 86400
                            )
                    )
                ));
    }
}
