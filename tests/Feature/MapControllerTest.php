<?php

namespace Tests\Feature;

use App\Models\Hour;
use App\Models\Period;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class MapControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.key' => 'base64:'.base64_encode(str_repeat('a', 32))]);
    }

    public function test_map_filters_by_start_and_duration(): void
    {
        $valid = Restaurant::factory()
            ->has(Hour::factory()->has(Period::factory()->state(['start' => '09:00:00', 'end' => '17:00:00'])))
            ->create([
                'data' => [
                    'status' => 10,
                    'name' => 'Open Restaurant',
                    'address' => '1 Test Street',
                    'mapLatitude' => 22.3,
                    'mapLongitude' => 114.1,
                ],
            ]);

        Restaurant::factory()
            ->has(Hour::factory()->has(Period::factory()->state(['start' => '10:00:00', 'end' => '14:00:00'])))
            ->create();

        $response = $this->get('/map?start=10:00&duration=360');

        $response
            ->assertOk()
            ->assertViewHas('start', '10:00')
            ->assertViewHas('duration', 360);

        $markers = json_decode($response->viewData('markersJson'), true, flags: JSON_THROW_ON_ERROR);

        $this->assertCount(1, $markers);
        $this->assertSame($valid->data['name'], $markers[0]['name']);
    }

    public function test_map_page_renders_with_layout_and_bottom_sheet(): void
    {
        $response = $this->get('/map');

        $response
            ->assertOk()
            ->assertSee('id="map"', false)
            ->assertSee('id="filter-sheet"', false)
            ->assertSee('id="filter-toggle"', false)
            ->assertSee('id="filter-backdrop"', false)
            ->assertSee('name="description"', false)
            ->assertSee('action="/map"', false)
            ->assertSee('name="start"', false)
            ->assertSee('name="duration"', false)
            ->assertSee('window.restaurantMarkers', false);
    }
}
