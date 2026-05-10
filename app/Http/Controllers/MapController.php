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

        dd($validated);

        // use the same date to make the result more "determinstic"
        // Carbon::createFromFormat('!H:i', $validated['start'] ?? null);

        // dump($validated);

        $start = isset($validated['start']) ? Carbon::createFromFormat('!H:i', $validated['start']) : null;
        $end = isset($validated['end']) ? Carbon::createFromFormat('!H:i', $validated['end']) : null;

        // $markers = collect();

        $markers = Restaurant::query()
            ->select([
                'data->name as name',
                'data->address as address',
                'data->mapLatitude as latitude',
                'data->mapLongitude as longitude',
                'data->shortenUrl as url',
            ])
            ->openInWindow($start, $end)
            // ->getQuery()
            // ->lazyById()
            ->get()
            ->map(fn (Restaurant $restaurant) => $restaurant->toArray())
            ->values();

        return view('map', [
            'start' => $start,
            'end' => $end,
            'markersJson' => $markers->toJson(),
        ]);
    }
}
