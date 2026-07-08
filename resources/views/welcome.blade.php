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
        <section class="relative border-t border-charcoal/10 bg-warm-white">
            <div class="relative mx-auto max-w-5xl px-6 py-16 md:py-20">
                <div class="grid gap-16 md:grid-cols-2">
                    <div>
                        <div
                            class="mb-5 inline-flex h-10 w-10 items-center justify-center rounded-full bg-ember/10"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="20"
                                height="20"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                class="text-ember"
                            >
                                <polyline points="23 4 23 10 17 10"></polyline>
                                <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path>
                            </svg>
                        </div>
                        <h2
                            class="text-2xl font-bold tracking-tight text-ink"
                        >
                            Up to date
                        </h2>
                        <p class="mt-3 leading-relaxed text-charcoal/70">
                            We do our best to keep hours current — but best to
                            double-check before you head out.
                        </p>
                    </div>
                    <div>
                        <div
                            class="mb-5 inline-flex h-10 w-10 items-center justify-center rounded-full bg-ember/10"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="20"
                                height="20"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                class="text-ember"
                            >
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="7 10 12 15 17 10"></polyline>
                                <line x1="12" y1="15" x2="12" y2="3"></line>
                            </svg>
                        </div>
                        <h2
                            class="text-2xl font-bold tracking-tight text-ink"
                        >
                            Export
                        </h2>
                        <p class="mt-3 leading-relaxed text-charcoal/70">
                            For the curious (or the spreadsheet-inclined): grab
                            the whole dataset as a CSV.
                        </p>
                        <a
                            href="/export"
                            class="mt-4 inline-flex items-center gap-1.5 text-sm font-medium text-ink hover:underline"
                        >
                            download all data as CSV
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="14"
                                height="14"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        {{-- Collaborative --}}
        <section class="relative overflow-hidden bg-ember/[0.03]">
            <div class="absolute -right-20 -top-20 h-[32rem] w-[32rem] rounded-full bg-ember/[0.10] blur-3xl md:-right-32 md:-top-32 md:h-[40rem] md:w-[40rem]" aria-hidden="true"></div>
            <div class="relative mx-auto max-w-5xl px-6 py-16 md:py-20">
                <div class="grid gap-10 md:grid-cols-2 md:gap-16">
                    <div>
                        <h2 class="text-2xl font-bold tracking-tight text-ink">
                            A collaborative project
                        </h2>
                        <p class="mt-3 leading-relaxed text-charcoal/70">
                            Built on an idea from Liber Research, this project is
                            open source under the MIT license — contributions
                            welcome.
                        </p>
                    </div>
                    <ul class="space-y-5 text-sm">
                        <li class="flex items-start gap-3">
                            <div class="mt-0.5 text-ember">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="2" y1="12" x2="22" y2="12"></line>
                                    <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                                </svg>
                            </div>
                            <div>
                                <a
                                    href="https://liber-research.com/night_vibe_restaurants/"
                                    class="group inline-flex items-center gap-1 font-medium text-ink hover:underline"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    Night Vibe Restaurants
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="12"
                                        height="12"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="text-charcoal/40 group-hover:text-ink"
                                    >
                                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                                        <polyline points="15 3 21 3 21 9"></polyline>
                                        <line x1="10" y1="14" x2="21" y2="3"></line>
                                    </svg>
                                </a>
                                <p class="text-charcoal/50">the original study</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="mt-0.5 text-ember">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                                    <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                                    <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                                </svg>
                            </div>
                            <div>
                                <a
                                    href="https://www.instagram.com/oh.hi.mark.ii"
                                    class="group inline-flex items-center gap-1 font-medium text-ink hover:underline"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    Instagram
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="12"
                                        height="12"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="text-charcoal/40 group-hover:text-ink"
                                    >
                                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                                        <polyline points="15 3 21 3 21 9"></polyline>
                                        <line x1="10" y1="14" x2="21" y2="3"></line>
                                    </svg>
                                </a>
                                <p class="text-charcoal/50">stay in touch with the developer</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="mt-0.5 text-ember">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.53 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"></path>
                                </svg>
                            </div>
                            <div>
                                <a
                                    href="https://github.com/Markyiptw/t2e-food"
                                    class="group inline-flex items-center gap-1 font-medium text-ink hover:underline"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    GitHub Repo
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="12"
                                        height="12"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="text-charcoal/40 group-hover:text-ink"
                                    >
                                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                                        <polyline points="15 3 21 3 21 9"></polyline>
                                        <line x1="10" y1="14" x2="21" y2="3"></line>
                                    </svg>
                                </a>
                                <p class="text-charcoal/50">source code, issues, and contributions</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        {{-- Footer --}}
        <footer>
            <div class="mx-auto max-w-5xl px-6 py-16 text-center">
                <div class="mx-auto mb-5 h-px w-10 bg-ember"></div>
                <p class="text-xs font-bold tracking-[0.2em] uppercase text-charcoal/40">
                    TIME 2 EAT
                </p>
            </div>
        </footer>
    </main>
</x-layout>
