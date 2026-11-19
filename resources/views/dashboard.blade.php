@extends('layout')
@section('content')
<div class="mb-2 flex items-baseline justify-between">
    <h1 class="text-lg font-semibold">{{ \Illuminate\Support\Carbon::parse($today)->translatedFormat('j F') }}</h1>
    <span class="text-sm text-slate-400">{{ $done }} / {{ $dailies->count() }}</span>
</div>
<div class="bar h-2 overflow-hidden rounded-full bg-black/40">
    <div class="h-full rounded-full bg-amber-400" style="width: {{ $dailies->count() ? round($done / $dailies->count() * 100) : 0 }}%"></div>
</div>

<h2 class="h2">Дейлики</h2>
@forelse ($dailies as $h)
    @php $ok = ($logs->get($h->id)?->count ?? 0) > 0; @endphp
    <form method="POST" action="{{ route('habits.tap', $h) }}" class="mb-2">@csrf
        <button class="row {{ $ok ? 'opacity-50' : '' }}">
            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-md border {{ $ok ? 'border-emerald-500 bg-emerald-500 text-black' : 'border-white/20' }}">{{ $ok ? '✓' : '' }}</span>
            <span class="flex-1 text-left {{ $ok ? 'line-through' : '' }}">{{ $h->title }}</span>
            @if ($h->streak)<span class="text-xs text-orange-400">🔥{{ $h->streak }}</span>@endif
            <span class="text-xs text-slate-500">{{ config('game.xp')[$h->difficulty] }} XP</span>
        </button>
    </form>
@empty
    <p class="text-sm text-slate-500">На сегодня дейликов нет.</p>
@endforelse

<h2 class="h2">Привычки</h2>
@forelse ($flex as $h)
    @php $log = $logs->get($h->id); @endphp
    <div class="row mb-2">
        <form method="POST" action="{{ route('habits.tap', $h) }}">@csrf
            <input type="hidden" name="dir" value="minus">
            <button class="btn bg-rose-600/20 text-rose-300 hover:bg-rose-600/40">−</button>
        </form>
        <div class="flex-1">
            <div>{{ $h->title }}</div>
            <div class="text-xs text-slate-500">сегодня: +{{ $log?->count ?? 0 }} / −{{ $log?->neg ?? 0 }}</div>
        </div>
        <form method="POST" action="{{ route('habits.tap', $h) }}">@csrf
            <button class="btn bg-emerald-600/20 text-emerald-300 hover:bg-emerald-600/40">+</button>
        </form>
    </div>
@empty
    <p class="text-sm text-slate-500">Пусто.</p>
@endforelse

@if ($dailies->isEmpty() && $flex->isEmpty())
    <a href="{{ route('habits.index') }}" class="btn btn-p mt-6 inline-block">Создать первый квест</a>
@endif
@endsection
