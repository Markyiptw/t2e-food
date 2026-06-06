<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
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
