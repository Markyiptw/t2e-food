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

    public function test_hero_content_renders(): void
    {
        $response = $this->get('/');

        $response->assertSee('TIME', false);
        $response->assertSee('EAT', false);
        $response->assertSee('Food? At an hour like this?', false);
        $response->assertSee('still serving food', false);
    }

    public function test_form_renders(): void
    {
        $response = $this->get('/');

        $response->assertSee('for="start"', false);
        $response->assertSee('for="duration"', false);
        $response->assertSee('id="start"', false);
        $response->assertSee('id="duration"', false);
        $response->assertSee('02:00');
        $response->assertSee("I'm eating out at", false);
        $response->assertSee('Search on map', false);
        $response->assertSee('<form', false);
        $response->assertSee('method="GET"', false);
        $response->assertSee('action="/map"', false);
        $response->assertSee('name="start"', false);
        $response->assertSee('<option value="02:00" selected>02:00</option>', false);
        $response->assertSee('<option value="05:00">05:00</option>', false);
        $response->assertSee('<option value="09:00">09:00</option>', false);
        $response->assertSee('name="duration"', false);
        $response->assertSee('value="15"', false);
        $response->assertSee('minutes');
    }

    public function test_meta_tags_present(): void
    {
        $response = $this->get('/');

        $response->assertSee('name="description"', false);
        $response->assertSee('property="og:title"', false);
        $response->assertSee('property="og:description"', false);
        $response->assertSee('property="og:type"', false);
        $response->assertSee('name="twitter:card"', false);
    }

    public function test_export_link_exists(): void
    {
        $response = $this->get('/');

        $response->assertSee('download all data as CSV', false);
    }

    public function test_footer_renders(): void
    {
        $response = $this->get('/');

        $response->assertSee('TIME 2 EAT');
    }
}
