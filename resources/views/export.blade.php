<x-layout title="Export Restaurants">
    <main class="min-h-screen">
        {{-- Nav --}}
        <header class="mx-auto flex max-w-5xl items-center justify-between px-6 py-6">
            <a href="/" class="text-lg font-bold tracking-wide text-slate-800">
                TIME 2 EAT
            </a>
        </header>

        {{-- Content --}}
        <section class="mx-auto max-w-5xl px-6 pt-6 pb-16">
            <a
                href="/map"
                class="mb-6 inline-flex items-center gap-1 text-sm text-slate-500 underline hover:text-slate-700"
            >
                Back to map
            </a>

            <div class="max-w-md">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                    Export restaurants
                </h1>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">
                    Download a CSV of all kitchens open within a chosen time window.
                </p>

                <form
                    action="/export"
                    method="POST"
                    class="mt-6 space-y-4"
                >
                    @csrf

                    <div>
                        <label for="start" class="block text-sm font-medium text-slate-700">
                            Start time
                        </label>
                        <input
                            id="start"
                            type="time"
                            name="start"
                            value="{{ $start }}"
                            class="mt-1 block w-full rounded border-slate-300 text-sm text-slate-800 shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:max-w-xs"
                        />
                    </div>

                    <div>
                        <label for="end" class="block text-sm font-medium text-slate-700">
                            End time
                        </label>
                        <input
                            id="end"
                            type="time"
                            name="end"
                            value="{{ $end }}"
                            class="mt-1 block w-full rounded border-slate-300 text-sm text-slate-800 shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:max-w-xs"
                        />
                    </div>

                    <button
                        type="submit"
                        class="inline-flex cursor-pointer items-center gap-2 rounded bg-slate-800 px-6 py-2.5 text-sm font-semibold text-white hover:bg-slate-700"
                    >
                        Download CSV
                    </button>
                </form>
            </div>
        </section>

        {{-- Footer --}}
        <footer class="mx-auto max-w-5xl px-6 pb-12 text-center text-sm text-slate-400">
            TIME 2 EAT
        </footer>
    </main>
</x-layout>
