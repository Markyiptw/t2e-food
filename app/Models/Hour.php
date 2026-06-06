<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['restaurant_id', 'data'])]
class Hour extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'data' => 'array',
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
            get: fn () => $labels[$this->data['dayOfWeek']],
        );
    }

    public function humanReadablePeriod(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => match (true) {
                $this->data['isClose'] => 'closed',
                $this->data['is24hr'] => '24 hours',
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
        $query
            ->whereRaw('data @> ?', [
                collect([
                    'weight' => 0,
                    'isClose' => false,
                ])
                    ->toJson(),
            ]);
    }

    public function scopeTwentyFourHours(Builder $query): void
    {
        $query
            ->whereRaw('data @> ?', [json_encode(['is24hr' => true])]);
    }
}
