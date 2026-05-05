<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Restaurant extends Model
{
    protected function casts(): array
    {
        return [
            'data' => 'array',
            'query_params' => 'array',
        ];
    }

    public function hours(): HasMany
    {
        return $this->hasMany(RestaurantHour::class);
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
    }

    public function scopeOpenInWindow(Builder $query, int $dayOfWeek, string $startTime, string $endTime): void
    {
        $query->whereHas('hours', function (Builder $query) use ($dayOfWeek, $startTime, $endTime): void {
            $query->where('day_of_week', $dayOfWeek)
                ->where('weight', 0)
                ->where('is_close', false)
                ->where(function (Builder $query) use ($startTime, $endTime): void {
                    $query->where('is_24hr', true)
                        ->orWhere(function (Builder $query) use ($startTime, $endTime): void {
                            $query->whereNotNull('period_1_start')
                                ->whereNotNull('period_1_end')
                                ->where('period_1_start', '<=', $startTime)
                                ->where('period_1_end', '>=', $endTime);
                        })
                        ->orWhere(function (Builder $query) use ($startTime, $endTime): void {
                            $query->whereNotNull('period_2_start')
                                ->whereNotNull('period_2_end')
                                ->where('period_2_start', '<=', $startTime)
                                ->where('period_2_end', '>=', $endTime);
                        })
                        ->orWhere(function (Builder $query) use ($startTime, $endTime): void {
                            $query->whereNotNull('period_3_start')
                                ->whereNotNull('period_3_end')
                                ->where('period_3_start', '<=', $startTime)
                                ->where('period_3_end', '>=', $endTime);
                        });
                });
        });
    }

    public function isOpenInWindow(int $dayOfWeek, string $startTime, string $endTime): bool
    {
        foreach ($this->getPoiHours() as $hours) {
            if (! is_array($hours)) {
                continue;
            }

            if (($hours['dayOfWeek'] ?? null) !== $dayOfWeek) {
                continue;
            }

            if (($hours['weight'] ?? 0) !== 0) {
                continue;
            }

            if (($hours['isClose'] ?? false) === true) {
                continue;
            }

            if (($hours['is24hr'] ?? false) === true) {
                return true;
            }

            foreach ([1, 2, 3] as $period) {
                $periodStart = $hours["period{$period}Start"] ?? null;
                $periodEnd = $hours["period{$period}End"] ?? null;

                if (! is_string($periodStart) || $periodStart === '' || ! is_string($periodEnd) || $periodEnd === '') {
                    continue;
                }

                $periodStart = substr($periodStart, 0, 5);
                $periodEnd = substr($periodEnd, 0, 5);

                if ($periodStart <= $startTime && $periodEnd >= $endTime) {
                    return true;
                }
            }
        }

        return false;
    }
}
