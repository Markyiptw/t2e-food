@props(['title' => 'Time 2 Eat', 'description' => 'Find Hong Kong kitchens still serving food. Night shifts, late hangs, early mornings — whatever keeps you out.'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <title>{{ $title ?? 'Time 2 Eat' }}</title>

        <meta
            name="description"
            content="{{ $description }}"
        />
        <meta property="og:title" content="{{ $title ?? 'Time 2 Eat' }}" />
        <meta
            property="og:description"
            content="{{ $description }}"
        />
        <meta property="og:type" content="website" />
        <meta name="twitter:card" content="summary" />

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="min-h-screen bg-white font-sans text-slate-800 antialiased">
        {{ $slot }}
    </body>
</html>
