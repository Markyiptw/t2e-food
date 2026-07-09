<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class LanguagePickerTest extends TestCase
{
    public function test_picker_renders_on_welcome_page(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('aria-label="Language"', false);
    }

    public function test_picker_renders_on_map_page(): void
    {
        $response = $this->get('/map');

        $response->assertOk();
        $response->assertSee('aria-label="Language"', false);
    }

    public function test_english_locale_is_active_by_default(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $this->assertMatchesRegularExpression('/aria-current="true"[^>]*>\s*EN\s*<\/span/', $response->getContent());
        $response->assertSee('value="zh-HK"', false);
        $response->assertSee('action="'.route('locale.update').'"', false);
    }

    public function test_chinese_locale_is_active_when_cookie_set(): void
    {
        $response = $this->withCookie('locale', 'zh-HK')->get('/');

        $response->assertOk();
        $this->assertMatchesRegularExpression('/aria-current="true"[^>]*>\s*繁\s*<\/span/', $response->getContent());
        $response->assertSee('value="en"', false);
        $response->assertSee('action="'.route('locale.update').'"', false);
    }
}
