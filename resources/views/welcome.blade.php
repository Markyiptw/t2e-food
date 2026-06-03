<x-layout>
    <div class="flex w-full max-w-3xl flex-col items-center">
        {{-- Header --}}
        <header class="mb-16 flex w-full items-center justify-between pt-8">
            <a
                href="/"
                class="text-xl font-bold tracking-tight text-[#1b1b18] dark:text-[#EDEDEC]"
            >
                Time 2 Eat
            </a>

            <nav class="flex items-center gap-6 text-sm">
                <a
                    href="/map"
                    class="text-[#706f6c] hover:text-[#1b1b18] dark:text-[#A1A09A] dark:hover:text-[#EDEDEC]"
                >
                    Map
                </a>
                <a
                    href="#"
                    class="text-[#706f6c] hover:text-[#1b1b18] dark:text-[#A1A09A] dark:hover:text-[#EDEDEC]"
                >
                    Data
                </a>

                @if (Route::has('login'))
                    @auth
                        <a
                            href="{{ url('/dashboard') }}"
                            class="text-[#706f6c] hover:text-[#1b1b18] dark:text-[#A1A09A] dark:hover:text-[#EDEDEC]"
                        >
                            Dashboard
                        </a>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="text-[#706f6c] hover:text-[#1b1b18] dark:text-[#A1A09A] dark:hover:text-[#EDEDEC]"
                        >
                            Log in
                        </a>
                        @if (Route::has('register'))
                            <a
                                href="{{ route('register') }}"
                                class="text-[#706f6c] hover:text-[#1b1b18] dark:text-[#A1A09A] dark:hover:text-[#EDEDEC]"
                            >
                                Register
                            </a>
                        @endif
                    @endauth
                @endif
            </nav>
        </header>

        {{-- Search Hero --}}
        <section
            class="w-full rounded-sm bg-[#f5f0eb] p-10 text-center dark:bg-[#1a1816]"
        >
            <form
                action="/map"
                method="GET"
                class="flex flex-col items-center gap-6"
            >
                <div
                    class="flex flex-wrap items-center justify-center gap-2 text-lg text-[#1b1b18] lg:text-xl dark:text-[#EDEDEC]"
                >
                    <span>I am looking for food at</span>

                    <select
                        name="start"
                        class="rounded-sm border border-[#d4cfc8] bg-white px-3 py-2 text-base font-medium text-[#1b1b18] focus:border-[#c45b3c] focus:outline-none dark:border-[#3E3E3A] dark:bg-[#161615] dark:text-[#EDEDEC]"
                    >
                        <option value="08:00">08:00</option>
                        <option value="12:00">12:00</option>
                        <option value="20:00">20:00</option>
                        <option value="02:00" selected>02:00</option>
                    </select>

                    <span>with a</span>

                    <input
                        type="number"
                        name="duration"
                        value="15"
                        min="0"
                        class="w-20 rounded-sm border border-[#d4cfc8] bg-white px-3 py-2 text-center text-base font-medium text-[#1b1b18] focus:border-[#c45b3c] focus:outline-none dark:border-[#3E3E3A] dark:bg-[#161615] dark:text-[#EDEDEC]"
                    />

                    <span>minutes window before closing.</span>
                </div>

                <button
                    type="submit"
                    class="mt-2 inline-block rounded-full bg-[#c45b3c] px-10 py-4 text-lg font-bold text-white transition-all hover:bg-[#a84d32]"
                >
                    Explore the Map &rarr;
                </button>

                <a
                    href="#"
                    class="text-sm text-[#706f6c] underline decoration-[#c45b3c] underline-offset-4 hover:text-[#1b1b18] dark:text-[#A1A09A] dark:hover:text-[#EDEDEC]"
                >
                    &hellip;or export for all the data nerds &rarr;
                </a>
            </form>
        </section>

        {{-- Why Time 2 Eat? --}}
        <section class="mt-16 w-full text-left">
            <div class="border-t border-[#e5e0da] py-8 dark:border-[#2d2c2a]">
                <p
                    class="text-lg leading-relaxed text-[#1b1b18] dark:text-[#EDEDEC]"
                >
                    Most apps show you what&rsquo;s nearby. We show you
                    what&rsquo;s actually open.
                </p>
            </div>

            <div class="border-t border-[#e5e0da] py-8 dark:border-[#2d2c2a]">
                <p
                    class="text-lg leading-relaxed text-[#1b1b18] dark:text-[#EDEDEC]"
                >
                    Real coverage, updated regularly. 33,000+ restaurants across
                    Hong Kong.
                </p>
            </div>

            <div class="border-t border-[#e5e0da] py-8 dark:border-[#2d2c2a]">
                <p
                    class="text-lg leading-relaxed text-[#1b1b18] dark:text-[#EDEDEC]"
                >
                    No accounts, no ads, no tracking. Just a tool that works.
                </p>
            </div>

            <div class="border-t border-[#e5e0da] py-8 dark:border-[#2d2c2a]">
                <p
                    class="text-lg leading-relaxed text-[#1b1b18] dark:text-[#EDEDEC]"
                >
                    For researchers, too. Export clean, structured data for your
                    own projects.
                </p>
            </div>
        </section>

        {{-- Footer --}}
        <footer
            class="mt-16 w-full border-t border-[#e5e0da] pt-6 pb-8 text-center text-sm text-[#A1A09A] dark:border-[#2d2c2a] dark:text-[#62605b]"
        >
            &copy; Time 2 Eat &middot;
            <a href="#" class="hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]">
                GitHub
            </a>
        </footer>
    </div>
</x-layout>
