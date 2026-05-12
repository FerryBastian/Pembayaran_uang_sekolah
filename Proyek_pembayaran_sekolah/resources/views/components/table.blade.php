@props([
    'search' => true,
    'searchPlaceholder' => 'Cari data...',
    'empty' => 'Tidak ada data.',
    'pagination' => null,
])

<div
    x-data="{ search: '' }"
    {{ $attributes->merge(['class' => 'overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm']) }}
>
    @if ($search || isset($actions))
        <div class="flex flex-col gap-3 border-b border-slate-200 bg-white p-4 sm:flex-row sm:items-center sm:justify-between">
            @if ($search)
                <div class="relative w-full sm:max-w-sm">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="m21 21-4.3-4.3M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <input
                        type="search"
                        x-model.debounce.200ms="search"
                        class="w-full rounded-lg border-slate-300 pl-9 text-sm focus:border-primary focus:ring-primary"
                        placeholder="{{ $searchPlaceholder }}"
                    >
                </div>
            @endif

            @isset($actions)
                <div class="flex shrink-0 items-center gap-2">
                    {{ $actions }}
                </div>
            @endisset
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            @isset($head)
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">
                    {{ $head }}
                </thead>
            @endisset

            <tbody class="divide-y divide-slate-200 bg-white text-sm text-slate-600">
                {{ $slot }}
            </tbody>
        </table>
    </div>

    @isset($emptyState)
        <div class="border-t border-slate-200">
            {{ $emptyState }}
        </div>
    @endisset

    @if ($pagination)
        <div class="border-t border-slate-200 bg-white px-4 py-3">
            {{ $pagination->links() }}
        </div>
    @elseif (isset($paginationSlot))
        <div class="border-t border-slate-200 bg-white px-4 py-3">
            {{ $paginationSlot }}
        </div>
    @endif
</div>
