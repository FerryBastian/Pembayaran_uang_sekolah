@props([
    'status' => 'info',
])

@php
    $normalized = str_replace('-', '_', strtolower((string) $status));

    $styles = [
        'lunas' => 'bg-green-50 text-success ring-success/20',
        'success' => 'bg-green-50 text-success ring-success/20',
        'pending' => 'bg-amber-50 text-warning ring-warning/20',
        'warning' => 'bg-amber-50 text-warning ring-warning/20',
        'belum_bayar' => 'bg-red-50 text-danger ring-danger/20',
        'error' => 'bg-red-50 text-danger ring-danger/20',
        'danger' => 'bg-red-50 text-danger ring-danger/20',
        'sudah_dibaca' => 'bg-slate-100 text-slate-700 ring-slate-200',
        'belum_dibaca' => 'bg-blue-50 text-primary ring-primary/20',
        'admin' => 'bg-blue-50 text-primary ring-primary/20',
        'guru' => 'bg-indigo-50 text-indigo-700 ring-indigo-200',
        'siswa' => 'bg-cyan-50 text-cyan-700 ring-cyan-200',
        'orang_tua' => 'bg-violet-50 text-violet-700 ring-violet-200',
        'info' => 'bg-blue-50 text-primary ring-primary/20',
    ];

    $labels = [
        'belum_bayar' => 'Belum Bayar',
        'pending' => 'Menunggu Verifikasi',
        'gagal' => 'Ditolak',
        'sudah_dibaca' => 'Sudah Dibaca',
        'belum_dibaca' => 'Belum Dibaca',
        'orang_tua' => 'Orang Tua',
    ];

    $class = $styles[$normalized] ?? $styles['info'];
    $label = $slot->isEmpty()
        ? ($labels[$normalized] ?? str($normalized)->replace('_', ' ')->title())
        : $slot;
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {$class}"]) }}>
    {{ $label }}
</span>
