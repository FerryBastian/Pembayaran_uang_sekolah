@php
    $useOld = old('_form') === $form;
    $value = fn ($key, $fallback = '') => $useOld ? old($key) : $fallback;
@endphp

<input type="hidden" name="_form" value="{{ $form }}">
<div>
    <label class="block text-sm font-semibold text-secondary">Judul</label>
    <input name="judul" value="{{ $value('judul', $tagihan?->judul) }}" class="mt-2 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary" required>
</div>
<div>
    <label class="block text-sm font-semibold text-secondary">Deskripsi</label>
    <textarea name="deskripsi" rows="3" class="mt-2 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary">{{ $value('deskripsi', $tagihan?->deskripsi) }}</textarea>
</div>
<div class="grid gap-5 sm:grid-cols-2">
    <div>
        <label class="block text-sm font-semibold text-secondary">Nominal</label>
        <input name="nominal" type="number" min="0" step="1000" value="{{ $value('nominal', $tagihan?->nominal) }}" class="mt-2 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary" required>
    </div>
    <div>
        <label class="block text-sm font-semibold text-secondary">Jatuh Tempo</label>
        <input name="jatuh_tempo" type="date" value="{{ $value('jatuh_tempo') }}" class="mt-2 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary">
    </div>
    <div>
        <label class="block text-sm font-semibold text-secondary">Bulan</label>
        <select name="bulan" class="mt-2 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary" required>
            @foreach ($months as $key => $month)
                <option value="{{ $key }}" @selected((string) $value('bulan', $tagihan?->bulan ?? now()->month) === (string) $key)>{{ $month }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-semibold text-secondary">Tahun</label>
        <select name="tahun" class="mt-2 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary" required>
            @foreach ($years as $year)
                <option value="{{ $year }}" @selected((string) $value('tahun', $tagihan?->tahun ?? now()->year) === (string) $year)>{{ $year }}</option>
            @endforeach
        </select>
    </div>
</div>
