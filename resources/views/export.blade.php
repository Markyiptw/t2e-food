<x-layout title="Export Restaurants">
    <main class="grid min-h-screen place-items-center bg-[#f4ecd8] px-6 text-[#1f2a24] antialiased">
        <form action="/export" method="POST" class="w-full max-w-sm space-y-4">
            @csrf

            <label class="block space-y-2 text-sm font-bold tracking-wide">
                <span>Start</span>
                <input
                    type="time"
                    name="start"
                    value="{{ $start }}"
                    class="w-full border-2 border-[#1f2a24] bg-[#fbf6e9] px-4 py-3 text-base font-medium outline-none focus:ring-4 focus:ring-[#b32b22]/30"
                />
            </label>

            <label class="block space-y-2 text-sm font-bold tracking-wide">
                <span>End</span>
                <input
                    type="time"
                    name="end"
                    value="{{ $end }}"
                    class="w-full border-2 border-[#1f2a24] bg-[#fbf6e9] px-4 py-3 text-base font-medium outline-none focus:ring-4 focus:ring-[#b32b22]/30"
                />
            </label>

            <button
                type="submit"
                class="w-full border-2 border-[#1f2a24] bg-[#1f2a24] px-8 py-4 text-sm font-bold tracking-[0.22em] text-[#f4ecd8] uppercase shadow-[6px_6px_0_#b32b22] transition hover:-translate-y-0.5 hover:shadow-[8px_8px_0_#b32b22] focus:outline-none focus:ring-4 focus:ring-[#b32b22]/30"
            >
                Download as XLSX
            </button>
        </form>
    </main>
</x-layout>
