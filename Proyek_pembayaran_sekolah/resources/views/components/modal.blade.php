@props([
    'name' => 'modal',
    'title' => null,
    'size' => 'md',
])

@php
    $sizes = [
        'sm' => 'max-w-sm',
        'md' => 'max-w-lg',
        'lg' => 'max-w-2xl',
        'xl' => 'max-w-4xl',
        'full' => 'max-w-6xl',
    ];

    $maxWidth = $sizes[$size] ?? $sizes['md'];
@endphp

<div
    x-data="{ open: false }"
    x-on:open-modal.window="if ($event.detail === '{{ $name }}') open = true"
    x-on:close-modal.window="if (!$event.detail || $event.detail === '{{ $name }}') open = false"
    x-on:keydown.escape.window="open = false"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0"
    role="dialog"
    aria-modal="true"
>
    <div
        x-show="open"
        x-transition.opacity
        class="fixed inset-0 bg-slate-950/50"
        x-on:click="open = false"
        aria-hidden="true"
    ></div>

    <div
        x-show="open"
        x-transition
        class="relative mx-auto mt-10 {{ $maxWidth }} overflow-hidden rounded-xl bg-white shadow-xl"
    >
        @if ($title || isset($header))
            <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-6 py-4">
                <div class="min-w-0">
                    @isset($header)
                        {{ $header }}
                    @else
                        <h2 class="text-lg font-bold text-secondary">{{ $title }}</h2>
                    @endisset
                </div>

                <button
                    type="button"
                    class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-600"
                    x-on:click="open = false"
                    aria-label="Tutup modal"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M6 6l12 12M18 6 6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>
            </div>
        @endif

        <div {{ $attributes->merge(['class' => 'px-6 py-5']) }}>
            {{ $slot }}
        </div>

        @isset($footer)
            <div class="flex items-center justify-end gap-2 border-t border-slate-200 bg-slate-50 px-6 py-4">
                {{ $footer }}
            </div>
        @endisset
    </div>
</div>
