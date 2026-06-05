<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class MapController extends Controller
{
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'start' => [
                'nullable',
                'string',
                Rule::date()->format('H:i'),
            ],
            'duration' => [
                'nullable',
                'integer',
                'min:0',
            ],
        ]);

        // use the same date, i.e. 1970-01-01, to make the result more "determinstic"

        $start = isset($validated['start']) ? Carbon::createFromFormat('!H:i', $validated['start']) : null;

        $duration = (int) ($validated['duration'] ?? null);
        $restaurants = Restaurant::query()
            ->select([
                'data->name as name',
                'data->address as address',
                'data->mapLatitude as latitude',
                'data->mapLongitude as longitude',
                'data->shortenUrl as url',
            ]);

        if ($start !== null) {
            $restaurants->openInWindow($start, $duration);
        }

        $markers = $restaurants
            ->get()
            ->map(fn (Restaurant $restaurant) => $restaurant->toArray())
            ->values();

        return view('map', [
            'start' => $start?->format('H:i'),
            'duration' => $duration,
            'markersJson' => $markers->toJson(),
        ]);
    }
}
