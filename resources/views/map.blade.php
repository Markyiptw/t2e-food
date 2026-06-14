<x-layout title="Restaurant Map">
    <div class="flex h-screen flex-col">
        {{-- Header: desktop only --}}
        <header class="hidden border-b border-[#1f2a24]/10 bg-[#f4ecd8] md:block">
            <div
                class="mx-auto flex max-w-5xl items-center justify-between px-6 py-4"
            >
                <a
                    href="/"
                    class="text-lg tracking-[0.2em] text-[#0f6b54]"
                    style="font-family: 'Oswald', ui-sans-serif, sans-serif"
                >
                    TIME <span class="text-[#b32b22]">2</span> EAT
                </a>
                <a
                    href="/"
                    class="text-sm tracking-wide text-[#3c4a42]/70 underline"
                >
                    ← Back
                </a>
            </div>
        </header>

        {{-- Map container --}}
        <div class="relative flex-1">
            <div id="map" class="h-full w-full"></div>

            <div
                id="map-loading-overlay"
                class="fixed inset-0 z-[2000] flex items-center justify-center bg-[#1f2a24]/65 px-6 backdrop-blur-sm"
            >
                <div
                    class="max-w-sm border border-[#1f2a24]/15 bg-[#fbf6e9] p-6 text-center shadow-[8px_8px_0_#0f6b54]"
                >
                    <div
                        class="mx-auto mb-4 h-10 w-10 animate-spin rounded-full border-4 border-[#0f6b54]/25 border-t-[#b32b22]"
                        aria-hidden="true"
                    ></div>
                    <p
                        class="text-sm font-bold tracking-[0.18em] text-[#b32b22] uppercase"
                        style="font-family: 'Oswald', ui-sans-serif, sans-serif"
                    >
                        Loading Restaurants
                    </p>
                    <p class="mt-2 text-sm text-[#3c4a42]/75">
                        Plotting the first batch of kitchens on the map.
                    </p>
                </div>
            </div>

            {{-- Desktop filter panel --}}
            <form
                method="GET"
                action="/map"
                class="fixed top-4 right-4 z-[1000] hidden w-80 border border-[#1f2a24]/15 bg-[#fbf6e9] p-5 shadow-[6px_6px_0_#0f6b54] md:block"
            >
                <h2
                    class="mb-4 border-b border-dashed border-[#1f2a24]/20 pb-2 text-sm font-bold tracking-wider text-[#b32b22] uppercase"
                    style="font-family: 'Oswald', ui-sans-serif, sans-serif"
                >
                    Filter by Opening Hours
                </h2>

                <div class="mb-3">
                    <label
                        for="day_of_week_desktop"
                        class="mb-1 block text-xs font-bold text-[#b32b22]"
                    >
                        Day of Week
                    </label>
                    <select
                        id="day_of_week_desktop"
                        name="day_of_week"
                        class="slip-select slip-field w-full px-2 py-1.5 text-sm"
                    >
                        <option selected value="">Any</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label
                        for="start_desktop"
                        class="mb-1 block text-xs font-bold text-[#b32b22]"
                    >
                        Start Time
                    </label>
                    <input
                        id="start_desktop"
                        type="time"
                        name="start"
                        value="{{ $start }}"
                        required
                        class="slip-time slip-field w-full px-2 py-1.5 text-sm"
                    />
                </div>

                <div class="mb-4">
                    <label
                        for="duration_desktop"
                        class="mb-1 block text-xs font-bold text-[#b32b22]"
                    >
                        Duration
                    </label>
                    <div class="flex items-center gap-2">
                        <input
                            id="duration_desktop"
                            type="number"
                            name="duration"
                            value="{{ $duration }}"
                            min="0"
                            step="1"
                            placeholder="0"
                            class="slip-number slip-field w-full px-2 py-1.5 text-sm"
                        />
                        <span class="text-xs text-[#3c4a42]/70">
                            minutes
                        </span>
                    </div>
                </div>

                <button
                    type="submit"
                    class="w-full bg-[#b32b22] py-2.5 text-sm font-bold tracking-[0.15em] text-[#f4ecd8] hover:bg-[#962219]"
                    style="font-family: 'Oswald', ui-sans-serif, sans-serif"
                >
                    FILTER
                </button>

                @if ($start !== null || $duration !== null)
                    <a
                        href="/map"
                        class="mt-2 block w-full border border-[#1f2a24]/20 py-2.5 text-center text-sm font-medium text-[#1f2a24] hover:bg-[#f4ecd8]"
                    >
                        Clear Filters
                    </a>
                @endif
            </form>

            {{-- Mobile filter trigger --}}
            <button
                id="filter-toggle"
                type="button"
                class="fixed bottom-6 right-4 z-[1000] flex h-14 w-14 items-center justify-center rounded-full bg-[#b32b22] text-white shadow-lg hover:bg-[#962219] md:hidden"
                aria-label="Open filters"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-6 w-6"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"
                    />
                </svg>
            </button>

            {{-- Mobile bottom sheet backdrop --}}
            <div
                id="filter-backdrop"
                class="pointer-events-none fixed inset-0 z-[1000] bg-black/30 opacity-0 transition-opacity duration-300 md:hidden"
            ></div>

            {{-- Mobile bottom sheet --}}
            <div
                id="filter-sheet"
                class="fixed inset-x-0 bottom-0 z-[1001] max-h-[70vh] translate-y-full overflow-y-auto rounded-t-2xl bg-[#fbf6e9] p-6 shadow-[0_-8px_32px_rgba(31,42,36,0.2)] transition-transform duration-300 md:hidden"
            >
                {{-- Drag handle --}}
                <div
                    class="mx-auto mb-5 h-1 w-12 rounded-full bg-[#1f2a24]/20"
                ></div>

                <h2
                    class="mb-5 text-center text-sm font-bold tracking-[0.15em] text-[#b32b22] uppercase"
                    style="font-family: 'Oswald', ui-sans-serif, sans-serif"
                >
                    Filter by Opening Hours
                </h2>

                <form method="GET" action="/map">
                    <div
                        class="mb-4 border-b border-dashed border-[#1f2a24]/15 pb-4"
                    >
                        <label
                            for="day_of_week_mobile"
                            class="mb-1.5 block text-xs font-bold text-[#b32b22]"
                        >
                            Day of Week
                        </label>
                        <select
                            id="day_of_week_mobile"
                            name="day_of_week"
                            class="slip-select slip-field w-full px-3 py-2 text-sm"
                        >
                            <option selected value="">Any</option>
                        </select>
                    </div>

                    <div
                        class="mb-4 border-b border-dashed border-[#1f2a24]/15 pb-4"
                    >
                        <label
                            for="start_mobile"
                            class="mb-1.5 block text-xs font-bold text-[#b32b22]"
                        >
                            Start Time
                        </label>
                        <input
                            id="start_mobile"
                            type="time"
                            name="start"
                            value="{{ $start }}"
                            required
                            class="slip-time slip-field w-full px-3 py-2 text-sm"
                        />
                    </div>

                    <div
                        class="mb-5 border-b border-dashed border-[#1f2a24]/15 pb-4"
                    >
                        <label
                            for="duration_mobile"
                            class="mb-1.5 block text-xs font-bold text-[#b32b22]"
                        >
                            Duration
                        </label>
                        <div class="flex items-center gap-3">
                            <input
                                id="duration_mobile"
                                type="number"
                                name="duration"
                                value="{{ $duration }}"
                                min="0"
                                step="1"
                                placeholder="0"
                                class="slip-number slip-field w-full px-3 py-2 text-sm"
                            />
                            <span class="text-sm text-[#3c4a42]/70">
                                minutes
                            </span>
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="w-full bg-[#b32b22] py-3 text-sm font-bold tracking-[0.15em] text-[#f4ecd8] hover:bg-[#962219]"
                        style="font-family: 'Oswald', ui-sans-serif, sans-serif"
                    >
                        FILTER
                    </button>

                    @if ($start !== null || $duration !== null)
                        <a
                            href="/map"
                            class="mt-3 block w-full border border-[#1f2a24]/20 py-3 text-center text-sm font-medium text-[#1f2a24] hover:bg-[#f4ecd8]"
                        >
                            Clear Filters
                        </a>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <script>
        window.restaurantMarkersEndpoint = @json($markersEndpoint);

        (function () {
            const toggle = document.getElementById('filter-toggle');
            const sheet = document.getElementById('filter-sheet');
            const backdrop = document.getElementById('filter-backdrop');

            if (!toggle || !sheet || !backdrop) {
                return;
            }

            function openSheet() {
                backdrop.classList.remove(
                    'opacity-0',
                    'pointer-events-none',
                );
                backdrop.classList.add('opacity-100');
                sheet.classList.remove('translate-y-full');
                sheet.classList.add('translate-y-0');
            }

            function closeSheet() {
                backdrop.classList.remove('opacity-100');
                backdrop.classList.add('opacity-0', 'pointer-events-none');
                sheet.classList.remove('translate-y-0');
                sheet.classList.add('translate-y-full');
            }

            toggle.addEventListener('click', openSheet);
            backdrop.addEventListener('click', closeSheet);

            document.addEventListener('keydown', function (e) {
                if (
                    e.key === 'Escape' &&
                    sheet.classList.contains('translate-y-0')
                ) {
                    closeSheet();
                }
            });
        })();
    </script>
</x-layout>
