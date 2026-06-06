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

        $writer = SimpleExcelWriter::streamDownload('test.xlsx')
            ->addHeader(self::HEADERS);

        // $path = $this->exportPath();
        // $writer = SimpleExcelWriter::create($path)
        // ->addHeader(self::HEADERS);

        Restaurant::query()
            ->when($start !== null && $end !== null, fn (Builder $query) => $query->openInAnyWindowBetween($start, $end))
            ->with([
                'hours' => fn ($query) => $query
                    ->with([
                        'periods' => fn ($query) => $query
                            ->orderBy('position'),
                    ])
                    ->orderByRaw('(data->>\'dayOfWeek\')::int nulls last')
                    ->orderBy('id'),
            ])
            ->chunkById(500, function ($restaurants) use ($writer): void {
                foreach ($restaurants as $restaurant) {
                    $writer
                        ->addRow([
                            $restaurant->data['name'] ?? '',
                            $restaurant->data['district']['name'] ?? '',
                            $restaurant->data['address'] ?? '',
                            $restaurant
                                ->hours
                                ->map(fn (Hour $hour) => "$hour->day_of_week: $hour->human_readable_period")
                                ->implode('; '),
                            collect($restaurant->data['categories'])
                                ->pluck('name')
                                ->implode(', '),
                        ]);
                }
                flush();
            });

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
