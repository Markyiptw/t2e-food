<x-layout title="Time 2 Eat — Home">
    <main>

        {{-- Hero --}}
        <section
            class="relative isolate flex min-h-[70vh] items-center overflow-hidden"
        >
            <div
                id="hero-slideshow"
                class="absolute inset-0 -z-20 bg-ink"
                aria-hidden="true"
            ></div>
            <div
                class="absolute inset-0 -z-10 bg-ink/40"
                aria-hidden="true"
            ></div>
            <div
                class="absolute inset-0 -z-[5] bg-gradient-to-b from-ink/60 via-ink/40 to-ink/70 md:hidden"
                aria-hidden="true"
            ></div>

            <div
                class="relative z-0 mx-auto w-full max-w-5xl px-6 py-16 md:px-0 md:py-20"
            >
                <div class="max-w-xl">
                    <div
                        class="md:rounded-2xl md:bg-ink/60 md:p-8 md:backdrop-blur-md"
                    >
                        <h1
                            class="text-3xl font-bold tracking-tight text-warm-white md:text-4xl"
                        >
                            Food? At an hour like this?
                        </h1>
                        <p
                            class="mt-4 text-lg leading-relaxed text-warm-white/90"
                        >
                            Night shifts, late hangs, early mornings — whatever
                            keeps you out, find somewhere in Hong Kong still
                            serving food.
                        </p>

                        <form method="GET" action="/map" class="mt-8">
                            <div
                                class="flex flex-col gap-3 md:flex-row md:flex-wrap md:items-baseline md:gap-x-2 md:gap-y-1.5"
                            >
                                <label
                                    for="start"
                                    class="text-sm font-medium text-warm-white/80"
                                >
                                    I'm eating out at
                                </label>
                                <select
                                    id="start"
                                    name="start"
                                    class="rounded border-charcoal/20 bg-warm-white text-ink shadow-sm focus:border-ember focus:ring-ember"
                                >
                                    <option value="02:00" selected>02:00</option>
                                    <option value="05:00">05:00</option>
                                    <option value="09:00">09:00</option>
                                </select>
                                <label
                                    for="duration"
                                    class="text-sm font-medium text-warm-white/80"
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
                                    class="w-16 rounded border-charcoal/20 bg-warm-white text-ink shadow-sm focus:border-ember focus:ring-ember"
                                />
                                <span class="text-sm text-warm-white/60">
                                    minutes
                                </span>
                            </div>

                            <button
                                type="submit"
                                class="mt-6 inline-flex cursor-pointer items-center gap-2 rounded bg-ember px-6 py-2.5 text-sm font-semibold text-ink hover:bg-ember-dark"
                            >
                                Search on map
                            </button>
                        </form>

                        <p class="mt-6 leading-relaxed text-warm-white/90 md:mt-10">
                            Nothing fancy — no bookings, no reviews, no ads. Just
                            what's open, and when. The most intuitive way to
                            browse restaurants: pinpoint where you'll be, and
                            what's open around that hour.
                            <a
                                href="/map"
                                class="underline hover:text-warm-white"
                            >
                                See for yourself
                            </a>
                            .
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Features --}}
        <section class="border-t border-charcoal/10">
            <div class="mx-auto max-w-5xl px-6 py-10">
                <div class="grid gap-12 md:grid-cols-2">
                    <div>
                        <h2
                            class="text-xl font-bold tracking-tight text-ink"
                        >
                            Up to date
                        </h2>
                        <p class="mt-3 leading-relaxed text-charcoal/70">
                            We do our best to keep hours current — but best to
                            double-check before you head out.
                        </p>
                    </div>
                    <div>
                        <h2
                            class="text-xl font-bold tracking-tight text-ink"
                        >
                            Export
                        </h2>
                        <p class="mt-3 leading-relaxed text-charcoal/70">
                            For the curious (or the spreadsheet-inclined): grab
                            the whole dataset as a CSV.
                        </p>
                        <a
                            href="/export"
                            class="mt-4 inline-block text-sm font-medium text-ink underline hover:text-charcoal"
                        >
                            download all data as CSV
                        </a>
                    </div>
                </div>
            </div>
        </section>

        {{-- Collaborative --}}
        <section class="border-t border-charcoal/10">
            <div class="mx-auto max-w-5xl px-6 py-16">
                <div class="max-w-xl">
                    <h2 class="text-xl font-bold tracking-tight text-ink">
                        A collaborative project
                    </h2>
                    <p class="mt-3 leading-relaxed text-charcoal/70">
                        Built on an idea from Liber Research, this project is
                        open source under the MIT license — contributions
                        welcome.
                    </p>
                    <ul class="mt-4 space-y-2 text-sm">
                        <li>
                            <a
                                href="https://liber-research.com/night_vibe_restaurants/"
                                class="font-medium text-ink underline hover:text-charcoal"
                            >
                                Night Vibe Restaurants
                            </a>
                            <span class="text-charcoal/50">
                                — the original study
                            </span>
                        </li>
                        <li>
                            <a
                                href="https://www.instagram.com/oh.hi.mark.ii"
                                class="font-medium text-ink underline hover:text-charcoal"
                            >
                                Instagram
                            </a>
                            <span class="text-charcoal/50">
                                — stay in touch with the developer
                            </span>
                        </li>
                        <li>
                            <a
                                href="https://github.com/Markyiptw/t2e-food"
                                class="font-medium text-ink underline hover:text-charcoal"
                            >
                                GitHub Repo
                            </a>
                            <span class="text-charcoal/50">
                                — source code, issues, and contributions
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        {{-- Footer --}}
        <footer
            class="mx-auto max-w-5xl px-6 pb-12 text-center text-sm text-charcoal/40"
        >
            TIME 2 EAT
        </footer>
    </main>
</x-layout>
