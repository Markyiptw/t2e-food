<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    private const array HEADERS = [
        'name',
        'district',
        'address',
        'opening_hours',
        'categories',
    ];

    private const array DAY_OF_WEEK_LABELS = [
        1 => 'Sun',
        2 => 'Mon',
        3 => 'Tue',
        4 => 'Wed',
        5 => 'Thu',
        6 => 'Fri',
        7 => 'Sat',
    ];

    public function index(Request $request): View
    {
        [$start, $end] = $this->validatedWindow($request);

        return view('export', [
            'start' => $start?->format('H:i'),
            'end' => $end?->format('H:i'),
            'restaurantCount' => $this->restaurants($start, $end)->count(),
        ]);
    }

    public function store(Request $request): BinaryFileResponse
    {
        [$start, $end] = $this->validatedWindow($request);

        $path = $this->exportPath();
        $writer = SimpleExcelWriter::create($path)->addHeader(self::HEADERS);

        $this->restaurants($start, $end)
            ->with([
                'hours' => fn ($query) => $query
                    ->select(['id', 'restaurant_id', 'data'])
                    ->with(['periods' => fn ($query) => $query
                        ->select(['id', 'hour_id', 'position', 'start', 'end'])
                        ->orderBy('position')])
                    ->orderByRaw('(data->>\'dayOfWeek\')::int nulls last')
                    ->orderBy('id'),
            ])
            ->select(['id', 'data'])
            ->orderBy('id')
            ->chunkById(500, function ($restaurants) use ($writer): void {
                foreach ($restaurants as $restaurant) {
                    $writer->addRow($this->exportRow($restaurant));
                }
            });

        $writer->close();

        return response()
            ->download($path, 'restaurants.xlsx')
            ->deleteFileAfterSend();
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

    private function restaurants(?Carbon $start, ?Carbon $end): Builder
    {
        return Restaurant::query()
            ->when($start !== null && $end !== null, fn (Builder $query) => $query->openInAnyWindowBetween($start, $end));
    }

    private function exportPath(): string
    {
        $disk = Storage::disk('local');
        $disk->makeDirectory('exports');

        return $disk->path('exports/restaurants-'.Str::uuid().'.xlsx');
    }

    /**
     * @return array<int, string>
     */
    private function exportRow(Restaurant $restaurant): array
    {
        $data = $restaurant->data ?? [];

        return [
            $this->stringData($data, 'name'),
            $this->stringData($data, 'district.name'),
            $this->stringData($data, 'address'),
            $this->formatOpeningHours($restaurant),
            $this->formatCategories($data),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function stringData(array $data, string $key): string
    {
        $value = data_get($data, $key);

        return is_scalar($value) ? (string) $value : '';
    }

    private function formatOpeningHours(Restaurant $restaurant): string
    {
        return $restaurant->hours
            ->filter(fn ($hour): bool => is_array($hour->data) && (int) ($hour->data['weight'] ?? -1) === 0)
            ->filter(fn ($hour): bool => array_key_exists((int) ($hour->data['dayOfWeek'] ?? 0), self::DAY_OF_WEEK_LABELS))
            ->map(fn ($hour): string => $this->formatHour($hour))
            ->implode('; ');
    }

    private function formatHour($hour): string
    {
        $label = self::DAY_OF_WEEK_LABELS[(int) $hour->data['dayOfWeek']];

        if (($hour->data['isClose'] ?? false) === true) {
            return $label.': closed';
        }

        if (($hour->data['is24hr'] ?? false) === true) {
            return $label.': 24 hours';
        }

        $periods = $hour->periods
            ->map(fn ($period): string => $this->time($period->start).'-'.$this->time($period->end))
            ->implode(', ');

        return $label.': '.$periods;
    }

    private function time(mixed $time): string
    {
        return Str::of((string) $time)->substr(0, 5)->value();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function formatCategories(array $data): string
    {
        return collect($data['categories'] ?? [])
            ->pluck('name')
            ->filter(fn (mixed $name): bool => is_scalar($name) && $name !== '')
            ->implode(', ');
    }
}
