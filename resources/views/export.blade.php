<x-layout title="Export Restaurants">
    <div class="grain-texture flex w-full max-w-5xl flex-col px-6 py-10 lg:px-8">
        <header class="mb-12 flex items-center justify-between gap-6">
            <a href="/" class="text-2xl font-extrabold tracking-tight text-midnight-indigo">
                Time 2 Eat
            </a>

            <nav class="flex items-center gap-6 text-sm font-medium">
                <a href="/map" class="text-midnight-indigo/60 transition-colors hover:text-midnight-indigo">
                    Map
                </a>
                <a href="/export" class="text-midnight-indigo transition-colors hover:text-midnight-indigo">
                    Data
                </a>
            </nav>
        </header>

        <main class="grid gap-8 lg:grid-cols-[1.05fr_0.95fr] lg:items-start">
            <section class="rounded-[2rem] border border-midnight-indigo/10 bg-white/65 p-8 shadow-2xl shadow-midnight-indigo/10 backdrop-blur-sm md:p-10">
                <p class="mb-4 font-mono text-xs font-bold uppercase tracking-[0.35em] text-sunset-apricot">
                    XLSX Export
                </p>

                <h1 class="max-w-xl text-balance text-4xl font-extrabold leading-tight tracking-tight text-midnight-indigo md:text-5xl">
                    Restaurant data, ready for spreadsheet people.
                </h1>

                <p class="mt-5 max-w-xl text-lg leading-8 text-midnight-indigo/65">
                    Filter restaurants by any opening overlap, then download a workbook with name, district, address, opening hours, and categories.
                </p>

                <form action="/export" method="GET" class="mt-10 grid gap-5 sm:grid-cols-2">
                    <label class="grid gap-2 text-sm font-bold text-midnight-indigo">
                        Opens after
                        <input
                            type="time"
                            name="start"
                            value="{{ $start }}"
                            class="rounded-2xl border-2 border-midnight-indigo/10 bg-hazy-sand px-4 py-3 font-mono text-lg font-bold text-midnight-indigo outline-none transition focus:border-sunset-apricot"
                        />
                    </label>

                    <label class="grid gap-2 text-sm font-bold text-midnight-indigo">
                        Opens before
                        <input
                            type="time"
                            name="end"
                            value="{{ $end }}"
                            class="rounded-2xl border-2 border-midnight-indigo/10 bg-hazy-sand px-4 py-3 font-mono text-lg font-bold text-midnight-indigo outline-none transition focus:border-sunset-apricot"
                        />
                    </label>

                    <div class="flex flex-wrap gap-3 sm:col-span-2">
                        <button
                            type="submit"
                            class="rounded-full bg-midnight-indigo px-7 py-3 font-bold text-hazy-sand shadow-lg shadow-midnight-indigo/20 transition hover:-translate-y-0.5"
                        >
                            Apply filter
                        </button>

                        <a
                            href="/export"
                            class="rounded-full border border-midnight-indigo/15 px-7 py-3 font-bold text-midnight-indigo/65 transition hover:border-midnight-indigo/40 hover:text-midnight-indigo"
                        >
                            Clear
                        </a>
                    </div>
                </form>
            </section>

            <aside class="rounded-[2rem] bg-midnight-indigo p-8 text-hazy-sand shadow-2xl shadow-midnight-indigo/20 md:p-10">
                <p class="font-mono text-xs font-bold uppercase tracking-[0.35em] text-electric-amber">
                    Current selection
                </p>

                <div class="mt-8 rounded-3xl border border-white/10 bg-white/8 p-6">
                    <div class="text-6xl font-extrabold tracking-tight">
                        {{ number_format($restaurantCount) }}
                    </div>
                    <p class="mt-2 text-sm font-medium text-hazy-sand/60">
                        restaurants will be included
                    </p>
                </div>

                <dl class="mt-8 grid gap-4 text-sm">
                    <div class="flex items-center justify-between gap-4 border-b border-white/10 pb-4">
                        <dt class="text-hazy-sand/55">Window start</dt>
                        <dd class="font-mono font-bold">{{ $start ?? 'Any' }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-4 border-b border-white/10 pb-4">
                        <dt class="text-hazy-sand/55">Window end</dt>
                        <dd class="font-mono font-bold">{{ $end ?? 'Any' }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-4">
                        <dt class="text-hazy-sand/55">Format</dt>
                        <dd class="font-mono font-bold">.xlsx</dd>
                    </div>
                </dl>

                <form action="/export" method="POST" class="mt-10">
                    @csrf

                    <input type="hidden" name="start" value="{{ $start }}" />
                    <input type="hidden" name="end" value="{{ $end }}" />

                    <button
                        type="submit"
                        class="w-full rounded-full bg-sunset-apricot px-8 py-4 text-lg font-extrabold text-white shadow-xl shadow-sunset-apricot/25 transition hover:-translate-y-0.5 hover:bg-electric-amber"
                    >
                        Download as XLSX
                    </button>
                </form>
            </aside>
        </main>
    </div>
</x-layout>
