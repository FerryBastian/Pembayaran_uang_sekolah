@props([
    'title',
    'value',
    'description' => null,
    'color' => 'primary',
])

@php
    $colors = [
        'primary' => 'bg-blue-50 text-primary',
        'success' => 'bg-green-50 text-success',
        'warning' => 'bg-amber-50 text-warning',
        'danger' => 'bg-red-50 text-danger',
        'secondary' => 'bg-slate-100 text-secondary',
    ];

    $iconClass = $colors[$color] ?? $colors['primary'];
@endphp

<section {{ $attributes->merge(['class' => 'rounded-xl border border-slate-100 bg-card p-6 shadow-sm']) }}>
    <div class="flex items-start justify-between gap-4">
        <div class="min-w-0">
            <p class="text-sm font-medium text-slate-500">{{ $title }}</p>
            <p class="mt-2 truncate text-2xl font-bold text-secondary">{{ $value }}</p>

            @if ($description)
                <p class="mt-2 text-sm text-slate-500">{{ $description }}</p>
            @endif
        </div>

        <div class="{{ $iconClass }} flex h-11 w-11 shrink-0 items-center justify-center rounded-xl">
            @isset($icon)
                {{ $icon }}
            @else
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M4 19V5m0 14h16M8 16V9m4 7V6m4 10v-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            @endisset
        </div>
    </div>
</section>
