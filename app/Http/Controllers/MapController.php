<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class MapController extends Controller
{
    public function __invoke(Request $request): View
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

        // use the same date, i.e. 1970-01-01, to make the result more "deterministic"
        $start = isset($validated['start']) ? Carbon::createFromFormat('!H:i', $validated['start']) : null;

        $duration = (int) ($validated['duration'] ?? null);

        return view('map', [
            'start' => $start?->format('H:i'),
            'duration' => $duration,
            'markersEndpoint' => route('map.restaurants', array_filter([
                'start' => $start?->format('H:i'),
                'duration' => $duration,
            ], fn ($value) => $value !== null)),
        ]);
    }
}
