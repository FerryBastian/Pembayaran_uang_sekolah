@props([
    'title' => 'Data belum tersedia',
    'description' => 'Belum ada data yang dapat ditampilkan saat ini.',
])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center px-6 py-12 text-center']) }}>
    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-blue-50 text-primary">
        @isset($icon)
            {{ $icon }}
        @else
            <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M6 3h9l3 3v15H6V3Zm8 0v4h4M9 13h6M9 17h4M9 9h2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        @endisset
    </div>

    <h3 class="mt-4 text-base font-bold text-secondary">{{ $title }}</h3>
    <p class="mt-2 max-w-md text-sm text-slate-500">{{ $description }}</p>

    @isset($action)
        <div class="mt-5">
            {{ $action }}
        </div>
    @endisset
</div>
