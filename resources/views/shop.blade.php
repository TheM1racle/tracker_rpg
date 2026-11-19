@extends('layout')
@section('title', 'Магазин')
@section('content')
<h1 class="mb-4 text-lg font-semibold">Лавка · {{ $u->gold }} 🪙</h1>
<div class="grid gap-3 sm:grid-cols-2">
    @foreach ($items as $code => $item)
        <form method="POST" action="{{ route('shop.buy') }}" class="card">@csrf
            <input type="hidden" name="code" value="{{ $code }}">
            <div class="text-2xl">{{ $item['icon'] }}</div>
            <div class="mt-1 font-medium">{{ $item['name'] }}</div>
            <div class="mb-3 text-xs text-slate-400">{{ $item['desc'] }}</div>
            <button class="btn btn-p w-full" @disabled($u->gold < $item['cost'])>{{ $item['cost'] }} 🪙</button>
        </form>
    @endforeach
</div>

<h2 class="h2">Мои реальные награды</h2>
<form method="POST" action="{{ route('rewards.store') }}" class="card mb-3 flex gap-2">@csrf
    <input name="title" class="input" placeholder="Серия сериала" required maxlength="60">
    <input name="cost" type="number" class="input w-28" placeholder="120" required min="1">
    <button class="btn btn-p">+</button>
</form>
@foreach ($rewards as $reward)
    <div class="row mb-2">
        <span class="flex-1">🎁 {{ $reward->title }}</span>
        <form method="POST" action="{{ route('rewards.buy', $reward) }}">@csrf
            <button class="btn btn-p" @disabled($u->gold < $reward->cost)>{{ $reward->cost }} 🪙</button>
        </form>
        <form method="POST" action="{{ route('rewards.destroy', $reward) }}">@csrf @method('DELETE')
            <button class="btn text-slate-500 hover:text-rose-400">✕</button>
        </form>
    </div>
@endforeach
@endsection
