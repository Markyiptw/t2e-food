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
        </header>

        {{-- Content --}}
        <section
            class="mx-auto grid max-w-5xl items-center gap-12 px-6 pt-6 pb-12 md:grid-cols-[0.9fr_1.1fr]"
        >
            {{-- Decorative export receipt chit --}}
            <div
                class="relative order-last mx-auto w-full max-w-xs -rotate-1 bg-[#fbf6e9] p-6 shadow-[0_18px_40px_-18px_rgba(31,42,36,0.55)] md:order-first"
                style="
                    clip-path: polygon(
                        0 0,
                        100% 0,
                        100% 97%,
                        96% 100%,
                        92% 97%,
                        88% 100%,
                        84% 97%,
                        80% 100%,
                        76% 97%,
                        72% 100%,
                        68% 97%,
                        64% 100%,
                        60% 97%,
                        56% 100%,
                        52% 97%,
                        48% 100%,
                        44% 97%,
                        40% 100%,
                        36% 97%,
                        32% 100%,
                        28% 97%,
                        24% 100%,
                        20% 97%,
                        16% 100%,
                        12% 97%,
                        8% 100%,
                        4% 97%,
                        0 100%
                    );
                "
            >
                <div class="text-center">
                    <p
                        class="font-hk text-2xl font-black tracking-[0.3em] text-[#b32b22]"
                    >
                        DATA
                    </p>
                </div>
                <div
                    class="my-4 border-t border-dashed border-[#1f2a24]/25"
                ></div>
                <ul class="space-y-3 text-sm">
                    <li class="flex items-start justify-between gap-3">
                        <p class="font-serif text-xs text-[#3c4a42]">Format</p>
                        <span
                            class="mt-0.5 shrink-0 text-xs font-semibold text-[#0f6b54]"
                        >
                            .csv
                        </span>
                    </li>
                    <li class="flex items-start justify-between gap-3">
                        <p class="font-serif text-xs text-[#3c4a42]">Columns</p>
                        <span
                            class="mt-0.5 shrink-0 text-xs font-semibold text-[#0f6b54]"
                        >
                            name · address · hours
                        </span>
                    </li>
                    <li class="flex items-start justify-between gap-3">
                        <p class="font-serif text-xs text-[#3c4a42]">Price</p>
                        <span
                            class="mt-0.5 shrink-0 text-xs font-semibold text-[#0f6b54]"
                        >
                            free
                        </span>
                    </li>
                </ul>
                <div
                    class="my-4 border-t border-dashed border-[#1f2a24]/25"
                ></div>
                <div
                    class="font-display block text-center text-xs tracking-[0.2em] text-[#3c4a42]"
                >
                    多謝 THANK YOU
                </div>
            </div>

            <div class="w-full max-w-md">
                <a
                    href="/map"
                    class="font-display mb-6 inline-flex items-center gap-2 border border-[#0f6b54] bg-[#fbf6e9] px-4 py-2 text-xs tracking-[0.15em] text-[#0f6b54] uppercase shadow-[3px_3px_0_#0f6b54] transition-transform hover:-translate-x-0.5 hover:bg-[#0f6b54] hover:text-[#f4ecd8]"
                >
                    ← Back to map
                </a>

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
                            class="slip-time bg-transparent font-medium text-[#1f2a24] outline-none"
                        />
                    </div>

                    <div class="flex items-center gap-3 px-3 py-2.5 text-sm">
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
                            class="slip-time bg-transparent font-medium text-[#1f2a24] outline-none"
                        />
                    </div>

                    <button
                        type="submit"
                        class="font-display mt-1 flex w-full items-center justify-center gap-2 bg-[#b32b22] py-3 text-lg tracking-[0.2em] text-[#f4ecd8] hover:bg-[#962219]"
                    >
                        DOWNLOAD AS CSV
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
