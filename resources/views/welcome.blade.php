<x-layout>
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

        {{-- Hero --}}
        <section
            class="mx-auto grid max-w-5xl items-center gap-12 px-6 pt-10 pb-16 md:grid-cols-[1.1fr_0.9fr]"
        >
            <div>
                <h1
                    class="mt-3 font-serif text-6xl font-medium tracking-tight italic"
                >
                    Will there be
                    <span class="text-[#b32b22]">food</span>
                    ?
                </h1>
                <p class="mt-6 max-w-md text-lg leading-relaxed text-[#3c4a42]">
                    tell us your eta, and we'll tell you which Hong Kong
                    kitchens are still firing the wok.
                </p>

                {{-- Order slip input --}}
                <form
                    method="GET"
                    action="/map"
                    class="mt-9 max-w-md border border-[#1f2a24]/15 bg-[#fbf6e9] p-2 shadow-[6px_6px_0_#0f6b54]"
                >
                    <div
                        class="flex items-center gap-3 border-b border-dashed border-[#1f2a24]/20 px-3 py-2.5 text-sm"
                    >
                        <label
                            for="start"
                            class="font-hk w-16 font-bold text-[#b32b22]"
                        >
                            Time
                        </label>
                        <select
                            id="start"
                            name="start"
                            class="bg-transparent font-medium text-[#1f2a24] outline-none"
                        >
                            <option value="02:00" selected>02:00</option>
                            <option value="05:00">05:00</option>
                            <option value="09:00">09:00</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-3 px-3 py-2.5 text-sm">
                        <label
                            for="duration"
                            class="font-hk w-16 font-bold text-[#b32b22]"
                        >
                            Buffer
                        </label>
                        <input
                            id="duration"
                            type="number"
                            name="duration"
                            value="15"
                            min="0"
                            step="1"
                            class="w-16 bg-transparent font-medium text-[#1f2a24] outline-none"
                        />
                        <span class="font-medium">minutes</span>
                    </div>
                    <button
                        type="submit"
                        class="font-display mt-1 flex w-full items-center justify-center gap-2 bg-[#b32b22] py-3 text-lg tracking-[0.2em] text-[#f4ecd8] hover:bg-[#962219]"
                    >
                        VIEW ON MAP
                    </button>
                </form>
                <a
                    href="/export"
                    class="mt-3 block text-sm tracking-wide text-[#3c4a42]/70 underline"
                >
                    …or export for all the data nerds →
                </a>
            </div>

            {{-- Menu chit / receipt --}}
            <div
                id="menu"
                class="relative mx-auto w-full max-w-xs rotate-1 bg-[#fbf6e9] p-6 shadow-[0_18px_40px_-18px_rgba(31,42,36,0.55)]"
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
                        MENU
                    </p>
                </div>
                <div
                    class="my-4 border-t border-dashed border-[#1f2a24]/25"
                ></div>
                <ul class="space-y-3 text-sm">
                    <li class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-serif text-xs text-[#3c4a42]">
                                Data Source
                            </p>
                        </div>
                        <span
                            class="mt-0.5 shrink-0 text-xs font-semibold text-[#0f6b54]"
                        >
                            you guess
                        </span>
                    </li>
                    <li class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-serif text-xs text-[#3c4a42]">
                                Usefulness
                            </p>
                        </div>
                        <span
                            class="mt-0.5 shrink-0 text-xs font-semibold text-[#0f6b54]"
                        >
                            quite
                        </span>
                    </li>
                    <li class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-serif text-xs text-[#3c4a42]">
                                Vibe Check
                            </p>
                        </div>
                        <span
                            class="mt-0.5 shrink-0 text-xs font-semibold text-[#0f6b54]"
                        >
                            pass
                        </span>
                    </li>
                    <li class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-serif text-xs text-[#3c4a42]">ADS</p>
                        </div>
                        <span
                            class="mt-0.5 shrink-0 text-xs font-semibold text-[#1f2a24]/35 line-through"
                        >
                            none
                        </span>
                    </li>
                </ul>
                <div
                    class="my-4 border-t border-dashed border-[#1f2a24]/25"
                ></div>
                <div
                    class="font-display block justify-between text-center text-xs tracking-[0.2em] text-[#3c4a42]"
                >
                    多謝 THANK YOU
                </div>
            </div>
        </section>

        {{-- Tile divider --}}
        <div
            class="h-2 w-full bg-[repeating-linear-gradient(90deg,#b32b22_0_14px,#f4ecd8_14px_16px)] opacity-60"
        ></div>

        {{-- Open source band --}}
        <section class="mx-auto my-16 max-w-5xl px-6 pb-20">
            <div
                class="flex flex-col items-start justify-between gap-6 border-2 border-[#0f6b54] bg-[#0f6b54] p-9 text-[#f4ecd8] md:flex-row md:items-center"
            >
                <div>
                    <h2 class="text-2xl">
                        <span class="font-serif italic">
                            A community project
                        </span>
                    </h2>
                    <p class="mt-2 max-w-lg text-sm text-[#f4ecd8]/85">
                        Open source and free. Found a bug? Send a pull request —
                        every fix feeds the whole street.
                    </p>
                </div>
                <div class="flex shrink-0 gap-3">
                    <a
                        href="https://github.com/Markyiptw/t2e-food"
                        class="font-display bg-[#b32b22] px-5 py-3 text-base tracking-[0.15em] hover:bg-[#962219]"
                    >
                        ⭐ GITHUB
                    </a>
                </div>
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
