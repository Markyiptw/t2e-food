<?php

namespace App\Http\Controllers;

use App\Models\Hour;
use App\Models\Restaurant;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Spatie\SimpleExcel\SimpleExcelWriter;

class ExportController extends Controller
{
    private const array HEADERS = [
        'name',
        'district',
        'address',
        'coordinates',
        'opening_hours',
        'categories',
    ];

    public function index(Request $request): View
    {
        [$start, $end] = $this->validatedWindow($request);

        return view('export', [
            'start' => $start?->format('H:i'),
            'end' => $end?->format('H:i'),
        ]);
    }

    public function store(Request $request)
    {
        [$start, $end] = $this->validatedWindow($request);

        $writer = SimpleExcelWriter::streamDownload('test.csv')
            ->addHeader(self::HEADERS);

        Restaurant::query()
            ->active()
            ->when($start !== null && $end !== null, fn (Builder $query) => $query->openInAnyWindowBetween($start, $end))
            ->with([
                'district',
                'location',
                'categories',
                'hours' => fn ($query) => $query
                    ->with([
                        'periods' => fn ($query) => $query
                            ->orderBy('position'),
                    ])
                    ->where('day_of_week', '>', 0)
                    ->orderBy('day_of_week')
                    ->orderBy('id'),
            ])
            ->chunkById(500,
                function ($restaurants) use ($writer) {
                    $restaurants
                        ->map(fn (Restaurant $restaurant) => [
                            $restaurant->name ?? '',
                            $restaurant->district?->name ?? '',
                            $restaurant->address ?? '',
                            $restaurant->location
                                ? $restaurant->location->latitude.', '.$restaurant->location->longitude
                                : '',
                            $restaurant
                                ->hours
                                ->map(fn (Hour $hour) => "$hour->day_of_week: $hour->human_readable_period")
                                ->implode('; '),
                            $restaurant
                                ->categories
                                ->pluck('name')
                                ->implode(', '),
                        ])
                        ->each(fn ($row) => $writer->addRow($row));
                    flush();
                }
            );

        $writer->toBrowser();
    }

    /**
     * @return array{0: Carbon|null, 1: Carbon|null}
     */
    private function validatedWindow(Request $request): array
    {
        $validated = $request->validate([
            'start' => ['required_with:end', 'nullable', 'string', Rule::date()->format('H:i')],
            'end' => ['required_with:start', 'nullable', 'string', Rule::date()->format('H:i')],
        ]);

        return [
            isset($validated['start']) ? Carbon::createFromFormat('!H:i', $validated['start']) : null,
            isset($validated['end']) ? Carbon::createFromFormat('!H:i', $validated['end']) : null,
        ];
    }
}
