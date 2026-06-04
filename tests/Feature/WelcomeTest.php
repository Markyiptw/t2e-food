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

        $response->assertSee('TIME <span class="text-[#b32b22]">2</span> EAT', false);
        $response->assertSee('Will there be <span class="text-[#b32b22]">food</span>?', false);
        $response->assertSee("tell us your eta, and we'll tell you which Hong Kong kitchens are still firing the wok.", false);
    }

    public function test_order_slip_renders(): void
    {
        $response = $this->get('/');

        $response->assertSee('Time');
        $response->assertSee('02:00');
        $response->assertSee('Buffer');
        $response->assertSee('VIEW ON MAP');
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

    public function test_receipt_renders(): void
    {
        $response = $this->get('/');

        $response->assertSee('MENU');
        $response->assertSee('Data Source');
        $response->assertSee('you guess');
        $response->assertSee('Usefulness');
        $response->assertSee('quite');
        $response->assertSee('Vibe Check');
        $response->assertSee('pass');
        $response->assertSee('ADS');
        $response->assertSee('none');
        $response->assertSee('多謝 THANK YOU');
    }

    public function test_export_link_exists(): void
    {
        $response = $this->get('/');

        $response->assertSee('…or export for all the data nerds →', false);
    }

    public function test_open_source_band_renders(): void
    {
        $response = $this->get('/');

        $response->assertSee('A community project');
        $response->assertSee('Open source and free.');
        $response->assertSee('GITHUB');
        $response->assertSee('幫手');
        $response->assertSee('CONTRIBUTE');
    }

    public function test_jsx_style_self_closing_divs_are_not_rendered(): void
    {
        $response = $this->get('/');

        $response->assertDontSee('<div class="h-2 w-full bg-[repeating-linear-gradient(90deg,#b32b22_0_14px,#f4ecd8_14px_16px)] opacity-60" />', false);
    }
}
