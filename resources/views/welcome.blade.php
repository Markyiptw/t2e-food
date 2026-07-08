<x-layout title="Time 2 Eat — Home">
    <main class="min-h-screen">
        {{-- Nav --}}
        <header
            class="mx-auto flex max-w-5xl items-center justify-between px-6 py-6"
        >
            <a href="/" class="text-lg font-bold tracking-wide text-slate-800">
                TIME 2 EAT
            </a>
        </header>

        {{-- Hero --}}
        <section
            class="relative isolate flex min-h-[70vh] items-center overflow-hidden"
        >
            <div
                id="hero-slideshow"
                class="absolute inset-0 -z-10 bg-slate-900"
                aria-hidden="true"
            ></div>
            <div
                class="absolute inset-0 -z-10 bg-gradient-to-br from-slate-900/70 via-slate-900/40 to-transparent"
                aria-hidden="true"
            ></div>

            <div class="mx-auto w-full max-w-5xl px-6 py-20">
                <div class="max-w-xl">
                    <h1 class="text-4xl font-bold tracking-tight text-white">
                        Food? At an hour like this?
                    </h1>
                    <p class="mt-4 text-lg leading-relaxed text-slate-100">
                        Night shifts, late hangs, early mornings — whatever keeps
                        you out, find somewhere in Hong Kong still serving food.
                    </p>

                    <form method="GET" action="/map" class="mt-8">
                        <div
                            class="flex flex-wrap items-baseline gap-x-2 gap-y-1.5"
                        >
                            <label
                                for="start"
                                class="text-sm font-medium text-slate-700"
                            >
                                I'm eating out at
                            </label>
                            <select
                                id="start"
                                name="start"
                                class="rounded border-slate-300 text-slate-800 shadow-sm focus:border-slate-500 focus:ring-slate-500"
                            >
                                <option value="02:00" selected>02:00</option>
                                <option value="05:00">05:00</option>
                                <option value="09:00">09:00</option>
                            </select>
                            <label
                                for="duration"
                                class="text-sm font-medium text-slate-700"
                            >
                                for
                            </label>
                            <input
                                id="duration"
                                type="number"
                                name="duration"
                                value="15"
                                min="0"
                                step="1"
                                class="w-16 rounded border-slate-300 text-slate-800 shadow-sm focus:border-slate-500 focus:ring-slate-500"
                            />
                            <span class="text-sm text-slate-500">minutes</span>
                        </div>

                        <button
                            type="submit"
                            class="mt-6 inline-flex cursor-pointer items-center gap-2 rounded bg-slate-800 px-6 py-2.5 text-sm font-semibold text-white hover:bg-slate-700"
                        >
                            Search on map
                        </button>
                    </form>

                    <p class="mt-10 leading-relaxed text-slate-100">
                        Nothing fancy — no bookings, no reviews, no ads. Just what's
                        open, and when. The most intuitive way to browse
                        restaurants: pinpoint where you'll be, and what's open
                        around that hour.
                        <a
                            href="/map"
                            class="underline hover:text-white"
                        >
                            See for yourself
                        </a>
                        .
                    </p>
                </div>
            </div>
        </section>

        {{-- Features --}}
        <section class="border-t border-slate-200">
            <div class="mx-auto max-w-5xl px-6 py-10">
                <div class="grid gap-12 md:grid-cols-2">
                    <div>
                        <h2
                            class="text-xl font-bold tracking-tight text-slate-900"
                        >
                            Up to date
                        </h2>
                        <p class="mt-3 leading-relaxed text-slate-600">
                            We do our best to keep hours current — but best to
                            double-check before you head out.
                        </p>
                    </div>
                    <div>
                        <h2
                            class="text-xl font-bold tracking-tight text-slate-900"
                        >
                            Export
                        </h2>
                        <p class="mt-3 leading-relaxed text-slate-600">
                            For the curious (or the spreadsheet-inclined): grab
                            the whole dataset as a CSV.
                        </p>
                        <a
                            href="/export"
                            class="mt-4 inline-block text-sm font-medium text-slate-800 underline hover:text-slate-600"
                        >
                            Export data
                        </a>
                    </div>
                </div>
            </div>
        </section>

        {{-- Collaborative --}}
        <section class="border-t border-slate-200">
            <div class="mx-auto max-w-5xl px-6 py-16">
                <div class="max-w-xl">
                    <h2 class="text-xl font-bold tracking-tight text-slate-900">
                        A collaborative project
                    </h2>
                    <p class="mt-3 leading-relaxed text-slate-600">
                        Built on an idea from Liber Research, this project is
                        open source under the MIT license — contributions
                        welcome.
                    </p>
                    <ul class="mt-4 space-y-2 text-sm">
                        <li>
                            <a
                                href="https://liber-research.com/night_vibe_restaurants/"
                                class="font-medium text-slate-800 underline hover:text-slate-600"
                            >
                                Night Vibe Restaurants
                            </a>
                            <span class="text-slate-500">
                                — the original study
                            </span>
                        </li>
                        <li>
                            <a
                                href="https://www.instagram.com/oh.hi.mark.ii"
                                class="font-medium text-slate-800 underline hover:text-slate-600"
                            >
                                Instagram
                            </a>
                            <span class="text-slate-500">
                                — stay in touch with the developer
                            </span>
                        </li>
                        <li>
                            <a
                                href="https://github.com/Markyiptw/t2e-food"
                                class="font-medium text-slate-800 underline hover:text-slate-600"
                            >
                                GitHub Repo
                            </a>
                            <span class="text-slate-500">
                                — source code, issues, and contributions
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        {{-- Footer --}}
        <footer
            class="mx-auto max-w-5xl px-6 pb-12 text-center text-sm text-slate-400"
        >
            TIME 2 EAT
        </footer>
    </main>
</x-layout>
