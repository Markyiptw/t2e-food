<x-layout title="Restaurant Map">
    <div class="flex h-screen flex-col">
        {{-- Header --}}
        <header class="border-b border-slate-200 bg-white">
            <div class="mx-auto flex max-w-5xl items-center justify-between px-6 py-4">
                <a href="/" class="text-lg font-bold tracking-wide text-slate-800">
                    TIME 2 EAT
                </a>
                <a href="/" class="text-sm text-slate-500 underline hover:text-slate-700">
                    Back
                </a>
            </div>
        </header>

        {{-- Map container --}}
        <div class="relative flex-1">
            <div id="map" class="h-full w-full"></div>

            {{-- Filter panel --}}
            <form
                method="GET"
                action="/map"
                class="absolute top-4 left-4 z-[1000] w-72 rounded border border-slate-200 bg-white p-4 shadow-sm"
            >
                <h2 class="mb-3 text-sm font-semibold text-slate-700">
                    Filter by opening hours
                </h2>

                <div class="mb-3">
                    <label for="start" class="mb-1 block text-xs font-medium text-slate-600">
                        Eating out at
                    </label>
                    <input
                        id="start"
                        type="time"
                        name="start"
                        value="{{ $start }}"
                        required
                        class="block w-full rounded border-slate-300 text-sm text-slate-800 shadow-sm focus:border-slate-500 focus:ring-slate-500"
                    />
                </div>

                <div class="mb-4">
                    <label for="duration" class="mb-1 block text-xs font-medium text-slate-600">
                        for (min)
                    </label>
                    <div class="flex items-center gap-2">
                        <input
                            id="duration"
                            type="number"
                            name="duration"
                            value="{{ $duration }}"
                            min="0"
                            step="1"
                            placeholder="0"
                            class="block w-20 rounded border-slate-300 text-sm text-slate-800 shadow-sm focus:border-slate-500 focus:ring-slate-500"
                        />
                        <span class="text-xs text-slate-500">min</span>
                    </div>
                </div>

                <button
                    type="submit"
                    class="w-full cursor-pointer rounded bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700"
                >
                    Filter
                </button>

                @if ($start !== null || $duration !== null)
                    <a
                        href="/map"
                        class="mt-2 block w-full rounded border border-slate-200 py-2 text-center text-sm text-slate-600 hover:bg-slate-50"
                    >
                        Clear filters
                    </a>
                @endif
            </form>
        </div>
    </div>

    <script>
        window.restaurantMarkersEndpoint = @json($markersEndpoint);
    </script>
</x-layout>
