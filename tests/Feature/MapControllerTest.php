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

    public function test_map_restaurants_endpoint_filters_by_start_and_duration(): void
    {
        $valid = Restaurant::factory()
            ->has(Hour::factory()->has(Period::factory()->state(['start' => '09:00:00', 'end' => '17:00:00'])))
            ->create([
                'name' => 'Open Restaurant',
                'address' => '1 Test Street',
            ]);

        Restaurant::factory()
            ->has(Hour::factory()->has(Period::factory()->state(['start' => '10:00:00', 'end' => '14:00:00'])))
            ->create();

        $response = $this->getJson('/map/restaurants?start=10:00&duration=360');

        $response->assertOk();

        $markers = $response->json('markers');

        $this->assertCount(1, $markers);
        $this->assertSame($valid->name, $markers[0]['name']);
    }

    public function test_map_restaurants_endpoint_cursor_paginates_markers(): void
    {
        $restaurants = collect([
            'First Restaurant',
            'Second Restaurant',
            'Third Restaurant',
        ])->map(fn (string $name) => Restaurant::factory()->create([
            'name' => $name,
            'address' => '1 Test Street',
        ]));

        $firstPage = $this->getJson('/map/restaurants?limit=1')
            ->assertOk()
            ->assertJsonPath('has_more', true);

        $this->assertSame($restaurants[0]->id, $firstPage->json('markers.0.id'));
        $this->assertNotNull($firstPage->json('next_page_url'));

        $secondPage = $this->getJson($firstPage->json('next_page_url'))
            ->assertOk()
            ->assertJsonPath('has_more', true);

        $this->assertSame($restaurants[1]->id, $secondPage->json('markers.0.id'));
    }

    public function test_map_page_renders_with_map_and_filter_panel(): void
    {
        $response = $this->get('/map');

        $response
            ->assertOk()
            ->assertSee('id="map"', false)
            ->assertSee('name="description"', false)
            ->assertSee('action="/map"', false)
            ->assertSee('name="start"', false)
            ->assertSee('name="duration"', false)
            ->assertSee('window.restaurantMarkersEndpoint', false)
            ->assertDontSee('window.restaurantMarkers =', false)
            ->assertViewHas('markersEndpoint')
            ->assertViewMissing('markersJson');
    }
}
