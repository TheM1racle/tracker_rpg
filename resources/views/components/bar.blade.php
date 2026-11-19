@props(['value', 'max', 'color', 'label'])
<div>
    <div class="mb-1 flex justify-between text-[11px] text-slate-400">
        <span>{{ $label }}</span><span>{{ $value }} / {{ $max }}</span>
    </div>
    <div class="bar h-2 overflow-hidden rounded-full bg-black/40">
        <div class="{{ $color }} h-full rounded-full" style="width: {{ $max ? min(100, round($value / $max * 100)) : 0 }}%"></div>
    </div>
</div>
