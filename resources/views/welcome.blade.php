<x-layout>
    <header
        class="mb-6 w-full max-w-[335px] text-sm not-has-[nav]:hidden lg:max-w-4xl"
    >
        @if (Route::has('login'))
            <nav class="flex items-center justify-end gap-4">
                @auth
                    <a
                        href="{{ url('/dashboard') }}"
                        class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                    >
                        Dashboard
                    </a>
                @else
                    <a
                        href="{{ route('login') }}"
                        class="inline-block rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#19140035] dark:text-[#EDEDEC] dark:hover:border-[#3E3E3A]"
                    >
                        Log in
                    </a>

                    @if (Route::has('register'))
                        <a
                            href="{{ route('register') }}"
                            class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                        >
                            Register
                        </a>
                    @endif
                @endauth
            </nav>
        @endif
    </header>

    <div
        class="flex w-full max-w-4xl flex-col items-center text-center opacity-100 transition-opacity duration-750 starting:opacity-0"
    >
        <svg
            width="120"
            height="120"
            viewBox="0 0 120 120"
            xmlns="http://www.w3.org/2000/svg"
            class="mb-10 text-[#1b1b18] dark:text-[#EDEDEC]"
        >
            <circle
                cx="60"
                cy="60"
                r="54"
                fill="none"
                stroke="currentColor"
                stroke-width="2.5"
            />
            <line
                x1="60"
                y1="10"
                x2="60"
                y2="20"
                stroke="currentColor"
                stroke-width="2.5"
            />
            <line
                x1="110"
                y1="60"
                x2="100"
                y2="60"
                stroke="currentColor"
                stroke-width="2.5"
            />
            <line
                x1="60"
                y1="110"
                x2="60"
                y2="100"
                stroke="currentColor"
                stroke-width="2.5"
            />
            <line
                x1="10"
                y1="60"
                x2="20"
                y2="60"
                stroke="currentColor"
                stroke-width="2.5"
            />
            <line
                x1="60"
                y1="60"
                x2="60"
                y2="30"
                stroke="currentColor"
                stroke-width="4"
                stroke-linecap="round"
                transform="rotate(60 60 60)"
            />
            <line
                x1="60"
                y1="60"
                x2="60"
                y2="20"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
            />
            <circle
                cx="60"
                cy="60"
                r="3.5"
                fill="currentColor"
            />
        </svg>

        <h1
            class="text-5xl font-black tracking-tight lg:text-7xl"
        >
            Time
            <span class="text-[#F53003] dark:text-[#FF4433]">2</span>
            Eat
        </h1>

        <p
            class="mt-6 max-w-md text-lg leading-relaxed text-[#706f6c] lg:text-xl dark:text-[#A1A09A]"
        >
            Choose your dining window.<br />Find what&rsquo;s open.
        </p>

        <a
            href="/map"
            class="mt-10 inline-block rounded-full bg-[#F53003] px-10 py-4 text-lg font-bold text-white transition-all hover:scale-105 hover:bg-[#d42a02] hover:shadow-lg dark:bg-[#FF4433] dark:hover:bg-[#e63a2a]"
        >
            Explore the Map &rarr;
        </a>

        <div
            class="mt-20 grid w-full grid-cols-1 gap-6 sm:grid-cols-3"
        >
            <div
                class="rounded-lg bg-white p-6 shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:bg-[#161615] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]"
            >
                <div class="mb-3 text-3xl">&#x1F5FA;&#xFE0F;</div>
                <h3 class="mb-2 text-base font-semibold">
                    Interactive Map
                </h3>
                <p
                    class="text-sm leading-relaxed text-[#706f6c] dark:text-[#A1A09A]"
                >
                    Browse restaurant locations across Hong Kong.
                </p>
            </div>

            <div
                class="rounded-lg bg-white p-6 shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:bg-[#161615] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]"
            >
                <div class="mb-3 text-3xl">&#x23F0;</div>
                <h3 class="mb-2 text-base font-semibold">
                    Flexible Time Filters
                </h3>
                <p
                    class="text-sm leading-relaxed text-[#706f6c] dark:text-[#A1A09A]"
                >
                    Pick any dining window &mdash; morning, late night,
                    or weekday afternoon &mdash; and find what&rsquo;s
                    open.
                </p>
            </div>

            <div
                class="rounded-lg bg-white p-6 shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:bg-[#161615] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]"
            >
                <div class="mb-3 text-3xl">&#x1F4CB;</div>
                <h3 class="mb-2 text-base font-semibold">
                    Rich Listings
                </h3>
                <p
                    class="text-sm leading-relaxed text-[#706f6c] dark:text-[#A1A09A]"
                >
                    Addresses, hours, and direct links for every
                    restaurant.
                </p>
            </div>
        </div>

        <p
            class="mt-14 text-center text-xs text-[#A1A09A] dark:text-[#62605b]"
        >
            With gratitude to OpenRice &mdash; Hong Kong&rsquo;s leading
            restaurant platform
        </p>
    </div>
</x-layout>
