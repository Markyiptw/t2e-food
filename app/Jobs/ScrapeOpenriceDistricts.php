<?php

namespace App\Jobs;

use App\Models\District;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class ScrapeOpenriceDistricts implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $districts = Http::openrice()
            ->get('/metadata/region/all')
            ->collect('districts');

        Validator::validate($districts->all(), [
            '*.districtId' => ['required', 'numeric'],
            '*.districtGroupId' => ['nullable', 'numeric'],
        ]);

        $districts
            ->whereNull('districtGroupId')
            ->map(fn (array $district) => [
                'openrice_id' => $district['districtId'],
                'data' => collect($district)->except(['districtId'])->toJson(),
            ])
            ->values()
            ->all()
        |> (fn ($rows) => District::upsert($rows, ['openrice_id'], ['data']));

        $districts
            ->whereNotNull('districtGroupId')
            ->map(fn (array $district) => [
                'openrice_id' => $district['districtId'],
                'district_id' => District::where('openrice_id', $district['districtGroupId'])->first()?->id,
                'data' => collect($district)->except(['districtId'])->toJson(),
            ])
            ->values()
            ->all()
        |> (fn ($rows) => District::upsert($rows, ['openrice_id'], ['district_id', 'data']));

    }
}
