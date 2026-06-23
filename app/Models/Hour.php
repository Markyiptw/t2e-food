<?php

namespace App\Models;

use Database\Factories\HourFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['restaurant_id', 'day_of_week', 'is_close', 'is_24hr'])]
class Hour extends Model
{
    /** @use HasFactory<HourFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_close' => 'boolean',
            'is_24hr' => 'boolean',
        ];
    }

    public function dayOfWeek(): Attribute
    {
        $labels = [
            1 => 'Sun',
            2 => 'Mon',
            3 => 'Tue',
            4 => 'Wed',
            5 => 'Thu',
            6 => 'Fri',
            7 => 'Sat',
        ];

        return Attribute::make(
            get: fn (?int $value) => $labels[$value] ?? null,
        );
    }

    public function humanReadablePeriod(): Attribute
    {
        return Attribute::make(
            get: fn () => match (true) {
                $this->is_close => 'closed',
                $this->is_24hr => '24 hours',
                default => $this
                    ->periods
                    ->map(fn ($period): string => $period->start.'-'.$period->end)
                    ->implode(', '),
            }
        );
    }

    public function periods(): HasMany
    {
        return $this->hasMany(Period::class);
    }

    public function scopeBaseWeeklySchedule(Builder $query): void
    {
        $query->where('is_close', false);
    }

    public function scopeTwentyFourHours(Builder $query): void
    {
        $query->where('is_24hr', true);
    }
}
