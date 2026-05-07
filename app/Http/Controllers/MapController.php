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

        $dayOfWeek = $validated['day_of_week'] ?? null;
        $startTime = $validated['start_time'] ?? null;
        $endTime = $validated['end_time'] ?? null;

        $markers = Restaurant::query()
            ->openInWindow($dayOfWeek, $startTime, $endTime)
            ->lazyById()
            ->map(function (Restaurant $restaurant): array {
                return [
                    'name' => $restaurant->data['name'],
                    'address' => $restaurant->data['address'],
                    'latitude' => $restaurant->data['mapLatitude'],
                    'longitude' => $restaurant->data['mapLongitude'],
                ];
            })
            ->values();

        return view('map', [
            'dayOfWeek' => $dayOfWeek,
            'startTime' => $startTime,
            'endTime' => $endTime,
            'markersJson' => $markers->toJson(),
        ]);
    }
}
