<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class LocaleTest extends TestCase
{
    public function test_locale_endpoint_sets_cookie(): void
    {
        $response = $this->post('/locale', ['locale' => 'zh-HK']);

        $response->assertRedirect();
        $response->assertCookie('locale', 'zh-HK');
    }

    public function test_locale_endpoint_rejects_invalid_locale(): void
    {
        $response = $this->post('/locale', ['locale' => 'fr']);

        $response->assertSessionHasErrors('locale');
    }

    public function test_locale_endpoint_accepts_english(): void
    {
        $response = $this->post('/locale', ['locale' => 'en']);

        $response->assertRedirect();
        $response->assertCookie('locale', 'en');
    }

    public function test_middleware_defaults_to_english(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $this->assertEquals('en', app()->getLocale());
    }

    public function test_middleware_uses_cookie_preference(): void
    {
        $response = $this->withCookie('locale', 'zh-HK')->get('/');

        $response->assertStatus(200);
        $this->assertEquals('zh-HK', app()->getLocale());
    }

    public function test_middleware_detects_zh_hk_browser_header(): void
    {
        $response = $this->withHeaders([
            'Accept-Language' => 'zh-HK,zh;q=0.9,en;q=0.8',
        ])->get('/');

        $response->assertStatus(200);
        $this->assertEquals('zh-HK', app()->getLocale());
    }

    public function test_middleware_detects_zh_tw_browser_header(): void
    {
        $response = $this->withHeaders([
            'Accept-Language' => 'zh-TW,zh;q=0.9,en;q=0.8',
        ])->get('/');

        $response->assertStatus(200);
        $this->assertEquals('zh-HK', app()->getLocale());
    }

    public function test_middleware_falls_back_to_english_for_unsupported_header(): void
    {
        $response = $this->withHeaders([
            'Accept-Language' => 'ja;q=0.9,fr;q=0.8',
        ])->get('/');

        $response->assertStatus(200);
        $this->assertEquals('en', app()->getLocale());
    }

    public function test_cookie_takes_precedence_over_header(): void
    {
        $response = $this->withCookie('locale', 'en')
            ->withHeaders(['Accept-Language' => 'zh-HK,zh;q=0.9'])
            ->get('/');

        $response->assertStatus(200);
        $this->assertEquals('en', app()->getLocale());
    }
}
