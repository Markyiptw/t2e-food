<x-layout>
    <main class="min-h-screen">
        {{-- Nav --}}
        <header class="mx-auto flex max-w-5xl items-center justify-between px-6 py-6">
            <a href="/" class="text-lg font-bold tracking-wide text-slate-800">
                TIME 2 EAT
            </a>
        </header>

        {{-- Hero --}}
        <section class="mx-auto max-w-5xl px-6 pt-10 pb-16">
            <div class="max-w-xl">
                <h1 class="text-4xl font-bold tracking-tight text-slate-900">
                    Food? At an hour like this?
                </h1>
                <p class="mt-4 text-lg leading-relaxed text-slate-600">
                    Night shifts, late hangs, early mornings — whatever keeps you out, find somewhere in Hong Kong still serving food.
                </p>

                <form method="GET" action="/map" class="mt-8">
                    <div class="flex flex-wrap items-baseline gap-x-2 gap-y-1.5">
                        <label for="start" class="text-sm font-medium text-slate-700">I'm eating out at</label>
                        <select
                            id="start"
                            name="start"
                            class="rounded border-slate-300 text-slate-800 shadow-sm focus:border-slate-500 focus:ring-slate-500"
                        >
                            <option value="02:00" selected>02:00</option>
                            <option value="05:00">05:00</option>
                            <option value="09:00">09:00</option>
                        </select>
                        <label for="duration" class="text-sm font-medium text-slate-700">for</label>
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

                <p class="mt-4">
                    <a href="/export" class="text-sm text-slate-500 underline hover:text-slate-700">
                        ...or download all data as CSV
                    </a>
                </p>
            </div>
        </section>

        {{-- Footer --}}
        <footer class="mx-auto max-w-5xl px-6 pb-12 text-center text-sm text-slate-400">
            TIME 2 EAT
        </footer>
    </main>
</x-layout>
