@props([
    'type' => 'info',
    'title' => null,
    'dismissible' => true,
])

@php
    $styles = [
        'success' => [
            'wrapper' => 'border-success/20 bg-green-50 text-green-800',
            'icon' => 'text-success',
            'button' => 'text-green-700 hover:bg-green-100',
        ],
        'error' => [
            'wrapper' => 'border-danger/20 bg-red-50 text-red-800',
            'icon' => 'text-danger',
            'button' => 'text-red-700 hover:bg-red-100',
        ],
        'danger' => [
            'wrapper' => 'border-danger/20 bg-red-50 text-red-800',
            'icon' => 'text-danger',
            'button' => 'text-red-700 hover:bg-red-100',
        ],
        'warning' => [
            'wrapper' => 'border-warning/20 bg-amber-50 text-amber-800',
            'icon' => 'text-warning',
            'button' => 'text-amber-700 hover:bg-amber-100',
        ],
        'info' => [
            'wrapper' => 'border-primary/20 bg-blue-50 text-blue-800',
            'icon' => 'text-primary',
            'button' => 'text-blue-700 hover:bg-blue-100',
        ],
    ];

    $style = $styles[$type] ?? $styles['info'];
@endphp

<div
    x-data="{ show: true }"
    x-show="show"
    x-transition.opacity.duration.150ms
    {{ $attributes->merge(['class' => "rounded-xl border p-4 {$style['wrapper']}"]) }}
    role="alert"
>
    <div class="flex gap-3">
        <div class="{{ $style['icon'] }} mt-0.5 shrink-0">
            @if ($type === 'success')
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="m5 13 4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            @elseif ($type === 'warning')
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 9v4m0 4h.01M10.3 4.6 2.7 18a2 2 0 0 0 1.7 3h15.2a2 2 0 0 0 1.7-3L13.7 4.6a2 2 0 0 0-3.4 0Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            @elseif ($type === 'error' || $type === 'danger')
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 9v4m0 4h.01M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            @else
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 11v5m0-8h.01M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            @endif
        </div>

        <div class="min-w-0 flex-1 text-sm">
            @if ($title)
                <p class="font-semibold">{{ $title }}</p>
            @endif

            <div class="{{ $title ? 'mt-1' : '' }}">
                {{ $slot }}
            </div>
        </div>

        @if ($dismissible)
            <button
                type="button"
                class="{{ $style['button'] }} -mr-1 -mt-1 rounded-lg p-1 transition"
                @click="show = false"
                aria-label="Tutup alert"
            >
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M6 6l12 12M18 6 6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>
        @endif
    </div>
</div>
