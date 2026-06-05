@props(['title' => 'Time 2 Eat'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <title>{{ $title ?? 'Time 2 Eat' }}</title>

        <meta
            name="description"
            content="Tell us your ETA and we'll tell you which Hong Kong kitchens are still firing the wok."
        />
        <meta property="og:title" content="{{ $title ?? 'Time 2 Eat' }}" />
        <meta
            property="og:description"
            content="Tell us your ETA and we'll tell you which Hong Kong kitchens are still firing the wok."
        />
        <meta property="og:type" content="website" />
        <meta name="twitter:card" content="summary" />

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link
            href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=IM+Fell+English:ital@0;1&family=Noto+Serif+HK:wght@300..900&family=Oswald:wght@400..700&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap"
            rel="stylesheet"
        />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                /* Fallback: critical CSS only */
                body {
                    font-family: 'Outfit', ui-sans-serif, system-ui, sans-serif;
                    background: linear-gradient(165deg, #F7F4EF 0%, #E5DFED 100%);
                    color: #1C1625;
                    min-height: 100vh;
                    margin: 0;
                }
            </style>
        @endif
    </head>
    <body
        hx-ext="response-targets"
        hx-target-error="this"
        class="min-h-screen font-sans antialiased"
    >
        {{ $slot }}
    </body>
</html>
