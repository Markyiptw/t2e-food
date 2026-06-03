@props(['title' => 'Time 2 Eat'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <title>{{ $title ?? 'Time 2 Eat' }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net" />
        <link
            href="https://fonts.bunny.net/css?family=outfit:300,400,500,600,700,800|space-mono:400,400i,700,700i"
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
        class="flex min-h-screen flex-col items-center font-sans antialiased"
    >
        {{ $slot }}
    </body>
</html>
