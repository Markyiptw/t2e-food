<x-layout title="{{ __('Time 2 Eat — Map') }}" description="{{ __('Interactive map of Hong Kong restaurants. Filter by start time and meal duration to find what\'s open when you need it.') }}">
    <div class="flex flex-1 flex-col">
        {{-- Map attribution --}}
        <div class="flex-none border-b border-slate-200">
            <div class="mx-auto max-w-5xl px-6 py-2 text-xs text-slate-400">
                © OpenStreetMap contributors © CARTO
            </div>
        </div>

        {{-- Map container --}}
        <div class="relative flex-1 overflow-hidden">
            <div id="map" class="h-full w-full"></div>

            {{-- Expand tab (visible when form is hidden) --}}
            <button
                id="filter-expand"
                type="button"
                class="absolute top-0 right-4 z-[1000] hidden cursor-pointer rounded-b border border-t-0 border-slate-200 bg-white px-3 py-1 text-slate-500 shadow-sm hover:bg-slate-50"
                aria-label="{{ __('Show filter') }}"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </button>

            {{-- Filter panel --}}
            <form
                id="filter-form"
                method="GET"
                action="/map"
                class="absolute top-0 right-4 z-[1000] w-72 rounded-b border border-t-0 border-slate-200 bg-white shadow-sm transition-transform duration-300 ease-in-out"
            >
                <div class="flex items-center justify-between border-b border-slate-100 px-4 py-2">
                    <h2 class="text-sm font-semibold text-slate-700">
                        {{ __('Filter') }}
                    </h2>
                    <button
                        id="filter-collapse"
                        type="button"
                        class="cursor-pointer rounded p-0.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600"
                        aria-label="{{ __('Hide filter') }}"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="18 15 12 9 6 15"></polyline>
                        </svg>
                    </button>
                </div>

                <div class="p-4">

                <div class="mb-3">
                    <label for="start" class="mb-1 block text-xs font-medium text-slate-600">
                        {{ __('Start time') }}
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
                        {{ __('Meal duration (min)') }}
                    </label>
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
                </div>

                <div class="flex gap-2">
                    <button
                        type="submit"
                        class="flex-1 cursor-pointer rounded bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700"
                    >
                        {{ __('Update') }}
                    </button>

                    @if ($start !== null || $duration !== null)
                        <a
                            href="/map"
                            class="flex-1 rounded border border-slate-200 py-2 text-center text-sm text-slate-600 hover:bg-slate-50"
                        >
                            {{ __('Clear') }}
                        </a>
                    @endif
                </div>

                <div id="marker-count" class="mt-3 text-xs text-slate-500">
                    {{ __('Loading restaurants...') }}
                </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        window.restaurantMarkersEndpoint = @json($markersEndpoint);
    </script>
</x-layout>
