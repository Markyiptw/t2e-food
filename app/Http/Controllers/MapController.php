<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MapController extends Controller
{
    public function __invoke(Request $request)
    {
        $dayOfWeek = $request->integer('day_of_week');
        $startTime = $request->string('start_time')->trim()->value() ?: null;
        $endTime = $request->string('end_time')->trim()->value() ?: null;

        $filterActive = $dayOfWeek > 0 && $startTime !== null && $endTime !== null;

        $query = Restaurant::whereNotNull(DB::raw('NULLIF(latitude, 0)'))
            ->whereNotNull(DB::raw('NULLIF(longitude, 0)'));

        if ($filterActive) {
            $query->openInWindow($dayOfWeek, $startTime, $endTime);
        }

        $markers = $query->get()
            ->map(function (Restaurant $restaurant): array {
                return [
                    'name' => $restaurant->getName(),
                    'address' => $restaurant->getAddress(),
                    'latitude' => $restaurant->getLatitude(),
                    'longitude' => $restaurant->getLongitude(),
                ];
            })
            ->values();

        return view('map', [
            'dayOfWeek' => $dayOfWeek,
            'startTime' => $startTime,
            'endTime' => $endTime,
            'markersJson' => json_encode($markers),
        ]);
    }
}
