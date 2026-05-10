<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;

class MapController extends Controller
{
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'start_time' => 'nullable|string',
            'end_time' => 'nullable|string',
        ]);

        // dump($validated);

        $startTime = $validated['start_time'] ?? null;
        $endTime = $validated['end_time'] ?? null;

        // $markers = collect();

        $markers = Restaurant::query()
            ->select([
                'data->name as name',
                'data->address as address',
                'data->mapLatitude as latitude',
                'data->mapLongitude as longitude',
                'data->shortenUrl as url',
            ])
            ->openInWindow($startTime, $endTime)
            // ->getQuery()
            // ->lazyById()
            ->get()
            ->map(fn (Restaurant $restaurant) => $restaurant->toArray())
            ->values();

        return view('map', [
            'startTime' => $startTime,
            'endTime' => $endTime,
            'markersJson' => $markers->toJson(),
        ]);
    }
}
