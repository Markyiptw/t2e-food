<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;

class MapController extends Controller
{
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'day_of_week' => 'nullable|integer|min:1|max:7',
            'start_time' => 'nullable|string',
            'end_time' => 'nullable|string',
        ]);

        // dump($validated);

        $dayOfWeek = isset($validated['day_of_week']) ? (int) $validated['day_of_week'] : null;
        // $dayOfWeek = $validated['day_of_week'];
        $startTime = $validated['start_time'] ?? null;
        $endTime = $validated['end_time'] ?? null;

        // $markers = collect();

        $markers = Restaurant::query()
            ->select([
                'data->name as name',
                'data->address as address',
                'data->mapLatitude as latitude',
                'data->mapLongitude as longitude',
            ])
            ->openInWindow($dayOfWeek, $startTime, $endTime)
            // ->getQuery()
            // ->lazyById()
            ->get()
            ->map(fn (Restaurant $restaurant) => $restaurant->toArray())
            ->values();

        return view('map', [
            'dayOfWeek' => $dayOfWeek,
            'startTime' => $startTime,
            'endTime' => $endTime,
            'markersJson' => $markers->toJson(),
        ]);
    }
}
