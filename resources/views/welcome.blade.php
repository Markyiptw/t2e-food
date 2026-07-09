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
        <section
            class="relative overflow-hidden border-y border-charcoal/10 bg-ember/[0.03]"
        >
            <div
                class="absolute -left-24 -top-24 h-[34rem] w-[34rem] rounded-full bg-ember/[0.08] blur-3xl"
                aria-hidden="true"
            ></div>
            <div class="relative mx-auto max-w-5xl px-6 py-16 md:py-20">
                <div class="grid gap-12 md:grid-cols-2 md:gap-16">
                    <div>
                        <div
                            class="flex h-52 items-center justify-center md:h-56"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 290 200"
                                fill="none"
                                aria-hidden="true"
                                class="h-full w-auto"
                            >
                                {{-- Big driving gear (ember accent) --}}
                                <g
                                    class="gear-spin"
                                    transform="translate(122 96)"
                                >
                                    <rect
                                        x="-11"
                                        y="-92"
                                        width="22"
                                        height="18"
                                        rx="3"
                                        transform="rotate(0)"
                                        fill="#f59e0b"
                                        stroke="#171412"
                                        stroke-width="3"
                                        stroke-linejoin="round"
                                    ></rect>
                                    <rect
                                        x="-11"
                                        y="-92"
                                        width="22"
                                        height="18"
                                        rx="3"
                                        transform="rotate(36)"
                                        fill="#f59e0b"
                                        stroke="#171412"
                                        stroke-width="3"
                                        stroke-linejoin="round"
                                    ></rect>
                                    <rect
                                        x="-11"
                                        y="-92"
                                        width="22"
                                        height="18"
                                        rx="3"
                                        transform="rotate(72)"
                                        fill="#f59e0b"
                                        stroke="#171412"
                                        stroke-width="3"
                                        stroke-linejoin="round"
                                    ></rect>
                                    <rect
                                        x="-11"
                                        y="-92"
                                        width="22"
                                        height="18"
                                        rx="3"
                                        transform="rotate(108)"
                                        fill="#f59e0b"
                                        stroke="#171412"
                                        stroke-width="3"
                                        stroke-linejoin="round"
                                    ></rect>
                                    <rect
                                        x="-11"
                                        y="-92"
                                        width="22"
                                        height="18"
                                        rx="3"
                                        transform="rotate(144)"
                                        fill="#f59e0b"
                                        stroke="#171412"
                                        stroke-width="3"
                                        stroke-linejoin="round"
                                    ></rect>
                                    <rect
                                        x="-11"
                                        y="-92"
                                        width="22"
                                        height="18"
                                        rx="3"
                                        transform="rotate(180)"
                                        fill="#f59e0b"
                                        stroke="#171412"
                                        stroke-width="3"
                                        stroke-linejoin="round"
                                    ></rect>
                                    <rect
                                        x="-11"
                                        y="-92"
                                        width="22"
                                        height="18"
                                        rx="3"
                                        transform="rotate(216)"
                                        fill="#f59e0b"
                                        stroke="#171412"
                                        stroke-width="3"
                                        stroke-linejoin="round"
                                    ></rect>
                                    <rect
                                        x="-11"
                                        y="-92"
                                        width="22"
                                        height="18"
                                        rx="3"
                                        transform="rotate(252)"
                                        fill="#f59e0b"
                                        stroke="#171412"
                                        stroke-width="3"
                                        stroke-linejoin="round"
                                    ></rect>
                                    <rect
                                        x="-11"
                                        y="-92"
                                        width="22"
                                        height="18"
                                        rx="3"
                                        transform="rotate(288)"
                                        fill="#f59e0b"
                                        stroke="#171412"
                                        stroke-width="3"
                                        stroke-linejoin="round"
                                    ></rect>
                                    <rect
                                        x="-11"
                                        y="-92"
                                        width="22"
                                        height="18"
                                        rx="3"
                                        transform="rotate(324)"
                                        fill="#f59e0b"
                                        stroke="#171412"
                                        stroke-width="3"
                                        stroke-linejoin="round"
                                    ></rect>
                                    <circle
                                        cx="0"
                                        cy="0"
                                        r="78"
                                        fill="#f59e0b"
                                        stroke="#171412"
                                        stroke-width="3"
                                    ></circle>
                                    <circle
                                        cx="0"
                                        cy="0"
                                        r="62"
                                        fill="none"
                                        stroke="#171412"
                                        stroke-width="2"
                                        stroke-opacity="0.35"
                                    ></circle>
                                    <circle
                                        cx="0"
                                        cy="0"
                                        r="22"
                                        fill="#faf6f1"
                                        stroke="#171412"
                                        stroke-width="3"
                                    ></circle>
                                    <circle cx="0" cy="0" r="5" fill="#171412"></circle>
                                </g>
                                {{-- Small meshed gear (ink) --}}
                                <g
                                    class="gear-spin-rev"
                                    transform="translate(236 148)"
                                >
                                    <rect
                                        x="-7"
                                        y="-54"
                                        width="14"
                                        height="12"
                                        rx="2"
                                        transform="rotate(0)"
                                        fill="#faf6f1"
                                        stroke="#171412"
                                        stroke-width="3"
                                        stroke-linejoin="round"
                                    ></rect>
                                    <rect
                                        x="-7"
                                        y="-54"
                                        width="14"
                                        height="12"
                                        rx="2"
                                        transform="rotate(45)"
                                        fill="#faf6f1"
                                        stroke="#171412"
                                        stroke-width="3"
                                        stroke-linejoin="round"
                                    ></rect>
                                    <rect
                                        x="-7"
                                        y="-54"
                                        width="14"
                                        height="12"
                                        rx="2"
                                        transform="rotate(90)"
                                        fill="#faf6f1"
                                        stroke="#171412"
                                        stroke-width="3"
                                        stroke-linejoin="round"
                                    ></rect>
                                    <rect
                                        x="-7"
                                        y="-54"
                                        width="14"
                                        height="12"
                                        rx="2"
                                        transform="rotate(135)"
                                        fill="#faf6f1"
                                        stroke="#171412"
                                        stroke-width="3"
                                        stroke-linejoin="round"
                                    ></rect>
                                    <rect
                                        x="-7"
                                        y="-54"
                                        width="14"
                                        height="12"
                                        rx="2"
                                        transform="rotate(180)"
                                        fill="#faf6f1"
                                        stroke="#171412"
                                        stroke-width="3"
                                        stroke-linejoin="round"
                                    ></rect>
                                    <rect
                                        x="-7"
                                        y="-54"
                                        width="14"
                                        height="12"
                                        rx="2"
                                        transform="rotate(225)"
                                        fill="#faf6f1"
                                        stroke="#171412"
                                        stroke-width="3"
                                        stroke-linejoin="round"
                                    ></rect>
                                    <rect
                                        x="-7"
                                        y="-54"
                                        width="14"
                                        height="12"
                                        rx="2"
                                        transform="rotate(270)"
                                        fill="#faf6f1"
                                        stroke="#171412"
                                        stroke-width="3"
                                        stroke-linejoin="round"
                                    ></rect>
                                    <rect
                                        x="-7"
                                        y="-54"
                                        width="14"
                                        height="12"
                                        rx="2"
                                        transform="rotate(315)"
                                        fill="#faf6f1"
                                        stroke="#171412"
                                        stroke-width="3"
                                        stroke-linejoin="round"
                                    ></rect>
                                    <circle
                                        cx="0"
                                        cy="0"
                                        r="42"
                                        fill="#faf6f1"
                                        stroke="#171412"
                                        stroke-width="3"
                                    ></circle>
                                    <circle
                                        cx="0"
                                        cy="0"
                                        r="28"
                                        fill="none"
                                        stroke="#171412"
                                        stroke-width="2"
                                        stroke-opacity="0.35"
                                    ></circle>
                                    <circle
                                        cx="0"
                                        cy="0"
                                        r="11"
                                        fill="#f59e0b"
                                        stroke="#171412"
                                        stroke-width="3"
                                    ></circle>
                                </g>
                                {{-- Small accent dots --}}
                                <circle
                                    cx="40"
                                    cy="60"
                                    r="3"
                                    fill="#f59e0b"
                                    fill-opacity="0.6"
                                ></circle>
                                <circle
                                    cx="60"
                                    cy="170"
                                    r="3"
                                    fill="#171412"
                                    fill-opacity="0.25"
                                ></circle>
                                <circle
                                    cx="265"
                                    cy="55"
                                    r="3"
                                    fill="#171412"
                                    fill-opacity="0.25"
                                ></circle>
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
                            class="flex h-52 items-center justify-center md:h-56"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 260 200"
                                fill="none"
                                aria-hidden="true"
                                class="h-full w-auto"
                            >
                                {{-- Document panel --}}
                                <rect
                                    x="14"
                                    y="14"
                                    width="180"
                                    height="140"
                                    rx="10"
                                    fill="#faf6f1"
                                    stroke="#171412"
                                    stroke-width="3"
                                    stroke-opacity="0.18"
                                ></rect>
                                {{-- Folded CSV corner tab --}}
                                <path
                                    d="M170 14 L194 14 L194 38 Z"
                                    fill="#f59e0b"
                                    fill-opacity="0.18"
                                    stroke="#171412"
                                    stroke-width="3"
                                    stroke-opacity="0.18"
                                    stroke-linejoin="round"
                                ></path>
                                <text
                                    x="150"
                                    y="32"
                                    font-family="monospace"
                                    font-size="13"
                                    font-weight="700"
                                    fill="#d97706"
                                    text-anchor="middle"
                                >CSV</text>
                                {{-- Header row (ember tint) --}}
                                <rect
                                    x="26"
                                    y="28"
                                    width="156"
                                    height="20"
                                    rx="4"
                                    fill="#f59e0b"
                                    fill-opacity="0.14"
                                ></rect>
                                {{-- Column dividers --}}
                                <line
                                    x1="78"
                                    y1="28"
                                    x2="78"
                                    y2="154"
                                    stroke="#171412"
                                    stroke-width="2"
                                    stroke-opacity="0.1"
                                ></line>
                                <line
                                    x1="130"
                                    y1="28"
                                    x2="130"
                                    y2="154"
                                    stroke="#171412"
                                    stroke-width="2"
                                    stroke-opacity="0.1"
                                ></line>
                                {{-- Header cell labels --}}
                                <rect x="34" y="35" width="32" height="6" rx="3" fill="#d97706"></rect>
                                <rect x="84" y="35" width="36" height="6" rx="3" fill="#d97706"></rect>
                                <rect x="136" y="35" width="40" height="6" rx="3" fill="#d97706"></rect>
                                {{-- Skeleton data rows --}}
                                <rect x="34" y="60" width="32" height="6" rx="3" fill="#171412" fill-opacity="0.28"></rect>
                                <rect x="84" y="60" width="30" height="6" rx="3" fill="#171412" fill-opacity="0.22"></rect>
                                <rect x="136" y="60" width="44" height="6" rx="3" fill="#171412" fill-opacity="0.22"></rect>
                                <rect x="34" y="82" width="28" height="6" rx="3" fill="#171412" fill-opacity="0.28"></rect>
                                <rect x="84" y="82" width="38" height="6" rx="3" fill="#171412" fill-opacity="0.22"></rect>
                                <rect x="136" y="82" width="36" height="6" rx="3" fill="#171412" fill-opacity="0.22"></rect>
                                <rect x="34" y="104" width="34" height="6" rx="3" fill="#171412" fill-opacity="0.28"></rect>
                                <rect x="84" y="104" width="28" height="6" rx="3" fill="#171412" fill-opacity="0.22"></rect>
                                <rect x="136" y="104" width="42" height="6" rx="3" fill="#171412" fill-opacity="0.22"></rect>
                                <rect x="34" y="126" width="30" height="6" rx="3" fill="#171412" fill-opacity="0.28"></rect>
                                <rect x="84" y="126" width="34" height="6" rx="3" fill="#171412" fill-opacity="0.22"></rect>
                                <rect x="136" y="126" width="38" height="6" rx="3" fill="#171412" fill-opacity="0.22"></rect>
                                {{-- Download badge --}}
                                <circle
                                    cx="214"
                                    cy="150"
                                    r="40"
                                    fill="#f59e0b"
                                    stroke="#faf6f1"
                                    stroke-width="5"
                                ></circle>
                                <circle
                                    cx="214"
                                    cy="150"
                                    r="40"
                                    fill="none"
                                    stroke="#171412"
                                    stroke-width="3"
                                    stroke-opacity="0.18"
                                ></circle>
                                <g
                                    stroke="#171412"
                                    stroke-width="4"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    fill="none"
                                >
                                    <line x1="214" y1="134" x2="214" y2="166"></line>
                                    <polyline points="202 154 214 166 226 154"></polyline>
                                </g>
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
                            class="mt-5 inline-flex items-center gap-1.5 rounded-full border border-charcoal/15 px-4 py-2 text-sm font-medium text-ink transition-colors hover:border-ember/40 hover:text-ember"
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
