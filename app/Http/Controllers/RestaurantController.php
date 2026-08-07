<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class RestaurantController extends Controller
{
    private const MARKERS_PER_PAGE = 1000;

    public function index(Request $request): JsonResponse
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

        $restaurants = Restaurant::query()
            ->active()
            ->hasLocation()
            ->when($start !== null, fn ($query) => $query->openInWindow($start, $duration))
            ->with('categories:id,name', 'location')
            ->select([
                'restaurants.id',
                'restaurants.name',
                'restaurants.address',
                'restaurants.url',
            ])
            ->orderBy('restaurants.id')
            ->cursorPaginate($limit)
            ->through(fn (Restaurant $restaurant): array => [
                'id' => $restaurant->id,
                'name' => $restaurant->name,
                'address' => $restaurant->address,
                'latitude' => $restaurant->location->latitude,
                'longitude' => $restaurant->location->longitude,
                'url' => $restaurant->url,
                'categories' => $restaurant->categories->pluck('name')->values()->all(),
            ])
            ->withQueryString();

        return response()->json([
            'markers' => $restaurants->items(),
            'next_page_url' => $restaurants->nextPageUrl(),
            'has_more' => $restaurants->hasMorePages(),
        ]);
    }
}
