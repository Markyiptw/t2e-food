<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $browserLocale = $request->getPreferredLanguage(['en', 'zh-HK', 'zh-TW', 'zh-Hant'])
        |> (fn ($language) => Str::kebab($language))
        |> (fn ($language) => match ($language) {
            'zh-hk', 'zh-tw', 'zh-hant' => 'zh-HK',
            default => 'en',
        });

        $cookieLocale = $request->cookie('locale')
        |> (fn ($cookie) => Validator::make(['locale' => $cookie], ['locale' => 'required|in:en,zh-HK'])->passes() ? $cookie : null);

        $locale = $cookieLocale ?? $browserLocale;

        App::setLocale($locale);

        return $next($request);
    }
}
