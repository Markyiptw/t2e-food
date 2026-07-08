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
    <body class="flex h-screen flex-col overflow-hidden bg-white font-sans text-slate-800 antialiased">
        <header class="flex-none border-b border-slate-200 bg-white">
            <div class="mx-auto flex max-w-5xl items-center justify-between px-6 py-4">
                <a href="/" class="text-lg font-bold tracking-wide text-slate-800">
                    TIME 2 EAT
                </a>
                <nav class="flex items-center gap-6 text-sm">
                    <a
                        href="/map"
                        @class(['underline' => request()->routeIs('map'), 'text-slate-900' => request()->routeIs('map'), 'text-slate-500' => ! request()->routeIs('map'), 'hover:text-slate-700' => ! request()->routeIs('map')])
                    >
                        Map
                    </a>
                    <a
                        href="/export"
                        @class(['underline' => request()->is('export'), 'text-slate-900' => request()->is('export'), 'text-slate-500' => ! request()->is('export'), 'hover:text-slate-700' => ! request()->is('export')])
                    >
                        Export
                    </a>
                </nav>
            </div>
        </header>

        <div class="flex min-h-0 flex-1 flex-col overflow-y-auto">
            {{ $slot }}
        </div>
    </body>
</html>
