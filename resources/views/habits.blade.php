@extends('layout')
@section('title', 'Квесты')
@section('content')
<h1 class="mb-4 text-lg font-semibold">Новый квест</h1>
<form method="POST" action="{{ route('habits.store') }}" class="card space-y-3">@csrf
    @include('habits._form')
    <button class="btn btn-p">Добавить</button>
</form>

<h2 class="h2">Мои квесты ({{ $habits->count() }})</h2>
@foreach ($habits as $h)
    <details class="card mb-2">
        <summary class="flex cursor-pointer items-center gap-2 text-sm">
            <span>{{ $h->type === 'daily' ? '📅' : '🔁' }}</span>
            <span class="flex-1">{{ $h->title }}</span>
            <span class="text-xs text-slate-500">{{ ['', 'лёгкая', 'средняя', 'тяжёлая'][$h->difficulty] }}</span>
        </summary>
        <form method="POST" action="{{ route('habits.update', $h) }}" class="mt-4 space-y-3">@csrf @method('PUT')
            @include('habits._form', ['h' => $h])
            <button class="btn btn-p">Сохранить</button>
        </form>
        <form method="POST" action="{{ route('habits.destroy', $h) }}" class="mt-2">@csrf @method('DELETE')
            <button class="btn text-rose-400 hover:bg-rose-500/10">Удалить</button>
        </form>
    </details>
@endforeach
@endsection
