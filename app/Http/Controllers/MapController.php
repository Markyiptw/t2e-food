<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class MapController extends Controller
{
    private const MARKERS_PER_PAGE = 1000;

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
        $restaurants = $this->markerPaginator($start, $duration, self::MARKERS_PER_PAGE)
            ->withPath(route('map.restaurants'))
            ->withQueryString();

        return view('map', [
            'start' => $start?->format('H:i'),
            'duration' => $duration,
            'markersEndpoint' => route('map.restaurants', array_filter([
                'start' => $start?->format('H:i'),
                'duration' => $duration,
            ], fn ($value) => $value !== null)),
            'markersJson' => json_encode($restaurants->items(), JSON_THROW_ON_ERROR),
            'nextMarkersPageUrl' => $restaurants->nextPageUrl(),
        ]);
    }

    public function restaurants(Request $request): JsonResponse
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
            'cursor' => [
                'nullable',
                'string',
            ],
            'limit' => [
                'nullable',
                'integer',
                'min:1',
                'max:'.self::MARKERS_PER_PAGE,
            ],
        ]);

        // use the same date, i.e. 1970-01-01, to make the result more "deterministic"
        $start = isset($validated['start']) ? Carbon::createFromFormat('!H:i', $validated['start']) : null;

        $duration = (int) ($validated['duration'] ?? null);
        $limit = (int) ($validated['limit'] ?? self::MARKERS_PER_PAGE);

        $restaurants = $this->markerPaginator($start, $duration, $limit)
            ->withQueryString();

        return response()->json([
            'markers' => $restaurants->items(),
            'next_page_url' => $restaurants->nextPageUrl(),
            'has_more' => $restaurants->hasMorePages(),
        ]);
    }

    private function markerPaginator(?Carbon $start, int $duration, int $limit): CursorPaginator
    {
        return Restaurant::query()
            ->active()
            ->hasLocation()
            ->when($start !== null, fn ($query) => $query->openInWindow($start, $duration))
            ->select([
                'id',
                'data->name as name',
                'data->address as address',
                'data->mapLatitude as latitude',
                'data->mapLongitude as longitude',
                'data->shortenUrl as url',
            ])
            ->orderBy('id')
            ->cursorPaginate($limit)
            ->through(fn (Restaurant $restaurant): array => $restaurant->toArray());
    }
}
