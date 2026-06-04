<x-layout>
    <div class="grain-texture flex w-full max-w-5xl flex-col items-center px-6 lg:px-8">
        {{-- Header --}}
        <header class="mb-20 flex w-full items-center justify-between pt-10">
            <a
                href="/"
                class="text-2xl font-extrabold tracking-tight text-midnight-indigo"
            >
                Time 2 Eat
            </a>

            <nav class="flex items-center gap-8 text-sm font-medium">
                <a
                    href="/map"
                    class="text-midnight-indigo/60 transition-colors hover:text-midnight-indigo"
                >
                    Map
                </a>
                <a
                    href="/export"
                    class="text-midnight-indigo/60 transition-colors hover:text-midnight-indigo"
                >
                    Data
                </a>

                @if (Route::has('login'))
                    @auth
                        <a
                            href="{{ url('/dashboard') }}"
                            class="text-midnight-indigo/60 transition-colors hover:text-midnight-indigo"
                        >
                            Dashboard
                        </a>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="text-midnight-indigo/60 transition-colors hover:text-midnight-indigo"
                        >
                            Log in
                        </a>
                        @if (Route::has('register'))
                            <a
                                href="{{ route('register') }}"
                                class="rounded-full border border-midnight-indigo/20 px-5 py-2 text-midnight-indigo transition-all hover:border-midnight-indigo hover:bg-midnight-indigo hover:text-hazy-sand"
                            >
                                Register
                            </a>
                        @endif
                    @endauth
                @endif
            </nav>
        </header>

        {{-- Hero --}}
        <section class="mb-16 w-full text-center">
            <h1 class="mx-auto max-w-2xl text-balance text-5xl font-extrabold leading-[1.1] tracking-tight text-midnight-indigo md:text-6xl">
                What is <em class="not-italic text-sunset-apricot">actually</em> open right now.
            </h1>
        </section>

        {{-- Search --}}
        <section
            class="relative w-full overflow-hidden rounded-3xl border border-midnight-indigo/10 bg-white/60 p-10 shadow-xl shadow-midnight-indigo/5 backdrop-blur-sm md:p-14"
        >
            <div class="absolute -top-24 -right-24 h-48 w-48 rounded-full bg-electric-amber/20 blur-3xl"></div>
            <div class="absolute -bottom-16 -left-16 h-40 w-40 rounded-full bg-sunset-apricot/15 blur-3xl"></div>

            <form
                action="/map"
                method="GET"
                class="relative z-10 flex flex-col items-center gap-8"
            >
                <div
                    class="flex flex-wrap items-center justify-center gap-x-3 gap-y-4 text-xl font-medium text-midnight-indigo md:text-2xl"
                >
                    <span>Food at</span>

                    <select
                        name="start"
                        class="rounded-xl border-2 border-midnight-indigo/15 bg-hazy-sand px-4 py-2.5 font-mono text-lg font-bold text-midnight-indigo transition-all focus:border-sunset-apricot focus:outline-none"
                    >
                        <option value="08:00">08:00</option>
                        <option value="12:00">12:00</option>
                        <option value="20:00">20:00</option>
                        <option value="02:00" selected>02:00</option>
                    </select>

                    <span>within</span>

                    <input
                        type="number"
                        name="duration"
                        value="15"
                        min="0"
                        class="w-24 rounded-xl border-2 border-midnight-indigo/15 bg-hazy-sand px-4 py-2.5 text-center font-mono text-lg font-bold text-midnight-indigo transition-all focus:border-sunset-apricot focus:outline-none"
                    />

                    <span>minutes of closing.</span>
                </div>

                <button
                    type="submit"
                    class="group mt-2 inline-flex items-center gap-3 rounded-full bg-sunset-apricot px-10 py-5 text-lg font-bold text-white shadow-lg shadow-sunset-apricot/30 transition-all hover:-translate-y-0.5 hover:bg-electric-amber hover:shadow-xl hover:shadow-electric-amber/30 active:translate-y-0"
                >
                    <span>Explore the Map</span>
                    <span class="transition-transform group-hover:translate-x-1">&rarr;</span>
                </button>

                <a
                    href="/export"
                    class="text-sm font-medium text-midnight-indigo/50 underline decoration-sunset-apricot/60 underline-offset-4 transition-colors hover:text-midnight-indigo"
                >
                    &hellip;or export for all the data nerds &rarr;
                </a>
            </form>
        </section>

        {{-- Minimal Stats --}}
        <section class="mt-16 flex w-full flex-wrap items-center justify-center gap-8 text-sm font-medium text-midnight-indigo/50">
            <span>33,000+ restaurants</span>
            <span class="hidden h-1 w-1 rounded-full bg-midnight-indigo/30 sm:inline-block"></span>
            <span>No accounts</span>
            <span class="hidden h-1 w-1 rounded-full bg-midnight-indigo/30 sm:inline-block"></span>
            <span>No ads</span>
        </section>

        {{-- Footer --}}
        <footer
            class="mt-20 mb-12 flex w-full items-center justify-between border-t border-midnight-indigo/10 pt-8 text-sm text-midnight-indigo/40"
        >
            <span>&copy; Time 2 Eat</span>
            <a
                href="#"
                class="transition-colors hover:text-midnight-indigo"
            >
                GitHub
            </a>
        </footer>
    </div>
</x-layout>
