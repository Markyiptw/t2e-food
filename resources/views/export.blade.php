<x-layout title="Export Restaurants">
    <main
        class="font-body min-h-screen bg-[#f4ecd8] text-[#1f2a24] antialiased"
    >
        {{-- Tile strip --}}
        <div
            class="h-3 w-full bg-[repeating-linear-gradient(90deg,#0f6b54_0_22px,#0c5644_22px_24px)]"
        ></div>

        {{-- Nav --}}
        <header
            class="mx-auto flex max-w-5xl items-center justify-between px-6 py-6"
        >
            <div class="flex items-baseline gap-3">
                <a
                    href="/"
                    class="text-xl tracking-[0.25em] text-[#0f6b54]"
                    style="font-family: 'Oswald', ui-sans-serif, sans-serif"
                >
                    TIME
                    <span class="text-[#b32b22]">2</span>
                    EAT
                </a>
            </div>
            <a
                href="/map"
                class="text-sm tracking-wide text-[#3c4a42]/70 underline"
            >
                ← Back to map
            </a>
        </header>

        {{-- Content --}}
        <section
            class="mx-auto grid max-w-5xl place-items-center px-6 pt-10 pb-16"
        >
            <div class="w-full max-w-md">
                <h1
                    class="mb-2 font-serif text-3xl font-medium tracking-tight italic"
                >
                    Export
                    <span class="text-[#b32b22]">restaurants</span>
                </h1>
                <p class="mb-8 text-sm leading-relaxed text-[#3c4a42]">
                    Pick a time window and download every matching kitchen as a
                    spreadsheet.
                </p>

                {{-- Order slip form --}}
                <form
                    action="/export"
                    method="POST"
                    class="border border-[#1f2a24]/15 bg-[#fbf6e9] p-2 shadow-[6px_6px_0_#0f6b54]"
                >
                    @csrf

                    <div
                        class="flex items-center gap-3 border-b border-dashed border-[#1f2a24]/20 px-3 py-2.5 text-sm"
                    >
                        <label
                            for="start"
                            class="font-hk w-16 font-bold text-[#b32b22]"
                        >
                            Start
                        </label>
                        <input
                            id="start"
                            type="time"
                            name="start"
                            value="{{ $start }}"
                            class="bg-transparent font-medium text-[#1f2a24] outline-none"
                        />
                    </div>

                    <div
                        class="flex items-center gap-3 px-3 py-2.5 text-sm"
                    >
                        <label
                            for="end"
                            class="font-hk w-16 font-bold text-[#b32b22]"
                        >
                            End
                        </label>
                        <input
                            id="end"
                            type="time"
                            name="end"
                            value="{{ $end }}"
                            class="bg-transparent font-medium text-[#1f2a24] outline-none"
                        />
                    </div>

                    <button
                        type="submit"
                        class="font-display mt-1 flex w-full items-center justify-center gap-2 bg-[#b32b22] py-3 text-lg tracking-[0.2em] text-[#f4ecd8] hover:bg-[#962219]"
                    >
                        DOWNLOAD AS XLSX
                    </button>
                </form>
            </div>
        </section>

        {{-- Footer --}}
        <footer
            class="font-display mx-auto max-w-5xl px-6 pb-28 text-center text-sm tracking-[0.2em] text-[#3c4a42]"
        >
            TIME 2 EAT · website by
            <a href="https://instagram.com/oh.hi.mark.ii">
                <span class="underline">oh.himark.ii</span>
            </a>
            · research method by
            <a href="https://instagram.com/liberresearch">
                <span class="underline">liberresearch</span>
            </a>
        </footer>
    </main>
</x-layout>
