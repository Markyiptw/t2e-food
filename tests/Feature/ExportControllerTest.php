<?php

namespace Tests\Feature;

use App\Models\Hour;
use App\Models\Period;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Spatie\SimpleExcel\SimpleExcelReader;
use Tests\TestCase;

class ExportControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function tearDown(): void
    {
        Storage::disk('local')->deleteDirectory('exports');

        parent::tearDown();
    }

    public function test_export_page_renders_filter_and_download_form(): void
    {
        $response = $this->get('/export?start=10:00&end=14:00');

        $response
            ->assertOk()
            ->assertSee('action="/export"', false)
            ->assertSee('method="GET"', false)
            ->assertSee('method="POST"', false)
            ->assertSee('name="start"', false)
            ->assertSee('name="end"', false)
            ->assertSee('Download as XLSX');
    }

    public function test_export_downloads_filtered_restaurants_as_xlsx(): void
    {
        $matching = $this->restaurantWithPeriod('Dim Sum Lab', 'Central', '1 Test Street', ['Cantonese', 'Noodles'], '09:00:00', '17:00:00');
        $this->restaurantWithPeriod('Dinner Only', 'Wan Chai', '2 Test Street', ['Hot Pot'], '18:00:00', '22:00:00');

        $response = $this->post('/export', [
            'start' => '10:00',
            'end' => '11:00',
        ]);

        $response->assertDownload('restaurants.xlsx');

        $reader = SimpleExcelReader::create($response->baseResponse->getFile()->getPathname())
            ->preserveDateTimeFormatting();

        $rows = $reader
            ->getRows()
            ->values()
            ->all();

        $this->assertSame(['name', 'district', 'address', 'opening_hours', 'categories'], $reader->getHeaders());
        $this->assertSame([
            [
                'name' => $matching->data['name'],
                'district' => 'Central',
                'address' => '1 Test Street',
                'opening_hours' => 'Sun: 09:00-17:00',
                'categories' => 'Cantonese, Noodles',
            ],
        ], $rows);
    }

    /**
     * @param  array<int, string>  $categories
     */
    private function restaurantWithPeriod(
        string $name,
        string $district,
        string $address,
        array $categories,
        string $start,
        string $end,
    ): Restaurant {
        return Restaurant::factory()
            ->has(Hour::factory()->dayOfWeek(1)->has(Period::factory()->state([
                'start' => $start,
                'end' => $end,
            ])))
            ->create([
                'data' => [
                    'status' => 10,
                    'mapLatitude' => 22.3,
                    'mapLongitude' => 114.1,
                    'name' => $name,
                    'district' => ['name' => $district],
                    'address' => $address,
                    'categories' => collect($categories)
                        ->map(fn (string $category): array => ['name' => $category])
                        ->all(),
                ],
            ]);
    }
}
