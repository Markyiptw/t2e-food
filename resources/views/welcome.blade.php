<x-layout title="{{ __('Time 2 Eat — Home') }}">
    <main>
        {{-- Hero --}}
        <section
            class="relative isolate flex min-h-[70vh] items-center overflow-hidden"
        >
            <div
                id="hero-slideshow"
                class="bg-ink absolute inset-0 -z-20"
                aria-hidden="true"
            ></div>
            <div
                class="bg-ink/40 absolute inset-0 -z-10"
                aria-hidden="true"
            ></div>
            <div
                class="from-ink/60 via-ink/40 to-ink/70 absolute inset-0 -z-[5] bg-gradient-to-b md:hidden"
                aria-hidden="true"
            ></div>

            <div
                class="relative z-0 mx-auto w-full max-w-5xl px-6 py-16 md:px-0 md:py-20"
            >
                <div class="max-w-xl">
                    <div
                        class="md:bg-ink/60 md:rounded-2xl md:p-8 md:backdrop-blur-md"
                    >
                        <h1
                            class="text-warm-white text-3xl font-bold tracking-tight md:text-4xl"
                        >
                            {{ __('Food? At an hour like this?') }}
                        </h1>
                        <p
                            class="text-warm-white/90 mt-4 text-lg leading-relaxed"
                        >
                            {{ __('Night shifts, late hangs, early mornings — whatever keeps you out, find somewhere in Hong Kong still serving food.') }}
                        </p>

                        <form method="GET" action="/map" class="mt-8">
                            <div
                                class="flex flex-col gap-3 md:flex-row md:flex-wrap md:items-baseline md:gap-x-2 md:gap-y-1.5"
                            >
                                <label
                                    for="start"
                                    class="text-warm-white/80 text-sm font-medium"
                                >
                                    {{ __("I'm eating out at") }}
                                </label>
                                <select
                                    id="start"
                                    name="start"
                                    class="border-charcoal/20 bg-warm-white text-ink focus:border-ember focus:ring-ember rounded shadow-sm"
                                >
                                    <option value="02:00" selected>
                                        02:00
                                    </option>
                                    <option value="05:00">05:00</option>
                                    <option value="09:00">09:00</option>
                                </select>
                                <label
                                    for="duration"
                                    class="text-warm-white/80 text-sm font-medium"
                                >
                                    {{ __('for') }}
                                </label>
                                <input
                                    id="duration"
                                    type="number"
                                    name="duration"
                                    value="15"
                                    min="0"
                                    step="1"
                                    class="border-charcoal/20 bg-warm-white text-ink focus:border-ember focus:ring-ember w-16 rounded shadow-sm"
                                />
                                <span class="text-warm-white/60 text-sm">
                                    {{ __('minutes') }}
                                </span>
                            </div>

                            <button
                                type="submit"
                                class="bg-ember text-ink hover:bg-ember-dark mt-6 inline-flex cursor-pointer items-center gap-2 rounded px-6 py-2.5 text-sm font-semibold"
                            >
                                {{ __('Search on map') }}
                            </button>
                        </form>

                        <p
                            class="text-warm-white/90 mt-6 leading-relaxed md:mt-10"
                        >
                            {{ __('Nothing fancy — no bookings, no reviews, no ads. Just what\'s open, and when. The most intuitive way to browse restaurants: pinpoint where you\'ll be, and what\'s open around that hour.') }}
                            <a
                                href="/map"
                                class="hover:text-warm-white underline"
                            >
                                {{ __('See for yourself') }}
                            </a>
                            .
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Divider --}}
        <div class="flex justify-center">
            <div class="bg-ember/30 h-px w-24"></div>
        </div>

        {{-- Features --}}
        <section
            class="border-charcoal/10 bg-ember/[0.03] relative overflow-hidden border-y"
            data-tangram-bg
        >


            <div class="relative mx-auto max-w-5xl px-6 py-16 md:py-20">
                <div
                    x-data="{ shown: true, ready: false }"
                    x-init="
                        const el = $el;
                        const obs = new IntersectionObserver(([entry]) => {
                            if (entry.isIntersecting) {
                                shown = true;
                                obs.unobserve(el);
                            }
                        }, { threshold: 0.1 });
                        if (el.getBoundingClientRect().top > window.innerHeight) {
                            shown = false;
                        }
                        requestAnimationFrame(() => ready = true);
                        obs.observe(el);
                    "
                    :class="[
                        shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8',
                        ready ? 'transition-all duration-700 ease-out' : ''
                    ]"
                    class="will-change-transform"
                >
                    <h2
                        class="text-ink mb-12 text-center text-xl font-bold tracking-tight"
                    >
                        {{ __('We take care of the data') }}
                    </h2>

                    <div class="grid gap-12 md:grid-cols-2 md:gap-16">
                        <div
                            class="bg-warm-white/80 border-charcoal/10 rounded-2xl border p-6 shadow-sm backdrop-blur-sm transition-all hover:shadow-md md:p-8"
                        >
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
                                {{-- Radar sweep --}}
                                <g transform="translate(145 100)">
                                    {{-- Range rings --}}
                                    <circle
                                        cx="0"
                                        cy="0"
                                        r="94"
                                        stroke="#171412"
                                        stroke-width="2"
                                        stroke-opacity="0.12"
                                    ></circle>
                                    <circle
                                        cx="0"
                                        cy="0"
                                        r="70"
                                        stroke="#171412"
                                        stroke-width="2"
                                        stroke-opacity="0.12"
                                    ></circle>
                                    <circle
                                        cx="0"
                                        cy="0"
                                        r="46"
                                        stroke="#171412"
                                        stroke-width="2"
                                        stroke-opacity="0.12"
                                    ></circle>
                                    <circle
                                        cx="0"
                                        cy="0"
                                        r="22"
                                        stroke="#171412"
                                        stroke-width="2"
                                        stroke-opacity="0.12"
                                    ></circle>

                                    {{-- Crosshair axes --}}
                                    <line
                                        x1="-94"
                                        y1="0"
                                        x2="94"
                                        y2="0"
                                        stroke="#171412"
                                        stroke-width="2"
                                        stroke-opacity="0.12"
                                        stroke-linecap="round"
                                    ></line>
                                    <line
                                        x1="0"
                                        y1="-94"
                                        x2="0"
                                        y2="94"
                                        stroke="#171412"
                                        stroke-width="2"
                                        stroke-opacity="0.12"
                                        stroke-linecap="round"
                                    ></line>

                                    {{-- Cardinal ticks --}}
                                    <line
                                        x1="0"
                                        y1="-94"
                                        x2="0"
                                        y2="-86"
                                        stroke="#171412"
                                        stroke-width="3"
                                        stroke-linecap="round"
                                        stroke-opacity="0.25"
                                    ></line>
                                    <line
                                        x1="0"
                                        y1="86"
                                        x2="0"
                                        y2="94"
                                        stroke="#171412"
                                        stroke-width="3"
                                        stroke-linecap="round"
                                        stroke-opacity="0.25"
                                    ></line>
                                    <line
                                        x1="-94"
                                        y1="0"
                                        x2="-86"
                                        y2="0"
                                        stroke="#171412"
                                        stroke-width="3"
                                        stroke-linecap="round"
                                        stroke-opacity="0.25"
                                    ></line>
                                    <line
                                        x1="86"
                                        y1="0"
                                        x2="94"
                                        y2="0"
                                        stroke="#171412"
                                        stroke-width="3"
                                        stroke-linecap="round"
                                        stroke-opacity="0.25"
                                    ></line>

                                    {{-- Blip dots --}}
                                    <circle
                                        cx="52"
                                        cy="-32"
                                        r="4"
                                        fill="#f59e0b"
                                        stroke="#171412"
                                        stroke-width="2"
                                        class="blip-pulse"
                                        style="animation-delay: 0s"
                                    ></circle>
                                    <circle
                                        cx="-28"
                                        cy="58"
                                        r="4"
                                        fill="#f59e0b"
                                        stroke="#171412"
                                        stroke-width="2"
                                        class="blip-pulse"
                                        style="animation-delay: 0.7s"
                                    ></circle>
                                    <circle
                                        cx="68"
                                        cy="48"
                                        r="3"
                                        fill="#f59e0b"
                                        stroke="#171412"
                                        stroke-width="2"
                                        class="blip-pulse"
                                        style="animation-delay: 1.4s"
                                    ></circle>
                                    <circle
                                        cx="-56"
                                        cy="-44"
                                        r="3"
                                        fill="#171412"
                                        fill-opacity="0.25"
                                    ></circle>
                                    <circle
                                        cx="36"
                                        cy="74"
                                        r="2.5"
                                        fill="#171412"
                                        fill-opacity="0.25"
                                    ></circle>

                                    {{-- Sweep wedge --}}
                                    <g class="radar-sweep">
                                        <circle
                                            cx="0"
                                            cy="0"
                                            r="94"
                                            fill="none"
                                        ></circle>
                                        <path
                                            d="M 0 0 L 81.41 -47 A 94 94 0 0 1 81.41 47 Z"
                                            fill="#f59e0b"
                                            fill-opacity="0.2"
                                            stroke="#f59e0b"
                                            stroke-width="2"
                                            stroke-linejoin="round"
                                        ></path>
                                    </g>

                                    {{-- Center hub --}}
                                    <circle
                                        cx="0"
                                        cy="0"
                                        r="5"
                                        fill="#171412"
                                    ></circle>
                                    <circle
                                        cx="0"
                                        cy="0"
                                        r="2"
                                        fill="#f59e0b"
                                    ></circle>
                                </g>

                            </svg>
                        </div>
                        <h2 class="text-ink text-2xl font-bold tracking-tight">
                            {{ __('Up to date') }}
                        </h2>
                        <p class="text-charcoal/70 mt-3 leading-relaxed">
                            {{ __('We do our best to keep hours current — but best to double-check before you head out.') }}
                        </p>
                    </div>
                    <div
                        class="bg-warm-white/80 border-charcoal/10 rounded-2xl border p-6 shadow-sm backdrop-blur-sm transition-all hover:shadow-md md:p-8"
                    >
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
                                >
                                    CSV
                                </text>
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
                                <rect
                                    x="34"
                                    y="35"
                                    width="32"
                                    height="6"
                                    rx="3"
                                    fill="#d97706"
                                ></rect>
                                <rect
                                    x="84"
                                    y="35"
                                    width="36"
                                    height="6"
                                    rx="3"
                                    fill="#d97706"
                                ></rect>
                                <rect
                                    x="136"
                                    y="35"
                                    width="40"
                                    height="6"
                                    rx="3"
                                    fill="#d97706"
                                ></rect>
                                {{-- Skeleton data rows --}}
                                <rect
                                    x="34"
                                    y="60"
                                    width="32"
                                    height="6"
                                    rx="3"
                                    fill="#171412"
                                    fill-opacity="0.28"
                                ></rect>
                                <rect
                                    x="84"
                                    y="60"
                                    width="30"
                                    height="6"
                                    rx="3"
                                    fill="#171412"
                                    fill-opacity="0.22"
                                ></rect>
                                <rect
                                    x="136"
                                    y="60"
                                    width="44"
                                    height="6"
                                    rx="3"
                                    fill="#171412"
                                    fill-opacity="0.22"
                                ></rect>
                                <rect
                                    x="34"
                                    y="82"
                                    width="28"
                                    height="6"
                                    rx="3"
                                    fill="#171412"
                                    fill-opacity="0.28"
                                ></rect>
                                <rect
                                    x="84"
                                    y="82"
                                    width="38"
                                    height="6"
                                    rx="3"
                                    fill="#171412"
                                    fill-opacity="0.22"
                                ></rect>
                                <rect
                                    x="136"
                                    y="82"
                                    width="36"
                                    height="6"
                                    rx="3"
                                    fill="#171412"
                                    fill-opacity="0.22"
                                ></rect>
                                <rect
                                    x="34"
                                    y="104"
                                    width="34"
                                    height="6"
                                    rx="3"
                                    fill="#171412"
                                    fill-opacity="0.28"
                                ></rect>
                                <rect
                                    x="84"
                                    y="104"
                                    width="28"
                                    height="6"
                                    rx="3"
                                    fill="#171412"
                                    fill-opacity="0.22"
                                ></rect>
                                <rect
                                    x="136"
                                    y="104"
                                    width="42"
                                    height="6"
                                    rx="3"
                                    fill="#171412"
                                    fill-opacity="0.22"
                                ></rect>
                                <rect
                                    x="34"
                                    y="126"
                                    width="30"
                                    height="6"
                                    rx="3"
                                    fill="#171412"
                                    fill-opacity="0.28"
                                ></rect>
                                <rect
                                    x="84"
                                    y="126"
                                    width="34"
                                    height="6"
                                    rx="3"
                                    fill="#171412"
                                    fill-opacity="0.22"
                                ></rect>
                                <rect
                                    x="136"
                                    y="126"
                                    width="38"
                                    height="6"
                                    rx="3"
                                    fill="#171412"
                                    fill-opacity="0.22"
                                ></rect>
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
                                    <line
                                        x1="214"
                                        y1="134"
                                        x2="214"
                                        y2="166"
                                    ></line>
                                    <polyline
                                        points="202 154 214 166 226 154"
                                    ></polyline>
                                </g>
                            </svg>
                        </div>
                        <h2 class="text-ink text-2xl font-bold tracking-tight">
                            {{ __('Export') }}
                        </h2>
                        <p class="text-charcoal/70 mt-3 leading-relaxed">
                            {{ __('For the curious (or the spreadsheet-inclined): grab the whole dataset as a CSV.') }}
                        </p>
                        <a
                            href="/export"
                            class="border-charcoal/15 text-ink hover:border-ember/40 hover:text-ember mt-5 inline-flex items-center gap-1.5 rounded-full border px-4 py-2 text-sm font-medium transition-colors"
                        >
                            {{ __('download all data as CSV') }}
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
            </div>
        </section>

        {{-- Collaborative --}}
        <section class="border-charcoal/10 border-t">
            <div class="mx-auto max-w-5xl px-6 py-16 md:py-20">
                <div
                    x-data="{ shown: true, ready: false }"
                    x-init="
                        const el = $el;
                        const obs = new IntersectionObserver(([entry]) => {
                            if (entry.isIntersecting) {
                                shown = true;
                                obs.unobserve(el);
                            }
                        }, { threshold: 0.1 });
                        if (el.getBoundingClientRect().top > window.innerHeight) {
                            shown = false;
                        }
                        requestAnimationFrame(() => ready = true);
                        obs.observe(el);
                    "
                    :class="[
                        shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8',
                        ready ? 'transition-all duration-700 ease-out' : ''
                    ]"
                    class="will-change-transform grid gap-10 md:grid-cols-2 md:gap-16"
                >
                    <div>
                        <div class="flex items-center gap-3">
                            <div class="bg-ember h-2 w-2 rounded-full"></div>
                            <h2 class="text-ink text-2xl font-bold tracking-tight">
                                {{ __('A collaborative project') }}
                            </h2>
                        </div>
                        <p class="text-charcoal/70 mt-3 leading-relaxed">
                            {{ __('Built on an idea from Liber Research, this project is open source under the MIT license — contributions welcome.') }}
                        </p>
                    </div>
                    <ul class="space-y-5 text-sm">
                        <li class="flex items-start gap-3">
                            <div class="text-ember mt-0.5">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="16"
                                    height="16"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="2" y1="12" x2="22" y2="12"></line>
                                    <path
                                        d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"
                                    ></path>
                                </svg>
                            </div>
                            <div>
                                <a
                                    href="https://liber-research.com/night_vibe_restaurants/"
                                    class="group text-ink inline-flex items-center gap-1 font-medium hover:underline"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    {{ __('Night Vibe Restaurants') }}
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
                                        <path
                                            d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"
                                        ></path>
                                        <polyline
                                            points="15 3 21 3 21 9"
                                        ></polyline>
                                        <line
                                            x1="10"
                                            y1="14"
                                            x2="21"
                                            y2="3"
                                        ></line>
                                    </svg>
                                </a>
                                <p class="text-charcoal/50">
                                    {{ __('the original study') }}
                                </p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="text-ember mt-0.5">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="16"
                                    height="16"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <rect
                                        x="2"
                                        y="2"
                                        width="20"
                                        height="20"
                                        rx="5"
                                        ry="5"
                                    ></rect>
                                    <path
                                        d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"
                                    ></path>
                                    <line
                                        x1="17.5"
                                        y1="6.5"
                                        x2="17.51"
                                        y2="6.5"
                                    ></line>
                                </svg>
                            </div>
                            <div>
                                <a
                                    href="https://www.instagram.com/oh.hi.mark.ii"
                                    class="group text-ink inline-flex items-center gap-1 font-medium hover:underline"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    {{ __('Instagram') }}
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
                                        <path
                                            d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"
                                        ></path>
                                        <polyline
                                            points="15 3 21 3 21 9"
                                        ></polyline>
                                        <line
                                            x1="10"
                                            y1="14"
                                            x2="21"
                                            y2="3"
                                        ></line>
                                    </svg>
                                </a>
                                <p class="text-charcoal/50">
                                    {{ __('stay in touch with the developer') }}
                                </p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="text-ember mt-0.5">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="16"
                                    height="16"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <path
                                        d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.53 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"
                                    ></path>
                                </svg>
                            </div>
                            <div>
                                <a
                                    href="https://github.com/Markyiptw/t2e-food"
                                    class="group text-ink inline-flex items-center gap-1 font-medium hover:underline"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    {{ __('GitHub Repo') }}
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
                                        <path
                                            d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"
                                        ></path>
                                        <polyline
                                            points="15 3 21 3 21 9"
                                        ></polyline>
                                        <line
                                            x1="10"
                                            y1="14"
                                            x2="21"
                                            y2="3"
                                        ></line>
                                    </svg>
                                </a>
                                <p class="text-charcoal/50">
                                    {{ __('source code, issues, and contributions') }}
                                </p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        {{-- Footer --}}
        <footer>
            <div class="mx-auto max-w-5xl px-6 py-8 text-center">
                <div
                    x-data="{ shown: true, ready: false }"
                    x-init="
                        const el = $el;
                        const obs = new IntersectionObserver(([entry]) => {
                            if (entry.isIntersecting) {
                                shown = true;
                                obs.unobserve(el);
                            }
                        }, { threshold: 0.1 });
                        if (el.getBoundingClientRect().top > window.innerHeight) {
                            shown = false;
                        }
                        requestAnimationFrame(() => ready = true);
                        obs.observe(el);
                    "
                    :class="[
                        shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8',
                        ready ? 'transition-all duration-700 ease-out' : ''
                    ]"
                    class="will-change-transform"
                >
                    <p class="text-charcoal/40 text-xs">
                    © {{ date('Y') }} Time 2 Eat
                </p>
                <p class="text-charcoal/30 mt-1 text-xs">
                    {{ __('Open source under the MIT License') }}
                </p>
                </div>
            </div>
        </footer>
    </main>
</x-layout>
