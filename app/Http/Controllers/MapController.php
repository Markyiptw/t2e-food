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
            'end' => [
                'nullable',
                'string',
                Rule::date()->format('H:i'),
            ],
        ]);

        // use the same date, i.e. 1970-01-01, to make the result more "determinstic"

        $start = isset($validated['start']) ? Carbon::createFromFormat('!H:i', $validated['start']) : null;
        $end = isset($validated['end']) ? Carbon::createFromFormat('!H:i', $validated['end']) : null;

        $markers = Restaurant::query()
            ->select([
                'data->name as name',
                'data->address as address',
                'data->mapLatitude as latitude',
                'data->mapLongitude as longitude',
                'data->shortenUrl as url',
            ])
            ->openInWindow($start, $end)
            ->get()
            ->map(fn (Restaurant $restaurant) => $restaurant->toArray())
            ->values();

        return view('map', [
            'start' => $start?->format('H:i'),
            'end' => $end?->format('H:i'),
            'markersJson' => $markers->toJson(),
        ]);
    }
}
