<div
    x-data="{ submitting: false }"
    class="border-charcoal/15 inline-flex items-center rounded-full border p-0.5 text-xs font-medium"
    aria-label="Language"
>
    @if (app()->getLocale() === 'en')
        <span
            class="bg-ember text-ink rounded-full px-2.5 py-1"
            aria-current="true"
        >
            EN
        </span>

        <form
            method="POST"
            action="{{ route('locale.update') }}"
            class="inline"
        >
            @csrf
            <input type="hidden" name="locale" value="zh-HK" />
            <button
                type="submit"
                @click="submitting = true"
                :class="{ 'opacity-50': submitting }"
                class="text-charcoal/60 hover:text-charcoal focus:ring-ember rounded-full px-2.5 py-1 focus:ring-2 focus:outline-none"
            >
                繁
            </button>
        </form>
    @else
        <form
            method="POST"
            action="{{ route('locale.update') }}"
            class="inline"
        >
            @csrf
            <input type="hidden" name="locale" value="en" />
            <button
                type="submit"
                @click="submitting = true"
                :class="{ 'opacity-50': submitting }"
                class="text-charcoal/60 hover:text-charcoal focus:ring-ember rounded-full px-2.5 py-1 focus:ring-2 focus:outline-none"
            >
                EN
            </button>
        </form>

        <span
            class="bg-ember text-ink rounded-full px-2.5 py-1"
            aria-current="true"
        >
            繁
        </span>
    @endif
</div>
