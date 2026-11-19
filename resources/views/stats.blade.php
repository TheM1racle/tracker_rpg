@extends('layout')
@section('title', 'Статистика')
@section('content')
<h1 class="mb-4 text-lg font-semibold">Путь героя</h1>

<div class="card">
    <div class="mb-3 text-sm text-slate-400">Последние 12 недель · всего выполнений: {{ $u->total_done }}</div>
    <div class="grid grid-flow-col grid-rows-7 gap-1">
        @foreach ($cells as $c)
            @php $lvl = $c['c'] === 0 ? 0 : ($c['c'] <= 2 ? 1 : ($c['c'] <= 4 ? 2 : 3)); @endphp
            <div class="h-3 w-3 rounded-sm {{ ['bg-white/5','bg-violet-900','bg-violet-600','bg-violet-400'][$lvl] }}" title="{{ $c['d'] }}: {{ $c['c'] }}"></div>
        @endforeach
    </div>
</div>

<h2 class="h2">Достижения</h2>
<div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
    @foreach ($ach as [$icon, $name, $desc, $ok])
        <div class="card text-center {{ $ok ? '' : 'opacity-35' }}">
            <div class="text-2xl">{{ $icon }}</div>
            <div class="mt-1 text-sm font-medium">{{ $name }}</div>
            <div class="text-[11px] text-slate-500">{{ $desc }}</div>
        </div>
    @endforeach
</div>

<h2 class="h2">Стрики</h2>
@foreach ($habits as $h)
    <div class="row mb-2">
        <span class="flex-1">{{ $h->title }}</span>
        <span class="text-sm text-orange-400">🔥 {{ $h->streak }}</span>
        <span class="text-xs text-slate-500">рекорд {{ $h->best_streak }}</span>
    </div>
@endforeach
@endsection
