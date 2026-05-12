@props([
    'items' => [],
])

<nav {{ $attributes->merge(['class' => 'flex items-center gap-2 text-sm text-slate-500']) }} aria-label="Breadcrumb">
    <a href="{{ url('/') }}" class="font-medium text-slate-500 hover:text-primary">
        Home
    </a>

    @foreach ($items as $item)
        @php
            $label = is_array($item) ? ($item['label'] ?? '') : $item;
            $url = is_array($item) ? ($item['url'] ?? null) : null;
            $isLast = $loop->last;
        @endphp

        <svg class="h-4 w-4 shrink-0 text-slate-300" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="m9 6 6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>

        @if ($url && !$isLast)
            <a href="{{ $url }}" class="font-medium text-slate-500 hover:text-primary">
                {{ $label }}
            </a>
        @else
            <span class="font-semibold text-slate-700" aria-current="{{ $isLast ? 'page' : 'false' }}">
                {{ $label }}
            </span>
        @endif
    @endforeach
</nav>
