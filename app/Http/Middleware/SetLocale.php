<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->resolveLocale($request);

        App::setLocale($locale);

        return $next($request);
    }

    private function resolveLocale(Request $request): string
    {
        $supported = ['en', 'zh-HK'];

        $cookieLocale = $request->cookie('locale');

        if ($cookieLocale && in_array($cookieLocale, $supported, true)) {
            return $cookieLocale;
        }

        $headerLocale = $request->getPreferredLanguage(['en', 'zh-HK', 'zh-TW', 'zh-Hant']);
        $normalized = str_replace('_', '-', $headerLocale ?? '');

        if (in_array($normalized, ['zh-HK', 'zh-TW', 'zh-Hant'], true)) {
            return 'zh-HK';
        }

        return 'en';
    }
}
