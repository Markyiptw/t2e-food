<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class WelcomeTest extends TestCase
{
    public function test_welcome_page_loads(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_search_form_renders_with_correct_action_and_method(): void
    {
        $response = $this->get('/');

        $response->assertSee('action="/map"', false);
        $response->assertSee('method="GET"', false);
    }

    public function test_time_dropdown_has_four_options_with_correct_default(): void
    {
        $response = $this->get('/');

        $response->assertSee('value="08:00">08:00', false);
        $response->assertSee('value="12:00">12:00', false);
        $response->assertSee('value="20:00">20:00', false);
        $response->assertSee('value="02:00" selected>02:00', false);
    }

    public function test_duration_input_defaults_to_fifteen(): void
    {
        $response = $this->get('/');

        $response->assertSee('name="duration"', false);
        $response->assertSee('value="15"', false);
    }

    public function test_explore_map_button_exists(): void
    {
        $response = $this->get('/');

        $response->assertSee('Explore the Map', false);
    }

    public function test_export_link_exists(): void
    {
        $response = $this->get('/');

        $response->assertSee('export for all the data nerds', false);
    }

    public function test_trust_section_renders(): void
    {
        $response = $this->get('/');

        $response->assertSee('Most apps show you what&rsquo;s nearby. We show you what&rsquo;s actually open.', false);
        $response->assertSee('Real coverage, updated regularly.', false);
        $response->assertSee('No accounts, no ads, no tracking.', false);
        $response->assertSee('For researchers, too.', false);
    }

    public function test_no_openrice_credit_is_present(): void
    {
        $response = $this->get('/');

        $response->assertDontSee('OpenRice');
    }
}
