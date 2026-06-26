<?php

namespace App\Services\Openrice;

use App\Models\Category;
use App\Models\District;
use App\Models\Hour;
use App\Models\Location;
use App\Models\Period;
use App\Models\Restaurant;
use App\Models\Status;
use Illuminate\Support\Collection;

final class SyncOpenriceRestaurant
{
    public function handle(OpenriceRestaurantData $data): Restaurant
    {
        $status = $this->syncStatus($data->status);
        $district = $this->syncDistrict($data->district);
        $model = $this->syncRestaurant($data, $status, $district);

        $this->syncLocation($model, $data->location);
        $this->syncCategories($model, $data->categories);
        $this->syncHours($model, $data->hours);

        return $model;
    }

    private function syncStatus(OpenriceStatusData $status): Status
    {
        return Status::firstOrCreate([
            'code' => $status->code,
            'text' => $status->text,
        ]);
    }

    private function syncDistrict(?OpenriceDistrictData $district): ?District
    {
        if ($district === null) {
            return null;
        }

        return District::firstOrCreate(
            ['external_id' => $district->externalId],
            ['name' => $district->name],
        );
    }

    private function syncRestaurant(OpenriceRestaurantData $data, Status $status, ?District $district): Restaurant
    {
        return Restaurant::withoutGlobalScope('active')->updateOrCreate(
            ['poi_id' => $data->poiId],
            [
                'name' => $data->name,
                'url' => $data->url,
                'status_id' => $status->id,
                'address' => $data->address,
                'district_id' => $district?->id,
            ],
        );
    }

    private function syncLocation(Restaurant $model, ?OpenriceLocationData $location): void
    {
        if ($location !== null) {
            Location::updateOrCreate(
                ['restaurant_id' => $model->id],
                ['latitude' => $location->latitude, 'longitude' => $location->longitude],
            );

            return;
        }

        $model->location()->delete();
    }

    /**
     * @param  Collection<int, OpenriceCategoryData>  $categories
     */
    private function syncCategories(Restaurant $model, Collection $categories): void
    {
        $categoryIds = $categories
            ->map(fn (OpenriceCategoryData $category): int => Category::firstOrCreate(
                ['name' => $category->name],
            )->id)
            ->all();

        $model->categories()->sync($categoryIds);
    }

    /**
     * @param  Collection<int, OpenriceHourData>  $hours
     */
    private function syncHours(Restaurant $model, Collection $hours): void
    {
        Hour::query()
            ->where('restaurant_id', $model->id)
            ->delete();

        $hours->each(function (OpenriceHourData $hour) use ($model): void {
            $hourModel = Hour::create([
                'restaurant_id' => $model->id,
                'day_of_week' => $hour->dayOfWeek,
                'is_close' => $hour->isClose,
                'is_24hr' => $hour->is24Hr,
            ]);

            $hour->periods->each(fn (OpenricePeriodData $period) => Period::create([
                'position' => $period->position,
                'start' => $period->start,
                'end' => $period->end,
                'hour_id' => $hourModel->id,
            ]));
        });
    }
}
